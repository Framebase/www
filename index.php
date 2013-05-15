<?php
define('INCLUDES_DIR', dirname(__FILE__) . '/Includes');
require_once('Includes/SplClassLoader.php');
$classLoader = new SplClassLoader(null, dirname(__FILE__) . '/Includes');
$classLoader->register();

try {
    \CuteControllers\Router::rewrite('framebase-js', '/assets/framebase_js');
    \CuteControllers\Router::start(dirname(__FILE__) . '/Includes/FSStack/Framebase/www/Controllers');
} catch (\CuteControllers\HttpError $err) {
    header('HTTP/1.1 ' . $err->getCode() . ' ' . $err->getMessage());
    echo $err->getMessage();
}
