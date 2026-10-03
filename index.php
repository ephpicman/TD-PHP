<?php

/**
 * Nex - Next Generation User Management
 * 
 * @package         nex/nex
 * @author          Sina Kuhestani (EphpicMan)
 * @version         1.0.0
 * @license         GPL-3.0-or-later
 * 
 * @wordpress-plugin
 * Plugin Name:     Nex
 * Plugin URI:      https://github.com/EphpicMan/Nex
 * Description:     Advanced user management and access control for WordPress
 * Version:         1.0.0
 * Author:          Sina Kuhestani
 * Author URI:      https://www.linkedin.com/in/ephpicman/
 * License:         GPL-3.0-or-later
 * License URI:     https://www.gnu.org/licenses/gpl-3.0.html
 * Requires PHP:    8.2
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