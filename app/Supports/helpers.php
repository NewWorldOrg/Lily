<?php

if (! function_exists('me')) {
    /**
     * Function return current user who logged in
     *
     * @param string $key
     * @return mixed|null
     */
    function me($key = null)
    {
        if (\Auth::guard('admin')->check()) {
            return $key ? \Auth::guard('admin')->user()->getAttribute($key) : \Auth::guard('admin')->user();
        }

        return null;
    }
}

