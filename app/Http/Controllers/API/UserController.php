<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Play;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function editNickname(Request $request, $id)
    {
        $authUser = Auth::user()->id;
 
        if($authUser == $id) {
            $user = User::find($id);
 
            $request->validate([
                'nickname' =>'required|min:3|max:50',
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
         
        return response('Nickname changed succesfully', 200);
    }
     
    public function allPlayersInfo()
    { 
        if(Auth::user()->role != 'admin') {
            return response()->json([
                'message' => 'Acces denied',
                'status' => 403                
            ]);
        }
    
        $users = User::all();
        $usersArray = [];

        foreach ($users as $user) {
            $userPlays = Play::where('user_id', $user->id)->get();
            $totalPlays = $userPlays->count();
            $winPlays = $userPlays->where('result', 'you win! :)')->count();
            $successPercentage = 0;

            if ($totalPlays > 0) {
                $successPercentage = ($winPlays / $totalPlays) * 100;
            }

            $userArray = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'successPercentage' => $successPercentage
            ];

            array_push($usersArray, $userArray);
        }
    
        return response()->json([
            'users' => $usersArray,
            'status' => 200
        ]);
    }
}
