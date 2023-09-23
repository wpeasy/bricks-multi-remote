<?php
/**
 * Plugin Name: Bricks Multi Remote
 * Author: Alan Blair
 * Author URI: https://www.wpeasy.au
 * Text Domain: bricks-multi-remote
 */

namespace BricksMultiRemotePlugin;

use BricksMultiRemote\App;
// WordPress or die clause
defined('ABSPATH') || exit;

require_once __DIR__ . '/vendor/autoload.php';

App::getInstance();