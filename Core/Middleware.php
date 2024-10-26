<?php

namespace Core;

use Core\Details\HttpContext;

interface Middleware {
    public function handle(HttpContext $context, MiddlewareFunc $middlewareFunc);
}
