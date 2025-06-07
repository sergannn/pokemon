<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{

    public $loginAfterSignUp = true;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
//это про названия путей, а не методов
        $this->middleware( 'auth:api', [ 'except' => [ 'login', 'register','checkusername','checknickname','changepassword' ] ] );
    }
    public function changePassword(Request $request)
    {
      //  echo 123;exit();
//      echo $request->email;
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required',
        ]);
      // Find user by email and verify password
    
   $user = User::where('email', $request->email)->first();
     
      if (!$user || !Hash::check($request->old_password, $user->password)) {
          return response()->json([
              'message' => 'wrong email или pass'
          ], 401);
      }
       
       //echo $user->password;
      // exit();
        $user->password = Hash::make($request->new_password);
        $user->save();
    
        // Revoke all tokens to force re-login
       // $user->tokens()->delete();
    
        return response()->json([
            'message' => 'changed'
        ]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login() {
        $credentials = request( [ 'email', 'password' ] );
       // print_r( $credentials);
        if ( ! $token = auth()->attempt( $credentials ) ) {
            return response()->json( [ 'error' => 'Unauthorized' ], 401 );
        }

        return $this->respondWithToken( $token );
    }

    /**
     * Register a user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register( Request $request ) {
      //  dump( '1' );
        $user = User::create( [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make( $request->password ),
        ] );

        $token = auth()->login( $user );

        return $this->respondWithToken( $token );
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me() {
        $user = auth()->user();
    
      
        // Load user's markers
        $markers = $user->markers()->get();
        $presents = DB::table('markers')
        ->join('presents', 'markers.present_id', '=', 'presents.id')
        ->where('markers.user_id', $user->id)
       // ->whereNull('markers.present_id', false)
       ->select(['presents.title','presents.img', 'markers.present_id'])
   //     ->distinct()
        ->get();
        return response()->json([
            'user' => $user,
            'presents' => $presents,
            'markers' => $markers
        ]);
        //return response()->json( auth()->user() );
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();

        return response()->json( [ 'message' => 'Successfully logged out' ] );
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->respondWithToken( auth()->refresh() );
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken( $token ) {
        return response()->json( [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ] );
    }

    public function checkUsername(Request $request)
{
    
    $isAvailable = User::where('email', $request->username)->doesntExist();
    if(!$isAvailable) { $notAvailable = User::where('email',$request->username)->get(); }
      
    return response()->json([
        'success' => true,
        'available' => $isAvailable,
        'name'=> isset($notAvailable) ? $notAvailable[0]->name : false,
        'message' => $isAvailable 
            ? 'Имя пользователя доступно'
            : 'Имя пользователя уже занято'
    ]);
}
public function checkNickname(Request $request)
{
    
    $isAvailable = User::where('name', $request->nickname)->doesntExist();
    
    return response()->json([
        'success' => true,
        'available' => $isAvailable,
        'message' => $isAvailable 
            ? 'Имя пользователя доступно'
            : 'Имя пользователя уже занято'
    ]);
}
}