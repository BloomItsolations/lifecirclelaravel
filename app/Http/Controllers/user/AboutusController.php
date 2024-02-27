<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;    
use App\Models\AboutUs;

class AboutusController extends Controller
{
    //About us
    public function index(){
        $aboutus= AboutUs::where('id',2)->get();
        // dd($aboutus);
        return view('pages.about',compact('aboutus'));
    }
}
