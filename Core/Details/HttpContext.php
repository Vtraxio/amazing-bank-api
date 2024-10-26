<?php

namespace Core\Details;

readonly class HttpContext {
    public function __construct(public HttpRequest $request, public HttpParams $params) {
    }
}