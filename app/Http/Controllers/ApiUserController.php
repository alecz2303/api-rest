<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Filters\UsrFilter;
use Illuminate\Http\Request;
use App\Http\Resources\ApiUserCollection;

class ApiUserController extends Controller
{
    function index(Request $request) {
        $filter = new \App\Filters\UsrFilter();
        $queryItems = $filter->transform($request);
        $users = User::where($queryItems);
        return new ApiUserCollection($users->paginate()->appends($request->query()));
    }
}
