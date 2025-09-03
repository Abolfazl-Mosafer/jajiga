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

        return $this->sendResponse(data: $destinations, message: "لیست مقصد ها با موفقیت گرفته شد.");
    }
}