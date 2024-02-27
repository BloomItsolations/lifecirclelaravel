<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PinList;
use App\Models\Package;
use App\Models\Pin;

class PinController extends Controller
{
    public function index(){
        $pins = PinList::orderBy('created_at','DESC')->get();
        $packages = Package::all();
        return view('admin.pin-list',compact('pins','packages'));
    }

    public function pinApproved(Request $request){
        $pin = PinList::where('pin_number',$request->id)->first();
        if(!$pin){
            return back()->with('flash_error','PIN is Invalid');
        }
        $pin->used_status = 'Approved';
        $pin->save();
        return back()->with('flash_success',' PIN is approved!');
    }

       
}
