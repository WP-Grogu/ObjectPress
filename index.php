<?php

/*
Plugin Name: ObjectPress
Plugin URI: https://hydrat.agency
Description: A micro-frameworks providing OOP tools (such as models) for WordPress development.
Author: Hydrat agency
Version: dev-2.0
Author URI: https://hydrat.agency
*/


/**
 * Software is like sex: It’s better when it’s free.
 *
 * This file is usefull if you plan to put the lib sources in your wordpress mu-plugins folder.
 */

if (!defined('ABSPATH')) {
    exit('This framework requires Wordpress in order to work properly.');
}

if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    exit('Couldn\'t find autoload file inside ObjectPress directory. Please run `composer install`');
}

require_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/src/Support/helpers.php';
