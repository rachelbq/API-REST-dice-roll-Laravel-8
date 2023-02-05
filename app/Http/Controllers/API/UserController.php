<?php

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function editNickname(Request $request, $id)
    {
        // return response()->json(['message' => 'PROBANDO RUTA editNickname']);
 
        $authUser = Auth::user()->id;
 
        if($authUser == $id) {
 
             $user = User::find($id);
 
             $request->validate([
                 'nickname' =>'required|min:3|max:50',
                 'email' => 'required|string|email|unique:users',
             ]);
 
        } elseif(!User::find($id)) {
            return response([
                'message' => 'User not found'
                    ], 404);
 
        } else {
            return response([
                'message' => 'Unauthorized'
            ], 401);
         }
     
        $user->update($request->all());
         
        //return $user;
        return response('Nickname changed succesfuly', 200);
    }
     
    public function allPlayersInfo()
    { 
        // return response()->json(['message' => 'PROBANDO RUTA allPlayersInfo']);
 
        if(Auth::user()->role == 'admin') {
 
            $users = DB::table('users')
                ->select('*')
                ->get();
         
            return response()->json([
                'users' => $users,
                'status' => 200
            ]);
 
        } else {
 
            return response()->json([
                'message' => 'Acces denied',
                'status' => 403                
            ]);
        }
    }
}
