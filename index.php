<?php

/**
 * Software is like sex: It’s better when it’s free.
 * 
 * This file is used if you decide to put the lib in your WP plugins folder.
 */

if (!defined('ABSPATH')) {
    throw new \Exception('This framework requires wordpress in order to work properly');
}

if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    throw new \Exception('Couldn\'t find autoload file inside ObjectPress directory.');
}

require_once __DIR__ . '/vendor/autoload.php';

\OP\Core\Container::getInstance();
