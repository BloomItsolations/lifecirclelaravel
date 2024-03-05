<?php

namespace App\Helpers;

use App\Models\BinaryRewards;
use App\Models\Level;
use App\Models\Package;
use App\Models\Repurchage;
use App\Models\RepurchageOrder;
use App\Models\Reward;
use App\Models\Tree;
use Illuminate\Support\Facades\Redis;
use App\Models\User;
use App\Models\Wallet;

// require_once "Image.class.php";
// require_once "Config.class.php";
// require_once "Uploader.class.php";

class Helper
{
    /**
     * @param int $user_id User-id
     *
     * @return string
     **/
    public static function getBlogUrl($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        $string = strtolower($string); // Convert to lowercase

        return $string;
    }
    public static function calculateRewards($user_id, $memberId, $package_id)
    {
        $user = User::find($user_id);
        if ($user) {
            $level = 1;
            // dd($level);
            Helper::upLines($user->member_id, $level, $memberId, $package_id);
        } else {
            return 0;
        }
    }
    public static function upLines($member_id, $level, $memberId, $package_id)
    {
        $user = User::where('member_id', $member_id)->first();

        if ($level <= 10) {
            if ($user && $level <= 10) {

                $sponser = User::where('member_id', $user->sponser_id)->first();

                if ($sponser) {
                    Helper::addReward($user->sponser_id, $user->member_id, $memberId, $level, $package_id);
                    $level++;
                    Helper::upLines($user->sponser_id, $level, $memberId, $package_id);
                } else {
                    // echo 'user:'.$user->member_id.'<br>sponser Id: '.$user->sponser_id.'<br> Sponser <br>';
                }
            } else {
                echo 'No User<br>';
            }
        }
    }
    public static function addReward($sponser_id, $member_id, $purchasing_user, $level, $package_id)
    {
        return false;
        if ($level < 1 || $level > 9) {
            echo 'User Level is Not In Range';
        } else {
            // Find the user's record
            $upline = User::where('member_id', $sponser_id)->first();
            $user = User::where('member_id', $member_id)->first();
            $primary_user = User::where('member_id', $purchasing_user)->first();
            // Check if the user was found
            if ($upline) {
                $package = Package::find($package_id);
                $levels = Level::get();
                // dd($levels);
                $level_percentage = $levels[$level - 1]->percentage;
                $level_reward = ($package->amount * $level_percentage) / 100;
                // dd($level_reward);
                $description = 'Reward Added For ' . $primary_user->member_id . ' to ' . $upline->member_id . ' for Level ' . $level;
                // echo 'conditoion: '.($directs>=$qualifying_directs).'<br> Description'.$description.'<br>' ;
                $reward = Reward::where(['user_id' => $upline->id, 'amount' => $level_reward, 'description' => $description])->first();
                if (!$reward) {
                    // echo '<br>reward<br>';
                    $reward = new Reward();
                    $reward->user_id = $upline->id;
                    $reward->child_id = $user->id;
                    $reward->description = $description;
                    $reward->amount = $level_reward;
                    $reward->save();
                    Helper::addToWallet($upline->id, $level_reward);
                }
            } else {
                echo 'No User Found';
            }
        }
    }
    public static function tree($sponser_id)
    {
        $bc_id = $sponser_id;
        $child_array = array();

        $childs = User::where(['placement_id' => $sponser_id])->get();

        foreach ($childs as $child) {
            $child_array[] = $child->member_id;

            $child_array = array_merge($child_array, Helper::child_node($child->member_id));
        }

        return ($child_array);
    }

    public static function getTree($sponser_id)
    {

        $bc_id = $sponser_id;
        $child_array = array();

        return User::whereIn('placement_id', $sponser_id)->pluck('member_id')->toArray();

        // foreach ($childs as $child) {
        //     $child_array[] = $child->member_id;

        //     $child_array = array_merge($child_array, Helper::child_node($child->member_id));
        // }

        // return ($child_array);
    }

