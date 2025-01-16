<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\ProgressUpdated;

class ProgressListenerController extends Controller
{
    public function progressListener(Request $request)
    {
        event(new ProgressUpdated($request->all()));
        
        return response()->json(['message' => 'Listening for progress updates']);
    }
}
