<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\RankManagement;
use App\Models\Tree;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Reward;
use App\Models\Wallet;
use App\Models\WithdrawlRequest;
use App\Models\ReferralAmount;
use App\Helpers\Helper;
use App\Models\BinaryBenefit;
use App\Models\BinaryRewards;
use App\Models\Order;
use App\Models\UserPackage;
use App\Models\DailyPackageBenefit;
use App\Models\PinList;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Session;
use Illuminate\Support\Facades\Auth;
use PHPUnit\TextUI\Help;
use Illuminate\Support\Facades\DB;
use App\Models\Level;

class HomeController extends Controller
{
    public function index(){
        $user = Auth::user();
        $directs_wallet = 0;
        $downline = 0;
        $total_orders = 0;
        $team_order = 0;

        $wallet = Wallet::where(['user_id'=>$user->id,'status'=>'Active'])->first();

        $directs=User::where('sponser_id',$user->member_id)->pluck('id')->toArray();
        if(count($directs)){
            $directs_wallet=Wallet::whereIn('user_id',$directs)->sum('amount');
            $team_order=Order::whereIn('user_id',$directs)->sum('payable_price');

        }

        // $downlines=Helper::tree($user->member_id);
        // if($downlines){
        //     $downline=count($downlines);

        // }

        $reward=Reward::where(['user_id'=>$user->id])->orderBy('id','desc')->limit(6)->get();
        $orders=Order::where(['user_id'=>$user->id,'status'=>'Active'])->whereNotNull('payment_id')->orderBy('id','desc')->get();
        $user_order=Order::where('user_id',$user->id)->sum('payable_price');
        $withdrawl_request=WithdrawlRequest::where('user_id',$user->id)->get();

        $rankmanagement=RankManagement::all();
        $packages = UserPackage::where('user_id',$user->id)
        ->whereNotNull('payment_id')
        ->orderBy('id','DESC')
        ->limit(1)
        ->get();

        // daily benefits
        // Artisan::call('command:calculateBinaryIncentive',['user_id'=>$user->id]);
        $dailyBenefits = DailyPackageBenefit::where('user_id', \Auth::user()->id)->with('package', 'user')->orderBy('created_at', 'DESC')->get();
        $binarys = BinaryBenefit::where('user_id', \Auth::user()->id)->get();
        $levels=Level::get();
        $unmatched=BinaryRewards::where('user_id', \Auth::user()->id)->where('status', 'UnPaid')->get();
        return view('user.index',compact('wallet','user','directs','directs_wallet','reward','orders',
                                        'user_order','rankmanagement','downline','team_order','withdrawl_request',
                                        'packages', 'dailyBenefits','binarys','levels','unmatched'));
    }


    public function register(){
        $rrr = Helper::get_user_level_all('14');
        // dd('test');
        return view('user.register');
    }

