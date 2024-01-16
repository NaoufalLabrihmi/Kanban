<?php

class Session
{
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public function hasGet($key)
    {
        return isset($_SESSION[$key]);
    }

    public function unset($key)
    {
        unset($_SESSION[$key]);
    }

    public function destroy()
    {
        session_destroy();
    }

    public function start()
    {
        session_start();
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
}
?>
