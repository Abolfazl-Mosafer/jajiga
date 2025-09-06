<?php

namespace App\Middlewares;

use App\Traits\ResponseTrait;

class CheckAccessMiddleware
{
    use ResponseTrait;
    
    public function checkAccess($accessed, $in=true)
    {
        $data = getPostDataInput();
        $access = in_array($data->user_detail->role, $accessed);
        if(!$in) $access = !in_array($data->user_detail->role, $accessed);

        if(!$access){
            $this->sendResponse(message: "شما دسترسی این کار را ندارید!", error: true, status: HTTP_Forbidden);
            return exit();
        }

        return true;
    }
}