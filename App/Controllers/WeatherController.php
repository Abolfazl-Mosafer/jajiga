<?php

namespace App\Controllers;

use App\Controllers\Controller;

class WeatherController extends Controller
{
    private $roles;

    public function __construct()
    {
        parent::__construct();

        $this->roles = ["guest"];

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