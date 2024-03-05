<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserPackage;
use App\Models\Wallet;

use App\Helpers\Helper;
use App\Models\BinaryBenefit;
use App\Models\Tree;

class calculateBinaryIncentive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:calculateBinaryIncentive {user_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to calculate monthly income of individual user on behalf of referred users.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return false;
        $user_id = $this->argument('user_id');
        if($user_id=='ALL'){
            $users_loop = User::all();
        }else{
            $users_loop = User::where('id',$user_id)->get();
        }
        // dd($users_loop);
        foreach($users_loop as $users_loop_row){
            $user_id = $users_loop_row->id;
            $tree_check = Tree::where('user_id',$user_id)->first();
            if(!$tree_check){
                return false;
            }
            $right_user_id = Tree::where('user_id',$user_id)->first()->right_user_id;
            $left_user_id = Tree::where('user_id',$user_id)->first()->left_user_id;
            // $user_id = 3;
            for($level=1;$level<=10;$level++){
                $left_users = array();
                $right_users = array();
                if($level==1){
                    $this->calculate_pairing($user_id,$right_user_id,$left_user_id);
                }
                else if($level==2){
                    // dd($right_user_id,$left_user_id);
                    $users = Tree::whereIn('user_id',[$right_user_id,$left_user_id])->get();
                    foreach($users as $user){
                        $right_users[] = $user->right_user_id;
                        $left_users[] = $user->left_user_id;
                    }
                    // dd($right_users,$left_users);
                    $right_users = array_filter($right_users);
                    $left_users = array_filter($left_users);
                    if(count($right_users)==2 && count($left_users)==2){
                        rsort($right_users);
                    }
                    else if(count($right_users)==2 && count($left_users)<2){
                        rsort($right_users);
                    }
                    else if(count($right_users)<2 && count($left_users)==2){
                        rsort($left_users);
                    }
                    else if(count($right_users)<2 && count($left_users)==2){
                        rsort($left_users);
                    }
                    foreach($right_users as $index=>$right){
                        if(isset($right_users[$index]) && $right_users[$index] && isset($left_users[$index]) && $left_users[$index]){
                            $this->calculate_pairing($user_id,$right_users[$index],$left_users[$index]);
                        }
                    }
                }
                else{
                    return false;
                    $level = $level -1;
                    $right_users_array = Helper::get_user_level_all($right_user_id,2,$level,'');
                    $left_users_array = Helper::get_user_level_all($left_user_id,2,$level,'');
                    $right_users = Tree::whereIn('user_id',$right_users_array)->get();
                    $left_users = Tree::whereIn('user_id',$left_users_array)->get();
                    // dd($right_users,$left_users);
                    dd($level,$right_users_array,$left_users_array);
                    $left_users = array();
                    $right_users = array();
                    // dd($users);
                    foreach($users as $user){
                        $right_users[] = $user->right_user_id;
                        $left_users[] = $user->left_user_id;
                    }
                }


            }
        }
    }

    public static function calculate_pairing($user_id,$right_user_id,$left_user_id){
        
        $right_amount = $left_amount= $binary_pair_amount = $binary_benefit = 0;
        $right_package = UserPackage::where('user_id',$right_user_id)->first();
        $left_package = UserPackage::where('user_id',$left_user_id)->first();
        if($right_package){
            $right_amount = $right_package->amount;
        }
        if($left_package){
            $left_amount = $left_package->amount;
        }
        if($right_amount==0 || $left_amount==0){
            return false;
        }
        if(($right_amount == $left_amount) || ($right_amount < $left_amount)){
            $binary_pair_amount = $right_amount;
        }
        else{
            $binary_pair_amount = $left_amount;
        }
        $binary_benefit = round(($binary_pair_amount*10)/100,2);
        $binary_array = [
            'user_id' => $user_id,
            'right_user_id' => $right_user_id,
            'left_user_id' => $left_user_id,
            'binary_pair_amount' => $binary_pair_amount,
            'binary_benefit' => $binary_benefit,
        ];

        $binary_benefits = BinaryBenefit::where('user_id',$user_id)
        ->where(function($query) use($right_user_id,$left_user_id){
            $query->where('right_user_id',$right_user_id)
            ->orWhere('left_user_id',$left_user_id);

        })
        ->first();
        // dd($binary_benefits);
        if(!$binary_benefits){
            BinaryBenefit::insert($binary_array);
            calculateBinaryIncentive::addToWallet($user_id,$binary_benefit);
            // $this->addToWallet($user_id,$amount);
        }
    }

    public static function addToWallet($user_id,$amount){
        $wallet=Wallet::where('user_id',$user_id)->first();
        if($wallet){
            $wallet->amount+=$amount;
            $wallet->save();
        }else{
            $wallet=new Wallet();
            $wallet->user_id=$user_id;
            $wallet->amount=$amount;
            $wallet->status="Active";
            $wallet->save();
        }
    }


}
