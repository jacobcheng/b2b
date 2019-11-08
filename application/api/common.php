<?php


if (!function_exists('getController')) {

    function getController()
    {
        $controller = explode('.', request()->controller());
        if (isset($controller[1])){
            return $controller[1];
        } else {
            return;
        }
    }
}
