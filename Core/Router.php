<?php

namespace Core;

use Closure;

class Router {
    private array $routes = [];

    public function add($method, $uri, $executor): static {
        $this->routes[] = [
            'method' => $method,
            'uri' => trim($uri, '/'),
            'executor' => $executor,
            'matchers' => [],
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

    /**
     * @throws HttpException
     */
    public function route($baseurl, $uri, $method): void {
        // User-wpisane przez użytkownika, gdy korzysta z api.
        // Registered-zdefiniowane przez dewelopera podczas tworzenia api.

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

            // Odróżnianie funkcji anonimowej od kontrolera
            $request = new Request($params);
            $executor = $registeredRoute['executor'];
            $data = null;
            if ($executor instanceof Closure) {
                $data = $executor($request);
            } elseif (is_array($executor)) {
                [$controller, $method] = $executor;
                $class = App::container()->newClass($controller);
                $data = $class->$method($request);
            }

            header("Content-Type: application/json");
            if ($data)
                echo json_encode($data);

            return;
        }

        // Nic nie zostało znalezione.
        throw new HttpException(HttpStatusCode::NOT_FOUND);
    }
}
