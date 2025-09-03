<?php

namespace App\Controllers;

use App\Controllers\Controller;

class DestinationController extends Controller
{
    private $roles;

    public function __construct()
    {
        parent::__construct();

        $this->roles = ["admin", "support"];
        $this->Access->checkAccess($this->roles);
    }

    public function index()
    {
        $destinations = $this->queryBuilder->table('destinations')->getAll()->execute();

        return $this->sendResponse(data: $destinations, message: "لیست مقصد ها با موفقیت گرفته شد");
    }

    public function store($request)
    {
        $this->validate([
            'title||required|min:3|max:50',
            'weather_id||required|number',
        ], $request);

        $weather = $this->queryBuilder->table('weathers')->where(value: $request->weather_id)->get()->execute();

        if(!$weather) return $this->sendResponse(message: "آب و هوای وارد شده نامعتبر است", error: true, status: HTTP_BadREQUEST);

        $newDestination = $this->queryBuilder->table('destinations')
            ->insert([
                'title' => $request->title,
                'weather_id' => $request->weather_id,
                'created_at' => time(),
                'updated_at' => time()
            ])->execute();

        return $this->sendResponse(data: $newDestination, message: "مقصد جدید با موفقیت اضافه شد");
    }
}