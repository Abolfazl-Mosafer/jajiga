<?php

namespace App\Controllers;

use App\Database\QueryBuilder;
use App\Traits\ResponseTrait;
use App\Validations\ValidateData;
use App\Middlewares\CheckAccessMiddleware;

class WeathersController
{
    use ResponseTrait;

    protected $queryBuilder;
    protected $Access;
    private $roles;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
        $this->Access       = new CheckAccessMiddleware();
        $this->roles = ["admin", "support"];

        $this->Access->checkAccess($this->roles);
    }

    public function index()
    {
        $weathers = $this->queryBuilder->table('weathers')->getAll()->execute();
        return $this->sendResponse(data: $weathers, message: "لیست آب و هوا ها با موفقیت گرفته شد.");
    }

    public function store($request)
    {
        dd("OK");
    }
}