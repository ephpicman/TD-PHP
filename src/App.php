<?php

declare(strict_types=1);

namespace Nex;

use Nex\Core\Container;
use Nex\Core\Globals;
use Nex\Core\WP;

/**
 * Nex application.
 *
 * The application coordinates framework bootstrapping, registers core
 * services, and integrates with the WordPress lifecycle.
 */
final class App
{
    /**
     * Application container.
     */
    private Container $container;

    /**
     * Create a new application instance.
     */
    public function __construct()
    {
        $this->container = new Container();

        $this->registerCoreServices();
    }

    /**
     * Retrieve the application's service container.
     */
    public function container(): Container
    {
        return $this->container;
    }

    /**
     * Plugin activation callback.
     */
    public function activate(): void {}

    /**
     * Plugin deactivation callback.
     */
    public function deactivate(): void {}

    /**
     * Plugin uninstall callback.
     */
    public static function uninstall(): void {}

    /**
     * Boot the application.
     */
    public function run(): void
    {
        WP::onActivation(NEX_PLUGIN_FILE, [$this, 'activate']);
        WP::onDeactivation(NEX_PLUGIN_FILE, [$this, 'deactivate']);
        WP::onUninstallation(NEX_PLUGIN_FILE, [self::class, 'uninstall']);
    }

    /**
     * Register framework services.
     */
    private function registerCoreServices(): void
    {
        $this->container->instance(
            Container::class,
            $this->container
        );

        $this->container->instance(
            Globals::class,
            new Globals()
        );

        $this->container->instance(
            self::class,
            $this
        );
    }
}
