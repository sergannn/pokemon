<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
    
    /**
     * Get the authenticated user's coins.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCoins()
    {
        $user = Auth::user();
        return response()->json(['coins' => $user->coins]);
    }

    /**
     * Update the authenticated user's coins.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCoins(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer',
            'operation' => 'required|in:add,deduct',
        ]);

        $user = Auth::user();
        $amount = $request->amount;
        $operation = $request->operation;

        if ($operation === 'add') {
            $newBalance = $user->addCoins($amount);
            return response()->json([
                'message' => 'Coins added successfully',
                'coins' => $newBalance
            ]);
        } else {
            $result = $user->deductCoins($amount);
            if ($result === false) {
                return response()->json([
                    'message' => 'Insufficient coins',
                    'coins' => $user->coins
                ], 400);
            }
            return response()->json([
                'message' => 'Coins deducted successfully',
                'coins' => $result
            ]);
        }
    }

    /**
     * Get the authenticated user's timer.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTimer()
    {
        $user = Auth::user();
        return response()->json(['timer' => $user->timer]);
    }
    public function getTimerAndCoins()
    {
        $user = Auth::user();
        return response()->json([
            'timer' => $user->timer,
            'coins'=>   $user->coins,
            'last_update' => $user->last_update]);
    }
    /**
     * Set the authenticated user's timer.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setTimer(Request $request)
    {

        /*  'remainingSeconds': remainingDuration,
        'earnedCoins': earnedCoins,
        'lastUpdate': DateTime.now().toIso8601String(),*/
        $request->validate([
            //'lastUpdate' => 'required|date',
            'remainingSeconds' => 'required|integer|min:0',
            'earnedCoins' => 'required|numeric|min:0',
        ]);
    
        $user = Auth::user();
        
        // Обновляем таймер
        $user->timer = $request->remainingSeconds;
        $user->last_update = $request->lastUpdate;
        
        // Если нужно обновлять монеты автоматически
        // $user->coins = $request->earnedCoins; 
        
        $user->save();
    
        return response()->json([
            'message' => 'Timer updated successfully',
            'timer' => $user->timer,
            'coins' => $user->coins,
            'lastUpdate' => $user->updated_at
        ]);
    }

    /**
     * Reset the authenticated user's timer to zero.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetTimer()
    {
        $user = Auth::user();
        $timer = $user->resetTimer();

        return response()->json([
            'message' => 'Timer reset successfully',
            'timer' => $timer
        ]);
    }
}
