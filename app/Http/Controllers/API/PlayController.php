<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Play;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class PlayController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function diceRoll($id)
    {
        // return response()->json(['message' => 'PROBANDO RUTA dicesRoll']);

        $authUser = Auth::user()->id;

        if ($authUser->id == $id) {    

            $dice1 = rand(1,6);
            $dice2 = rand(1,6);
            $sum = $dice1 + $dice2;
            $result = $sum == 7 ? 'you win! :)' : 'you lost... :(';
            $timestamps = now();

            $play = new Play();

            $play->user_id = $id;

            $play->dice1 = $dice1;
            $play->dice2 = $dice2;
            $play->sum = $sum;
            $play->result = $result;
            $play->timestamps = $timestamps;

            $play->save();

            $average_ranking = $this->rankingAverage();

            return response()->json([
                'Dice 1 = ' => $dice1,
                'Dice 2 = ' => $dice2,
                'Sum two dices = ' => $sum,
                'Result: ' => $result,
                'Average ranking of all users: ' => $average_ranking,
            ]);

       } else {
            return response([
                'message' => 'User not found']);        
       }
    }

    public function getOwnPlays($id)
    {
        // return response()->json(['message' => 'PROBANDO RUTA getUserPlays']);

        $authUser = Auth::user()->id;

        $user = new User();
          
        $userPlays = Play::all()
            ->where('user_id', '=', $id);
    
        if ($authUser->id == $id) {

            if ($userPlays->isEmpty()) {
                return response()->json(['Total of plays' => 0], 201); 
            }

            $play = new Play();
            $playerPlays = $play->getPlayerPlays($id);
            $user = new User();
            $userAverage = $user->getUserAverage($id);

            return [$playerPlays, $userAverage];

        } else {

            return response ([
                "message" => "User not found"
            ], 401);}
    }

    public function removeOwnPlays($id)
    {
        // return response()->json(['message' => 'PROBANDO RUTA destroyPlays']);

        $authUser = Auth::user()->id;

        if(!User::find($id)){
            return response([
                'message' => 'User not found']);

        } elseif($authUser == $id) {
            $userPlays = Play::where('user_id', '=', $id)->first('id');

            if($userPlays !== null) {
                Play::where('user_id', $id)->delete();
                return response(['message' => 'All your plays have been successfully removed']);
            
            } else {
                return response(['message' => "You don't have any plays to remove"]);
            }

        } else {
            return response(['message' => 'Unauthorized'], 401);
        }
    }
    
    public function rankingAverage()
    {
        //return response()->json(['message' => 'PROBANDO RUTA rankingAverage']);

        if (Auth::user()->role != 'admin') {

            return response(['message' => 'Access denied']);

        } else {

            $total_plays = DB::select("SELECT COUNT(*) as total_plays FROM plays");

            $total_success = DB::select("SELECT COUNT(*) as total_success FROM plays WHERE result='you win! :)'");

            $average_ranking = $total_success[0]->total_success / $total_plays[0]->total_plays;
        }

        return $average_ranking;
    }

    public function loserPlayer()
    {
        //return response()->json(['message' => 'PROBANDO RUTA loserPlayer']);

        if (Auth::user()->role != 'admin') {

            return response(['message' => 'Access denied']);

        } else {

            // $worst_player = DB::select("SELECT user_id, COUNT(*) as success_count FROM plays WHERE result='you win! :)' GROUP BY user_id ORDER BY success_count ASC LIMIT 1");
            $worst_player = DB::select(`
                SELECT user_id, COUNT(*) as success_count FROM plays
                WHERE result='you win! :)'
                GROUP BY user_id ORDER BY success_count ASC
                LIMIT 1
            `);
        }

        return $worst_player;
    }
    
    public function winnerPlayer()
    {
        //return response()->json(['message' => 'PROBANDO RUTA winnerPlayer']);

        if (Auth::user()->role != 'admin') {

            return response(['message' => 'Access denied']);

        } else {

        //$best_player = DB::select("SELECT user_id, COUNT(*) as success_count FROM plays WHERE result='you win! :)' GROUP BY user_id ORDER BY success_count DESC LIMIT 1");
        $best_player = DB::select(`
            SELECT user_id, COUNT(*) as success_count FROM plays
            WHERE result='you win! :)'
            GROUP BY user_id ORDER BY success_count DESC
            LIMIT 1
        `);
        }

        return $best_player;
    }
}
