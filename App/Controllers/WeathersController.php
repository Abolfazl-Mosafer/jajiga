<?php

namespace App\Controllers;

use App\Database\QueryBuilder;
use App\Traits\ResponseTrait;
use App\Validations\ValidateData;

class WeathersController
{
    use ResponseTrait;

    protected $queryBuilder;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
    }

    public function index()
    {
        $weathers = $this->queryBuilder->table('weathers')->getAll()->execute();
        return $this->sendResponse(data: $weathers, message: "لیست آب و هوا ها با موفقیت گرفته شد.");
    }
}