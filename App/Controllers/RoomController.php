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

    public function index($request)
    {
        $rooms = $this->queryBuilder->table('rooms')
        ->select(['rooms.*', 'GROUP_CONCAT(features.title) as features', 'destinations.title as destination', 'weathers.title as weather', 'IF(room_like.id IS NULL,0,1) as liked'])
        ->join('room_feature', 'rooms.id', '=', 'room_feature.room_id', 'LEFT')
        ->join('features', 'features.id', '=', 'room_feature.feature_id', 'LEFT')
        ->join('destinations', 'rooms.destination_id', '=', 'destinations.id', 'LEFT')
        ->join('weathers', 'destinations.weather_id', '=', 'weathers.id', 'LEFT')
        ->join('room_like', 'room_like.room_id', '=', 'rooms.id', 'LEFT', ['room_like.user_id', $request->user_detail->id ?? 0])
        ->groupBy('rooms.id, room_like.id')
        ->getAll()->execute();
        return $this->sendResponse(data: $rooms, message: "لیست اتاق ها با موفقیت دریافت شد");
    }

    public function get($id, $request)
    {
        $room = $this->queryBuilder->table('rooms')
            ->select(['rooms.*', 'GROUP_CONCAT(features.title) as features', 'destinations.title as destination', 'weathers.title as weather', 'IF(room_like.id IS NULL,0,1) as liked'])
            ->join('room_feature', 'rooms.id', '=', 'room_feature.room_id', 'LEFT')
            ->join('features', 'features.id', '=', 'room_feature.feature_id', 'LEFT')
            ->join('destinations', 'rooms.destination_id', '=', 'destinations.id', 'LEFT')
            ->join('weathers', 'destinations.weather_id', '=', 'weathers.id', 'LEFT')
            ->join('room_like', 'room_like.room_id', '=', 'rooms.id', 'LEFT', ['room_like.user_id', $request->user_detail->id ?? 0])
            ->groupBy('rooms.id, room_like.id')
            ->where(column: 'rooms.id', value: $id)->get()->execute();

        $room->features = explode(',', $room->features);

        if(!$room) return $this->sendResponse(message: "اتاق شما پیدا نشد", error: true, status: HTTP_BadREQUEST);
        return $this->sendResponse(data: $room, message: "اتاق شما با موفقیت دریافت شد");
    }

    public function store($request)
    {
        $this->validate([
            'host_id||number',
            'destination_id||number',
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
                'destination_id' => $request->destination_id,
                'title' => $request->title,
                'room_detail' => $request->room_detail,
                'capacity' => $request->capacity,
                'addition_capacity' => $request->addition_capacity ?? NULL,
                'created_at' => time(),
                'updated_at' => time()
            ])->execute();

        return $this->sendResponse(data: $newRoom, message: "اتاق شما با موفقیت ساخته شد");
    }

    public function update($id, $request)
    {
        $this->validate([
            'title||required|string|min:5',
            'room_detail||string',
            'capacity||required|number',
            'addition_capacity||number',
        ], $request);

        $updateRoom = $this->queryBuilder->table('rooms')
            ->update([
                'title' => $request->title,
                'room_detail' => $request->room_detail,
                'capacity' => $request->capacity,
                'addition_capacity' => $request->addition_capacity ?? NULL,
                'updated_at' => time()
            ])->where(value: $id)->execute();

        return $this->sendResponse(data: $updateRoom, message: "اتاق شما با موفقیت ویرایش شد");
    }

    public function destroy($id)
    {
            $deletedRoom = $this->queryBuilder->table('rooms')
            ->update([
                'deleted_at' => time()
            ])->where(value: $id)->execute();

        return $this->sendResponse(data: $deletedRoom, message: "اتاق با موفقیت حذف شد");
    }

    public function append_feature($request)
    {
        $this->validate([
            'room_id||required|number',
            'feature_id||required|number',
        ], $request);

        // Check Room
        $getRoom = $this->queryBuilder->table('rooms')->where(value: $request->room_id)->get()->execute();
        if(!$getRoom) return $this->sendResponse(message: "اتاق شما پیدا نشد", error: true, status: HTTP_BadREQUEST);

        // Check Feature
        $getFeature = $this->queryBuilder->table('features')->where(value: $request->feature_id)->get()->execute();
        if(!$getFeature) return $this->sendResponse(message: "ویژگی شما پیدا نشد", error: true, status: HTTP_BadREQUEST);

        $appendFeature = $this->queryBuilder->table('room_feature')
            ->insert([
                'room_id' => $request->room_id,
                'feature_id' => $request->feature_id,
                'created_at' => time()
            ])->execute();

        return $this->sendResponse(data: $appendFeature, message: "ویژگی مورد نظر به اتاق شما اضافه شد");
    }

    public function add_feature($request)
    {
        $this->validate([
            'title||required|string',
        ], $request);

        $addFeature = $this->queryBuilder->table('features')
            ->insert([
                'title' => $request->title,
                'created_at' => time(),
                'updated_at' => time()
            ])->execute();

        return $this->sendResponse(data: $addFeature, message: "ویژگی شما اضافه شد");
    }

    public function room_like($request)
    {
        $this->validate([
            'room_id||required|number'
        ], $request);

        $room = $this->queryBuilder->table('rooms')->where($request->room_id)->get()->execute();
        if(!$room) return $this->sendResponse(message: "اتاق شما پیدا نشد", error:true, status:HTTP_BadREQUEST);

        $getLike = $this->queryBuilder->table('room_like')
            ->where('room_id', '=', $request->room_id)
            ->where('user_id', '=', $request->user_detail->id)
            ->get()->execute();
        
        if($getLike){
            $deletedRoom =$this->queryBuilder->table('room_like')
                ->delete()->where('id', '=', $getLike->id)->execute();
                
            return $this->sendResponse(data: $deletedRoom, message: "پست مدنظر با موفقیت دیسلایک شد");
        }else {
            $likeRoom = $this->queryBuilder->table('room_like')
                ->insert([
                    'room_id' => $request->room_id,
                    'user_id' => $request->user_detail->id,
                    'created_at' => time()
                ])->execute();

            return $this->sendResponse(data: $likeRoom, message: "پست مدنظر با موفقیت لایک شد");
        }
    }
}