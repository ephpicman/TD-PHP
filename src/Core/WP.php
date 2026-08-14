<?php

declare(strict_types=1);

namespace Nex\Core;

class WP
{
    public static function addFilter(string $name, callable $callback, int $priority, int $acceptedArgs) {
        \add_filter($name, $callback, $priority, $acceptedArgs);
    }
    public static function addAction(string $name, callable $callback, int $priority, int $acceptedArgs) {
        \add_action($name, $callback, $priority, $acceptedArgs);
    }
    public static function onActivation(string $pluginRootFile, callable $callback) {
        \register_activation_hook($pluginRootFile, $callback);
    }
    public static function onDeactivation(string $pluginRootFile, callable $callback) {
        \register_deactivation_hook($pluginRootFile, $callback);
    }

    public static function onUninstallation(string $pluginRootFile, callable $callback) {
        \register_uninstall_hook($pluginRootFile, $callback);
    }
}