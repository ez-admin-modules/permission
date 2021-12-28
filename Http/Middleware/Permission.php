<?php

namespace EzAdmin\Modules\Permission\Http\Middleware;

use Closure;
use EzAdmin\Modules\Permission\Exceptions\PermissionException;
use EzAdmin\Modules\Permission\Services\AdminerService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * 管理后台权限
 */
class Permission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $module)
    {
        if ($request->user()) {
            $mark = $this->parseActionName($request->route()->getActionName(), $module);
            if (!AdminerService::instance()->hasPermission($request->user()->id, $mark)) {
                throw new PermissionException('Permission Forbidden');
            }
        }
        return $next($request);
    }

    /**
     * @param $actionName
     */
    protected function parseActionName($actionName, $module)
    {
        [$namespace, $action] = explode('@', $actionName);

        $defNamespace = 'EzAdmin\\EzAdmin\Modules\\' . Str::ucfirst($module) . '\\Http\\Controllers\\Api';
        $moduleNamespace = explode('\\', Str::replace($defNamespace . '\\', '', $namespace));

        $controller = strtolower(Str::replace('Controller', '', implode('_', array_splice($moduleNamespace, 2))));
        return $module . ':' . $controller . ':' . $action;
    }
}
