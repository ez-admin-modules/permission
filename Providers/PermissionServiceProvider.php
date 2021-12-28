<?php

namespace EzAdmin\Modules\Permission\Providers;

use EzAdmin\Modules\Permission\Http\Middleware\Permission;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Permission';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'permission';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        $this->registerMiddleware();
        $this->registerPublishing();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * 资源发布
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../Resources/dist' => public_path('modules/' . $this->moduleNameLower)], $this->moduleNameLower . '-module-dist');
        }
    }

    /**
     * 全局注册
     *
     * @return void
     */
    public function registerMiddleware()
    {
        // appendMiddlewareToGroup
        // $this->app->make(Kernel::class)->appendMiddlewareToGroup('api', Permission::class);
        $this->app->make(Router::class)->aliasMiddleware('permission', Permission::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
