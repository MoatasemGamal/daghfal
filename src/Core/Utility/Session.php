<?php

namespace Core\Utility;

class Session
{
    /**
     * start session if not started
     * @return void
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
