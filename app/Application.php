<?php

namespace App;

class Application
{
    public static function version($format = 'full')
    {

        $version = new \Codiksh\Version\Package\Version();
        if (isset($format)) {
            return $version->format($format);
        } else {
            return $version->format();
        }
    }

    public static function name()
    {
        return env('APP_NAME');
    }
}
