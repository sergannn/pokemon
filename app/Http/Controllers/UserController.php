<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController
{
    public function index(Request $request)
    {
        $users = User::with(['markers' => function($query) {
            $query->with('present:id,title,user_id');
        }])->select(['id', 'name', 'email'])->get();

        return response()->json($users)->setStatusCode(200);
    }

    public function show($user)
    {
        $user = User::find($user);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $presents = DB::table('markers')
        ->join('presents', 'markers.present_id', '=', 'presents.id')
        ->where('markers.user_id', $user->id)
       // ->whereNull('markers.present_id', false)
       ->select(['presents.title','presents.img', 'markers.present_id'])
   //     ->distinct()
        ->get();
    
        return response()->json($presents)->setStatusCode(200);
    
}
}