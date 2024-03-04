<?php

if (!defined("APP_CORE"))
{
    define("APP_CORE", __DIR__ . "/../../");
    define("PUBLIC_DATA", __DIR__ . "/../../public/");
    define("XT", ".php");
}

// Load App Class
require APP_CORE . "engine/core/definitions" . XT;

/**
 *  Autoloader for the entities zgExcell app
 */
spl_autoload_register(function ($class) {

    // project-specific namespace prefix
    $prefix = 'Entities\\';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class file and and path
    $reversedClass = array_values(array_reverse(explode("\\", $class)));
    $classFile = $reversedClass[0];
    unset($reversedClass[0]);
    $classPath = strtolower(implode("\\",array_reverse(array_filter($reversedClass))));

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = APP_LIBRARIES . str_replace('\\', '/', $classPath . "/" . $classFile) . '.php';

    //dd($file);

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

/**
 *  Autoloader for the Main EZcard app
 */
spl_autoload_register(function ($class)
{
    $reversedClass = array_values(array_reverse(explode("\\", $class)));
    $classFile = $reversedClass[0];
    unset($reversedClass[0]);
    $classArray = array_reverse(array_filter($reversedClass));
    $classPath = strtolower(implode("\\",$classArray));

    $file = APP_CORE . str_replace('\\', '/', $classPath . "/" . $classFile) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
        return;
    }

    if (empty($classArray[3])) { return; }

    $classModulePath = str_replace([strtolower("\\" . $classArray[1] . "\\")], ["\\" . $classArray[1] . "\\"], $classPath);
    $fileModule = APP_CORE . str_replace('\\', '/', $classModulePath . "/" . $classFile) . '.php';


    // if the file exists, require it
    if (file_exists($fileModule)) {
        require $fileModule;
        return;
    }

    $classModuleWidgetPath = str_replace([strtolower("\\" . $classArray[1] . "\\"), strtolower("\\" . $classArray[3] . "\\")], ["\\" . $classArray[1] . "\\", "\\" . $classArray[3] . "\\"], $classPath);
    $fileModuleWidget = APP_CORE . str_replace('\\', '/', $classModuleWidgetPath . "/" . $classFile) . '.php';

    // if the file exists, require it
    if (file_exists($fileModuleWidget)) {
        require $fileModuleWidget;
        return;
    }
});

/**
 *  Autoloader for the vendors EZcard app
 */
spl_autoload_register(function ($class) {

    // project-specific namespace prefix
    $prefix = 'Vendors\\';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class file and and path
    $relative_class = substr($class, $len);
    $reversedClass = array_values(array_reverse(explode("\\", $relative_class)));
    $classFile = $reversedClass[0];
    unset($reversedClass[0]);
    $classPath = strtolower(implode("\\",array_reverse(array_filter($reversedClass))));

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = APP_VENDORS . str_replace('\\', '/', $classPath . "/" . $classFile) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

/**
 *  Autoloader for the vendors EZcard app
 */
spl_autoload_register(function ($class) {

    // project-specific namespace prefix
    $prefix = 'App\\';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class file and and path
    $relative_class = substr($class, $len);
    $reversedClass = array_values(array_reverse(explode("\\", $relative_class)));
    $classFile = $reversedClass[0];
    unset($reversedClass[0]);
    $classPath = strtolower(implode("\\",array_reverse(array_filter($reversedClass))));

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = APP_LIBRARIES . str_replace('\\', '/', $classPath . "/" . $classFile) . '.php';

    //dd($file);

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});
/**
 *  Autoloader for the vendors EZcard app
 */
spl_autoload_register(function ($class) {

    // project-specific namespace prefix
    $prefix = 'Http\\';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class file and and path
    $relative_class = substr($class, $len);
    $reversedClass = array_values(array_reverse(explode("\\", $relative_class)));
    $classFile = $reversedClass[0];
    unset($reversedClass[0]);
    $classPath = strtolower(implode("\\",array_reverse(array_filter($reversedClass))));

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = APP_HTTP_ENTITIES . str_replace('\\', '/', $classPath . "/" . $classFile) . '.php';

    //dd($file);

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});