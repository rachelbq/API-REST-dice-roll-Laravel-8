<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Play;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Validator;


class PlayController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function diceRoll($id)
    {
        $authUser = Auth::user()->id;

        if ($authUser == $id) {    

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
            ]);

        } else {
            return response([
                'message' => 'User not found']);        
        }
    }

    public function getOwnPlays($id)
    {
        $authUser = Auth::user()->id;

        if ($authUser != $id) {
            return response([
                'message' => 'Access Denied'
            ], 401);
        }
    
        $plays = Play::where('user_id', $id)->get();
    
        if ($plays->isEmpty()) {
            return response([
                'message' => 'No plays found for this user'
            ]);
        }
    
        $wins = $plays->where('result', 'you win! :)')->count();
        $successPercentage = ($wins / $plays->count()) * 100;
    
        return [
            'plays' => $plays,
            'success_percentage' => $successPercentage.'%',
        ];
    }

    public function removeOwnPlays($id)
    {
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
        if (Auth::user()->role != 'admin') {
            return response(['message' => 'Access denied']);

        } else {
            $total_plays = DB::select("SELECT COUNT(*) as total_plays FROM plays");
            $total_success = DB::select("SELECT COUNT(*) as total_success FROM plays WHERE result='you win! :)'");

            if ($total_plays[0]->total_plays == 0) {
                return response()->json([
                    'message' => 'There are no plays'
            ]);
            }

            $average_ranking = $total_success[0]->total_success / $total_plays[0]->total_plays * 100;
        }

        return response()->json([
            'Average ranking of all users: ' => round($average_ranking, 2).'%'
        ]);
    }

    public function loserPlayer()
    {
        if (Auth::user()->role != 'admin') {
            return response(['message' => 'Access denied']);

        } else {
            $worst_player = DB::select("
                SELECT user_id, COUNT(*) as success_count FROM plays
                WHERE result='you win! :)'
                GROUP BY user_id ORDER BY success_count ASC
                LIMIT 1
            ");
        }
        
        return response()->json([
            'The loser/worst player is... ' => $worst_player,
        ]);
    }
    
    public function winnerPlayer()
    {
        if (Auth::user()->role != 'admin') {
            return response(['message' => 'Access denied']);

        } else {
            $best_player = DB::select("
                SELECT user_id, COUNT(*) as success_count FROM plays
                WHERE result='you win! :)'
                GROUP BY user_id ORDER BY success_count DESC
                LIMIT 1
            ");
        }

        return response()->json([
            'The winner/best player is... ' => $best_player,
        ]);
    }
}
