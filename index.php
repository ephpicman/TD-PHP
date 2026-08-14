<?php

/**
 * Nex - Next Generation dvanced User Management
 * 
 * @package         nex/nex
 * @author          Sina Kuhestani (EphpicMan)
 * @version         1.0.0
 * @license         MIT License
 * 
 * @wordpress-plugin
 * Plugin Name:     Nex
 * Plugin URI:      https://github.com/NexPHP/Nex
 * Description:     Advanced user management and access control for WordPres
 * Version:         1.0.0
 * Author:          Sina Kuhestani
 * Author URI:      https://www.linkedin.com/in/ephpicman/
 * License:         GPL-2.0+
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Requires PHP:    8.1
 *
 */

declare(strict_types=1);

define('NEX_PLUGIN_FILE', __FILE__);

require_once __DIR__ . "/vendor/autoload.php";

if (!function_exists('nex')) {
    function nex(): Nex\App
    {
        static $app;

        if ($app === null) {
            $app = new Nex\App();
        }

        return $app;
    }
}

nex()->run();