    public static function child_node($sponser_id)
    {

        // $bc_id = $sponser_id;
        // $sponser_id_array = [$bc_id];
        $child_array = array();
        $childs = User::where('placement_id', $sponser_id)->get();

        foreach ($childs as $key => $child) {


            $child_array[] = $child->member_id;

            $child_array = array_merge($child_array, Helper::child_node($child->member_id));
        }

        return ($child_array);
    }
    public static function calculateRepurchaseRewards($user_id, $orderId, $month, $year)
    {
        $user = User::find($user_id);
        if ($user) {
            $rank = $user->rank_id;
            $repurchage = Repurchage::where('rank_id', $rank)->first();
            if ($repurchage) {
                $own_order = RepurchageOrder::where('order_id', $orderId)->sum('payable_price');
                // $total_Purchage=$repurchage->self_purchage_amount+$repurchage->team_purchage_amount;
                $team = Helper::tree($user->member_id);
                // dd($team);
                $member_repurchage_order = 0;

                if (count($team) > 0) {
                    foreach ($team as $member) {
                        $member = User::where('member_id', $member)->first();
                        if ($member) {
                            // dd('member');
                            $member_repurchage_order += RepurchageOrder::where('user_id', $member->id)->whereMonth('created_at', $month)->whereYear('created_at', $year)->sum('payable_price');
                            // echo('member Purchage: '.$member_repurchage_order.'<br>Member_id: '.$member->member_id.'<br>month '.$month);

                        }
                    }
                }
                // dd($member_repurchage_order);
                if ($repurchage->self_purchage_amount <= $own_order && $repurchage->team_purchage_amount <= $member_repurchage_order) {
                    // dd('if');
                    $total_amount = $own_order + $member_repurchage_order;
                    $rewardAmount = ($total_amount * $repurchage->reward_percentage) / 100;
                    Helper::addRepurchageReward($user->id, $rewardAmount);
                }
            }
        }
    }
    public static function addRepurchageReward($user_id, $amount)
    {
        $user = User::find($user_id);
        if ($user) {
            $date = date('d-m-Y');
            $description = 'Repurchage Reward Added To ' . $user->member_id . ' On Date ' . $date;
            // echo 'user '.$user->member_id.' <br>sponser '.$sponser->member_id.'<br> rank '.$sponser->rank.'<br>';

            $reward = Reward::where(['user_id' => $user->id, 'description' => $description, 'amount' => $amount])->first();
            if (!$reward) {
                $reward = new Reward();
                $reward->user_id = $user->id;
                $reward->description = $description;
                $reward->amount = $amount;
                $reward->save();
                Helper::addToWallet($user->id, $amount);
            }
            // $this->addReward($sponser->sponser_id,$member_id);
        }
    }
    public static function addToWallet($user_id, $amount)
    {
        $wallet = Wallet::where('user_id', $user_id)->first();
        if ($wallet) {
            $wallet->amount += $amount;
            $wallet->save();
        } else {
            $wallet = new Wallet();
            $wallet->user_id = $user_id;
            $wallet->amount = $amount;
            $wallet->status = "Active";
            $wallet->save();
        }
    }




    public static function get_user_level_all($user_id, $returnType = 0, $level_count = 0, $sideType = 0)
    {
        // dd($user_id,$returnType,$level_count,$sideType);
        // $returnType=0 (vaccant node)
        // $returnType=1 (all level data)
        // $returnType=2 (single level data)
        // $returnType=3 (all users)
        $user_level = array();
        $all_users = array();
        $user_id_array = array($user_id);
        // dd($user_id_array);
        for ($level = 1; $level < 10; $level++) {
            $user_last_level = Helper::get_user_level($user_id_array, $sideType);
            // dd($user_last_level);
            if (count($user_last_level) > 0) {
                $user_level[$level] = $user_last_level;
                $all_users = array_merge($all_users, $user_last_level);
                $user_id_array = $user_last_level;
            }
        }
        // dd($user_level);
        if ($returnType == 0) {
            $vacant_node = Tree::whereIn('user_id', $all_users)
                ->whereNull('right_user_id')
                ->orWhereNull('left_user_id')
                ->first();
            if ($vacant_node) {
                return $vacant_node->user_id;
            } else {
                return '';
            }
        } else if ($returnType == 1) {
            return $user_level;
        } else if ($returnType == 2) {
            if (isset($user_level[$level_count])) {
                return $user_level[$level_count];
            } else {
                return array();
            }
        } else if ($returnType == 3) {
            return $all_users;
        }
    }

