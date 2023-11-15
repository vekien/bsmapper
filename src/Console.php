<?php

namespace App\Beatsaber;

class Console
{
    public static function write(string $message)
    {
        echo(" {$message} \n");
    }

    public static function space()
    {
        self::write("");
    }
}