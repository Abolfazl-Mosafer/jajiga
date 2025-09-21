<?php

namespace App\Middlewares;

use App\Traits\ResponseTrait;

class CheckAccessMiddleware
{
    use ResponseTrait;
    
    public function checkAccess($accessed, $in=true)
    {
        $request_token = getTokenFromRequest();
        $token = $request_token->headers ?? $request_token->query ?? $request_token->body ?? null;
        $data = getPostDataInput();

        if(!$data || !$token){
            $this->sendResponse(message: "توکن شما معتبر نیست!", error: true, status: HTTP_Forbidden);
            return exit();
        }

        $access = in_array($data->user_detail->role, $accessed);
        if(!$in) $access = !in_array($data->user_detail->role, $accessed);

        if(!$access){
            $this->sendResponse(message: "شما دسترسی این کار را ندارید!", error: true, status: HTTP_Forbidden);
            return exit();
        }

        return true;
    }
}