<?php /* This file is used to include the lib sources in your wordpress mu-plugins folder. */

/*
Plugin Name: ObjectPress
Plugin URI: https://hydrat.agency
Description: A micro-frameworks providing illuminate packages (such as models, Eloquent ORM, events, validation..) into WordPress for improved development.
Author: Hydrat Agency
Version: dev-2.0
Author URI: https://hydrat.agency
*/

if (!defined('ABSPATH')) {
    exit('This framework requires Wordpress in order to work properly.');
}

if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    exit('Couldn\'t find autoload file inside ObjectPress directory. Please run `composer install`');
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Support/helpers.php';