    public static function get_user_level($user_id_array, $sideType = 0)
    {
        // $sideType = 'LEFT'
        // $sideType = 'RIGHT'
        $users = Tree::whereIn('user_id', $user_id_array)->get();
        $user_array = array();
        foreach ($users as $user) {
            if ($sideType == 'LEFT') {
                $user_array[] = $user->left_user_id;
            } else if ($sideType == 'RIGHT') {
                $user_array[] = $user->right_user_id;
            } else {
                $user_array[] = $user->right_user_id;
                $user_array[] = $user->left_user_id;
            }
        }
        // dd($user_array);
        return $user_array;
    }

    public static function addRefferalIncome($user_id, $amount)
    {
        $primary_user = User::find($user_id);
        $upline = User::where('member_id', $primary_user->sponser_id)->first();
        if ($upline) {
            $description = 'Direct Refferal Reward Added For ' . $primary_user->member_id;

            $reward = new Reward();
            $reward->user_id = $upline->id;
            $reward->child_id = $user_id;
            $reward->description = $description;
            $reward->amount = $amount;
            $reward->save();
            Helper::addToWallet($upline->id, $amount);
        }
    }
    public static function addLevelIncome($user_id, $amount)
    {
        $user = User::find($user_id);
        // dd($user);
        $parent_id = $user->placement_id;
        $levels = Level::get();
        foreach ($levels as $level) {
            $parent = User::where('member_id', $parent_id)->first();
            if ($parent) {
                $level_percentage = $level->percentage;
                $level_reward = ($amount * $level_percentage) / 100;
                // dd($level_reward);
                $description = 'Reward Added For ' . $user->member_id . ' to ' . $parent->member_id . ' for Level ' . $level->name;
                // echo 'conditoion: '.($directs>=$qualifying_directs).'<br> Description'.$description.'<br>' ;
                $reward = Reward::where(['user_id' => $parent->id, 'amount' => $level_reward, 'description' => $description])->first();
                if (!$reward) {
                    // echo '<br>reward<br>';
                    $reward = new Reward();
                    $reward->user_id = $parent->id;
                    $reward->child_id = $user->id;
                    $reward->description = $description;
                    $reward->amount = $level_reward;
                    $reward->save();
                    Helper::addToWallet($parent->id, $level_reward);
                }
                $parent_id = $parent->placement_id;
            }
        }
    }
    public static function emptyNode($memberId,$side){
        $child = User::where('placement_id', $memberId)
                ->where('side', $side)
                ->first();
        if (!$child) {
            return $memberId;
        } else {
            return Helper::emptyNode($child->member_id, $side);
        }
    }
    // public static function generateBinaryIncome($user_id, $memberId, $package_id){
    //     $user = User::find($user_id);
    //     // $upline=User::where('member_id',$user->placement_id)->first();
    //     if ($user) {
    //         $level = 1;
    //         // dd($level);
    //         Helper::binaryUpLines($user->member_id, $level, $memberId, $package_id);
    //     } else {
    //         return 0;
    //     }
    // }
    public static function generateBinaryIncome($placement_id, $side, $userId, $level, $amount){
        $upline = User::where('member_id', $placement_id)->first();
        if ($upline){
            $binary_reward=new BinaryRewards();
            $binary_reward->user_id=$upline->id;
            $binary_reward->buyer_id=$userId;
            $binary_reward->level=$level;
            $binary_reward->side=$side;
            $binary_reward->amount=$amount;
            $binary_reward->status='UnPaid';
            $binary_reward->save();
            $level++;
            Helper::generateBinaryIncome($upline->placement_id, $upline->side, $userId, $level, $amount);
        }else{
            return 0;
        }
    }
}
