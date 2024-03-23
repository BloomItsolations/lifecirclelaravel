<?php

namespace App\Http\Controllers\jobs;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\BinaryBenefit;
use App\Models\BinaryRewards;
use App\Models\Reward;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MatchingController extends Controller
{
    public function matching(){
        $today=Carbon::now()
                    // ->subDays(1)
                    ->format('Y-m-d');
        $unmatched=BinaryRewards::where('status', 'UnPaid')->get();
        foreach ($unmatched as $um){
            $side='left';
            if($um->side=='left'){
                $side='right';
            }
            $existing=BinaryRewards::where(['user_id'=>$um->user_id, 'level'=>$um->level, 'side'=>$side, 'status'=>'UnPaid'])->first();
            if($existing){
                $binary_pair_amount = min($um->amount, $existing->amount);
                $binary_benefit=$binary_pair_amount*(10/100); // 10% of the pair amount is for binary reward.
                if($existing->side == 'left'){
                    $right_user=$um;
                    $left_user=$existing;
                }elseif($existing->side == 'right'){
                    $left_user=$um;
                    $right_user=$existing;
                }
                $binary_array = [
                    'user_id' => $um->user_id,
                    'right_user_id' => $right_user->buyer_id,
                    'left_user_id' => $left_user->buyer_id,
                    'binary_pair_amount' => $binary_pair_amount,
                    'binary_benefit' => $binary_benefit,
                ];
                // dd($binary_array);
                $inserted =BinaryBenefit::insert($binary_array);
                if ($inserted) {
                    $um->status = 'Paid';
                    $um->save();
                    $existing->status = 'Paid';
                    $existing->save();
                    $description = 'Binary Matching Reward Added For ' . $left_user->user_details->member_id . ' and ' . $right_user->user_details->member_id ;
                    // echo 'conditoion: '.($directs>=$qualifying_directs).'<br> Description'.$description.'<br>' ;
                    $reward = Reward::where(['user_id' => $um->user_id, 'amount' => $binary_benefit, 'description' => $description])->first();
                    if (!$reward) {
                        // echo '<br>reward<br>';
                        $reward = new Reward();
                        $reward->user_id = $um->user_id;
                        // $reward->child_id = $user->id;
                        $reward->description = $description;
                        $reward->amount = $binary_benefit;
                        $reward->tds = $binary_benefit*(5/100);
                        $reward->admin_charges = $binary_benefit*(10/100);
                        $reward->repurchase_wallet = $binary_benefit*(10/100);
                        $reward->credit = $binary_benefit-($reward->tds + $reward->admin_charges + $reward->repurchase_wallet);
                        $reward->save();
                        Helper::addToWallet($um->user_id, $reward->credit, $reward->repurchase_wallet);
                    }
                }
            }
            // dd($um,$existing);
        }
        return true;
    }
}
