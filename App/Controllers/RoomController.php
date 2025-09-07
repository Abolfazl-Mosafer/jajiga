<?php

namespace App\Controllers;

use App\Controllers\Controller;

class RoomController extends Controller
{

    protected $roles;

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $rooms = $this->queryBuilder->table('rooms')->getAll()->execute();
        return $this->sendResponse(data: $rooms, message: "لیست اتاق ها با موفقیت دریافت شد");
    }

    public function get($id)
    {
        $room = $this->queryBuilder->table('rooms')
            ->where(value: $id)->get()->execute();

        if(!$room) return $this->sendResponse(message: "اتاق شما پیدا نشد", error: true, status: HTTP_BadREQUEST);
        return $this->sendResponse(data: $room, message: "اتاق شما با موفقیت دریافت شد");
    }

    public function store($request)
    {
        $this->validate([
            'host_id||number',
            'title||required|string|min:5',
            'room_detail||string',
            'capacity||required|number',
            'addition_capacity||number',
        ], $request);

        if($request->user_detail->role == "host") $request->host_id = $request->user_detail->id;

        $getHost = $this->queryBuilder->table('users')
            ->where('id', '=', $request->host_id)
            ->where('role', '=', 'host')->get()->execute();
        if(!$getHost) return $this->sendResponse(message: "میزبان شما پیدا نشد", error: true, status: HTTP_BadREQUEST);

        $newRoom = $this->queryBuilder->table('rooms')
            ->insert([
                'host_id' => $request->host_id,
                'title' => $request->title,
                'room_detail' => $request->room_detail,
                'capacity' => $request->capacity,
                'addition_capacity' => $request->addition_capacity ?? NULL,
                'created_at' => time(),
                'updated_at' => time()
            ])->execute();

        return $this->sendResponse(data: $newRoom, message: "اتاق شما با موفقیت ساخته شد");
    }
}