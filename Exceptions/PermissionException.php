<?php

namespace EzAdmin\Modules\Permission\Exceptions;

use Exception;
use EzAdmin\Support\Facades\Response;

class PermissionException extends Exception
{
    public function report()
    {
        return true;
    }

    public function render()
    {
        return Response::fail($this->getMessage());
    }
}
