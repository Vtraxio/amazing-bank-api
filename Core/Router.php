<?php

namespace Core;

use Closure;
use Core\Details\HttpContext;
use Core\Details\HttpParams;
use Core\Details\HttpRequest;
use Exception;
use LogicException;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;

/**
 * Main router logic
 */
class Router {
    /**
     * @var array List of routes
     */
    private array $routes = [];

    /**
     * Add a route
     * @param $method string http method
     * @param $uri string uri
     * @param $executor array|Closure function to execute
     * @return $this Router
     */
    public function add(string $method, string $uri, array|Closure $executor): static {
        $this->routes[] = [
            'method' => $method,
            'uri' => trim($uri, '/'),
            'executor' => $executor,
            'matchers' => [],
            'middlewares' => [],
        ];

        return $this;
    }

    public function get($uri, $executor): static {
        return $this->add('GET', $uri, $executor);
    }

    public function post($uri, $executor): static {
        return $this->add('POST', $uri, $executor);
    }

    public function delete($uri, $executor): static {
        return $this->add('DELETE', $uri, $executor);
    }

    public function patch($uri, $executor): static {
        return $this->add('PATCH', $uri, $executor);
    }

    public function put($uri, $executor): static {
        return $this->add('PUT', $uri, $executor);
    }

    public function where($identifier, $matcher): static {
        $this->routes[array_key_last($this->routes)]['matchers'][$identifier] = $matcher;

        return $this;
    }

    public function use($middleware): static {
        $this->routes[array_key_last($this->routes)]['middlewares'][] = $middleware;

        return $this;
    }

    /**
     * Finds the requested route and calls {@link handle_route}
     * @throws HttpException|ReflectionException
     */
    public function route($baseurl, $uri, $method): void {
        // User-wpisane przez użytkownika, gdy korzysta z api.
        // Registered-zdefiniowane przez dewelopera podczas tworzenia api.

        // Usuń $baseurl z uri i upewnij się, że nie ma ukośników na końcach
        $uri = trim($uri, '/');
        $uri = substr($uri, strlen(trim($baseurl, '/')));
        $uri = trim($uri, '/');

        // Dojebany edge case
        if ($uri == '/')
            $uri = '';

        $userUriSegments = explode('/', $uri);

        foreach ($this->routes as $registeredRoute) {
            $registeredUriSegments = explode('/', $registeredRoute['uri']);

            // Jeśli metoda się nie zgadza, próóbujemy następny uri.
            if ($registeredRoute['method'] != $method) {
                continue;
            }

            // Jeśli liczba segmentów wogóle się nie zgadza, próbujemy następny uri.
            if (count($userUriSegments) != count($registeredUriSegments)) {
                continue;
            }

            $params = [];

            foreach ($registeredUriSegments as $key => $registeredUriSegment) {
                $userUriSegment = $userUriSegments[$key];

                // Niewiadoma
                if (str_starts_with($registeredUriSegment, ':')) {
                    $param = ltrim($registeredUriSegment, ':');

                    // Regex jest ustawiony i nie zgadza się, próbujemy kolejne uri.
                    if (isset($registeredRoute['matchers'][$param]) && !preg_match($registeredRoute['matchers'][$param], $userUriSegment)) {
                        continue 2;
                    }

                    $params[$param] = $userUriSegment;
                    continue;
                }

                // Jeśli się wogóle nie zgadza, próbujemy kolejne uri.
                if ($registeredUriSegment != $userUriSegment) {
                    continue 2;
                }
            }

            // Jeśli dojdziemy tu, to oznacza, że znaleźliśmy odpowiedni wpis.
            $data = $this->handle_route($params, $registeredRoute);


            header("Content-Type: application/json");
            if ($data)
                echo json_encode($data);

            return;
        }

        // Nic nie zostało znalezione.
        throw new HttpException(HttpStatusCode::NOT_FOUND);
    }

    /**
     * Process the selected route
     * @throws ReflectionException
     * @throws Exception
     */
    private function handle_route(array $routeParams, array $route): mixed {
        $request = new HttpRequest($routeParams);
        $params = new HttpParams($routeParams);
        $context = new HttpContext($request, $params);

        $executor = $route['executor'];
        $function = ReflectorUtils::getReflectionFunction($executor);

        $reflectionData = [$context, $request, $params];

        foreach ($route['middlewares'] as $middleware) {
            $class = App::container()->newClass($middleware);
            $class->handle($context);
            $reflectionData = array_merge($reflectionData, $class->available);
        }

        $funcParams = [];

        foreach ($function->getParameters() as $parameter) {
            $attributes = $parameter->getAttributes(Json::class);

            if (count($attributes) > 0) {
                // Construct same class as the parameter

                $class = $parameter->getType()->getName();
                $reflectionClass = new ReflectionClass($class);
                try {
                    $classInstance = $reflectionClass->newInstance($request->body());
                    $funcParams[] = $classInstance;
                } catch (Exception) {
                    throw new HttpException(HttpStatusCode::BAD_REQUEST, "Invalid JSON body");
                }
                continue;
            }

            $parameterType = $parameter->getType()->getName();
            $funcParam = current(array_filter($reflectionData, function ($pm) use ($parameterType) {
                return get_class($pm) == $parameterType;
            }));
            if (!$funcParam) {
                throw new ReflectionException("Route '{$route['uri']}' attempted to get a value of type '{$parameterType}' however it is not available in the reflection pool, are the proper middleware included?");
            }
            $funcParams[] = $funcParam;
        }

        if ($function instanceof ReflectionFunction) {
            return $function->invokeArgs($funcParams);
        } elseif ($function instanceof ReflectionMethod) {
            $class = App::container()->newClass($function->getDeclaringClass()->getName());
            return $function->invokeArgs($class, $funcParams);
        }
        throw new LogicException("This code should never be reached.");
    }
}
