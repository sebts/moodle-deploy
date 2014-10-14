<?php

global $GITCFG;

if (!file_exists('config.php') && file_exists('config-dist.php')) {
    copy('config-dist.php', 'config.php');
}
if (file_exists('config.php')) {
    require_once('./config.php');
} else {
    throw new Exception ("config.php must exist in root.", __LINE__);
}

function __autoload($class_name) {
  require_once $class_name.'.php';
}
