<?php

// Helper functions which can be used throughout the application.

if (!function_exists('getConfig')) {
    /**
     * Fetch values from config file.
     *
     * @param string $config_key    Key corresponding to the key on config file
     * @return mixed
     */
    function getConfig($config_key)
    {
        $config = include '../config/config.php';

        return isset($config[$config_key]) ? $config[$config_key] : false;
    }
}

if (!function_exists('generateCSRFToken')) {
    /**
     * Generate CSRF Token. Returns same token if already generated for the current session.
     *
     * @return string
     */
    function generateCSRFToken()
    {
        if (isset($_SESSION['token'])) {
            return $_SESSION['token'];
        }
        $_SESSION['token'] = md5(uniqid());

        return $_SESSION['token'];
    }
}

if (!function_exists('sanitizeText')) {
    /**
     * Sanitize incoming requests to prevent HTML injection and other attacks.
     *
     * @param string $text    Text to be sanitized
     * @return string
     */
    function sanitizeText($text)
    {
        if ($text || $text != '') {
            return htmlentities(trim($text), ENT_NOQUOTES, 'UTF-8');
        }
        return null;
    }
}

if (!function_exists('sanitizeArray')) {
    /**
     * Sanitize arrays and objects recursively.
     *
     * @param array|object $data    Array to be sanitized
     * @return mixed
     */
    function sanitizeArray($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {

                if (is_array($value) || is_object($value)) {
                    $data[$key] = sanitizeArray($value);
                } else {
                    $data[$key] = sanitizeText($value);
                }

            }
        } else if (is_object($data)) {
            foreach ($data as $key => $value) {

                if (is_array($value) || is_object($value)) {
                    $data->$key = sanitizeArray($value);
                } else {
                    $data->$key = sanitizeText($value);
                }

            }
        }

        return $data;
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect application to a url.
     *
     * @param  string  $to
     * @param  int     $http_status
     * @return void
     */
    function redirect($to = '/', $http_status = 302)
    {
        header('Location: ' . $to, true, $http_status);
    }
}

if (!function_exists('session_get')) {
    /**
     * Get value from $_SESSION.
     *
     * @param string $key
     * @return mixed
     */
    function session_get($key, $default = false)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }
}

if (!function_exists('session_set')) {
    /**
     * Set value to $_SESSION for given key.
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    function session_set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
}

if (!function_exists('u_session_unset')) {
    /**
     * Unset value of $_SESSION for given key.
     *
     * @param string $key
     * @return void
     */
    function u_session_unset($key)
    {
        unset($_SESSION[$key]);
    }
}