    public function userStore(Request $request){
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'mobile' => 'required|string|min:10|max:15|unique:users',
            'pin_number' => 'required',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
            'agree' => 'required',
        ]);

        $pin = PinList::where('pin_number',$request->pin_number)->first();
        if(!$pin){
            return back()->with('flash_error', "Invalid PIN.");
        }
        if($pin->user_id!=null){
            return back()->with('flash_error', "PIN is used alredy.");
        }
        if($pin->used_status=='Pending'){
            return back()->with('flash_error', "This PIN is not approved by admin.");
        }
        $check_sponser = User::where('member_id',$pin->member_id)->first();
        // dd($check_sponser);
        if ($check_sponser) {
            try{
                DB::beginTransaction();
                $user_count = (User::count()+1);
                $member_id = 'LIFE' . sprintf('%06d', $user_count);
                // dd($pin);
                $rank = RankManagement::orderBy('id','asc')->first();
                $empty_node=Helper::emptyNode($pin->member_id,$pin->side);
                // dd($empty_node);
                $user = new User;
                $user->name = $request->name;
                $user->rank_id = $rank->id;
                $user->email = $request->email;
                $user->mobile = $request->mobile;
                $user->sponser_id = $pin->member_id;
                $user->member_id = $member_id;
                $user->placement_id = $empty_node;
                $user->side = $pin->side;
                $user->password = Hash::make($request->password);
                $user->password_hint = ($request->password);
                $user->status='Active';
                $user->save();
                $this->purchasePackage($user,$request);
                $existingNodes = new Tree;
                $existingNodes->user_id = $user->id;
                $existingNodes->save();
                // $this->user_setting_to_left_right($check_sponser->id,$user);
                // dd('test');
                DB::commit();
                return back()->with('flash_success', "Registration Completed Successfully.");

            }
            catch(Exception $e){
                DB::rollback();
                dd($e);
            }
        }else{
            return back()->with('flash_error', "Invalid Sponsor Id.");
        }
    }

    function user_setting_to_left_right($sponser_id,$user){
        $existingNodes=Tree::where('user_id',$sponser_id)->first();
        if(!$existingNodes){
            $existingNodes = new Tree;
            $existingNodes->user_id = $sponser_id;
            $existingNodes->save();
        }
        if($existingNodes){
            if($existingNodes->right_user_id==null){
                $existingNodes->right_user_id = $user->id;
                $existingNodes->save();
                $user->placement_id=$existingNodes->user->member_id;
                $user->save();
                return true;
            }
            if($existingNodes->left_user_id==null){
                $existingNodes->left_user_id = $user->id;
                $existingNodes->save();
                $user->placement_id=$existingNodes->user->member_id;
                $user->save();
                return true;
            }
        }
        $sponser_id = Helper::get_user_level_all($sponser_id);
        if($sponser_id>0){
            $this->user_setting_to_left_right($sponser_id,$user);
        }
        // Artisan::call('command:calculateBinaryIncentive',['user_id'=>$user->id]);

    }
    
    public function purchasePackage($user,$request){
        $package = PinList::where('pin_number',$request->pin_number)->first();
        $package->user_id = $user->id;
        $package->used_status = 'Approved';
        $package->save();
        $getlastId = UserPackage::where('user_id', $user->id)->orderBy('id', 'desc')->first();
        if ($getlastId) {
            $order_id = "ORDER_" . time() . $getlastId->id;
        } else {
            $order_id = "ORDER_" . time() . "1";
        }
        // dd($request->all());
        $txnid = uniqid();
        $userPackage = new UserPackage();
        $userPackage->user_id = $user->id;
        $userPackage->package_id = $package->package_id;
        $userPackage->order_id = $order_id;
        $userPackage->amount = $package->package_amount;
        $userPackage->payment_type = 'COD';
        $userPackage->txn_id = $txnid;
        $userPackage->payment_request_id = $txnid;
        $userPackage->payment_id = $txnid;
        $userPackage->save();
        $direct_refferal_reward= $package->package_amount *(10/100);

        Helper::generateBinaryIncome($user->placement_id, $user->side, $user->id, $level=1, $package->package_amount);


        Helper::addRefferalIncome($user->id, $direct_refferal_reward);
        // Helper::addLevelIncome($user->id, $package->package_amount);
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


    public function login(){

        return view('user.login');
    }

    public function logins(Request $request){
        // dd($request->all());
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('user/dashboard')->with('success','Login successfully');
        }
        return back()->with([
            'flash_error' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('flash_success', "Logout Successfully.");
    }

    public function forget(){

        return view('user.forgot');
    }
    public function reset(){

        return view('user.reset');
    }
    public function generatewallet(){

        $users= User::get();

        foreach ($users as $key => $user) {
            $user_reward= Reward::where('user_id',$user->id)->sum('amount');
            // dd($user_reward);
            // $user_total_reward= array_sum($user_reward->amount);

            $user_wallet= Wallet::where('user_id',$user->id)->first();
            if($user_wallet){
                $user_wallet->amount= $user_reward;
                $user_wallet->status= 'pending';
                $user_wallet->save();
            }else{
                $user_wallet= new Wallet;
                $user_wallet->user_id= $user->id;
                $user_wallet->amount= $user_reward;
                $user_wallet->status= 'pending';
                $user_wallet->save();
            }
        }
        return redirect()->route('home');
    }
}
