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
        $this->validate([
            'title||required|min:3|max:50'
        ], $request);

        $newWeathers = $this->queryBuilder->table('weathers')
            ->insert([
                'title' => $request->title,
                'created_at' => time(),
                'updated_at' => time()
            ])->execute();

        return $this->sendResponse(data: $newWeathers, message: "آب و هوای جدید با موفقیت افزوده شد.");
    }
}