<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Marker;
use App\Models\Present;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MarkerController
{

    
    public function index( Request $request ) {
        $markers = Marker::with( 'present' )
            ->when( $request->has( 'status' ), function ( $query ) {
                return $query->where( 'status', $request->input( 'status' ) );
            } )
        //->skip(000)
        ->limit(1000)
            ->get();

        return response()->json( $markers )->setStatusCode( 200 );
    }

    public function webindex( Request $request ) {
        //ini_set('memory_limit', '-1'); 
        $coordinates = DB::table( 'markers' )
            ->select(
                'lat1', 'lon1', 'lat2', 'lon2', 'lat3', 'lon3', 'lat4', 'lon4'
            )
            ->groupBy( 'lat1', 'lon1', 'lat2', 'lon2', 'lat3', 'lon3', 'lat4', 'lon4' )
            ->orderByRaw( "lat1, lon1, lat2, lon2, lat3, lon4, lon3" )
            ->limit(100)
            ->get();

        $result = [];
        foreach ( $coordinates as $polygon ) {
            $result[] =
                [
                    [ 'lat' => $polygon->lat1, 'long' => $polygon->lon1 ],
                    [ 'lat' => $polygon->lat2, 'long' => $polygon->lon2 ],
                    [ 'lat' => $polygon->lat3, 'long' => $polygon->lon3 ],
                    [ 'lat' => $polygon->lat4, 'long' => $polygon->lon4 ]
                ];

        }

        return response()->json( $result )->setStatusCode( 200 );
    }

    public function getPresentsByMarker( Request $request, $markerId ) {
        $marker = Marker::findOrFail( $markerId );
        $user = auth()->user();
        $user->decrement( 'attempts' );
    
        if ($marker->present()->exists()) {  
            //$user->presents()->attach($marker->present);
        }
        $marker->user_id=auth()->id();
        $marker->updateStatus('used');
        //        dump($user->name,$user->attempts);
        $presents = $marker->present;
      //  echo count($presents);
        return response()->json( $presents )->setStatusCode( 200 );
    }
    public function getPresentsByMarkerWeb( Request $request, $markerId ) {
        $marker = Marker::findOrFail( $markerId );
        $marker->user_id=2  ;
        //if ($marker->present()->exists()) {  
          //  User::findOrFail(1)->associatePresent($marker->present);
          //  User::findOrFail(1)->presents()->attach($marker->present->id);
        //  echo $marker->present->id;
        
     
    //    $user = auth()->user();
    //    $user->decrement( 'attempts' );
        //        dump($user->name,$user->attempts);
        $marker->updateStatus('used');
        if ($marker->present()->exists()) {  }
        $presents = $marker->present;
      
        return response()->json( $presents )->setStatusCode( 200 );
    }

    public function getStatus( Request $request, $markerId ) {
        $marker = Marker::findOrFail( $markerId );

        return response()->json( [ 'status' => $marker->status ] )->setStatusCode( 200 );
    }

    public function updateStatus( Request $request, $markerId ) {
        $marker = Marker::findOrFail( $markerId );
        $marker->update( [ 'status' => $request->input( 'status' ) ] );

        return response()->json( [ 'message' => 'Marker status updated successfully' ] )->setStatusCode( 200 );
    }
}
