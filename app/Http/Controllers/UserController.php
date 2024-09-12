<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller {
      /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return response()->json([
            'status' => true,
            'message' => 'Users retrieved successfully',
            'data' => $users
        ], 200);

    }
}