<?php

namespace App\Http\Controllers\user;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Tree;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class GenealogyController extends Controller
{
    public function genealogy(Request $request){

        if($request->member_id){
            $user=User::where('member_id',$request->member_id)->first();
            $tree=Tree::where('user_id',$user->id)->first();
        }else{
            $user=Auth::user();
            $tree=Tree::where('user_id',$user->id)->first();
        }
        $first_level_array = Helper::get_user_level_all($user->id,$type=2,$level=1);
        $first_level = User::whereIn('id',$first_level_array)->orderBy('id','DESC')->get();
        $second_level_array = Helper::get_user_level_all($user->id,$type=2,$level=2);
        $second_level = User::whereIn('id',$second_level_array)->orderBy('id','DESC')->get();
        $three_level_array = Helper::get_user_level_all($user->id,$type=2,$level=3);
        $three_level = User::whereIn('id',$three_level_array)->orderBy('id','DESC')->get();
        return view('user.Genealogy.genealogy',compact('first_level','second_level','three_level','user'));
}
public function genealogySearch(Request $request){
    // $search_id=$request->member_id;
    // $user=Auth::user();
    // $tree=Tree::where('user_id',$user->id)->first();
    // $search=User::where('member_id',$search_id)->first();
    // if($tree->left_user_id==$search->id){
    //     $memberId=$search_id;
    // }elseif($tree->middle_user_id==$search->id){
    //     $memberId=$search_id;

    // }elseif($tree->right_user_id==$search->id){
    //     $memberId=$search_id;

    // }elseif($tree->fourth_user_id==$search->id){
    //     $memberId=$search_id;

    // }else{
    //     return redirect()->back()->with('error','No Such Member Found In Your Downline');
    // }
    // dd($request->all());
    $user=User::where('member_id',$request->member_id)->first();
    // dd($user);
        if($user){
            // dd('dd');
            $memberId=$request->member_id;
            return redirect()->route('child-genealogy',$memberId);

        }else{
            return redirect()->back()->with('error','No Such Member Found In Your Downline');
        }

}
public function child_genealogy($memberId){
    $user=User::where('member_id',$memberId)->first();
    $tree=Tree::where('user_id',$user->id)->first();

    return view('user.Genealogy.child-genealogy',compact('tree','user'));
}

}
