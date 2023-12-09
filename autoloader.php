<?php
set_include_path(getcwd() . "/");
spl_autoload_extensions(".php");
spl_autoload_register(function ($class) {
    require_once str_replace("\\", "/", $class) . ".php";
});
