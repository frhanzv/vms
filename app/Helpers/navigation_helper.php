<?php

if (! function_exists('app_route_path')) {
    /**
     * URI path relative to the app root (strips subdirectory prefix e.g. /vms/).
     */
    function app_route_path(?string $path = null): string
    {
        $path = trim($path ?? service('uri')->getPath(), '/');
        $prefix = trim(parse_url(base_url(), PHP_URL_PATH) ?: '', '/');

        if ($prefix === '') {
            return $path;
        }

        if ($path === $prefix) {
            return '';
        }

        if (str_starts_with($path, $prefix . '/')) {
            return substr($path, strlen($prefix) + 1);
        }

        return $path;
    }
}
