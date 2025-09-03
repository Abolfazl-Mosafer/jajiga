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
        $this->roles = ["host", "admin"];
        $this->Access->checkAccess($this->roles);

        $rooms = $this->queryBuilder->table('rooms')->getAll()->execute();

        return $this->sendResponse(data: $rooms, message: "لیست اتاق ها با موفقیت دریافت شد");
    }
}