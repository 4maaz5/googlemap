<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    public function store(Request $request){
        $id=Auth::user()->id;
        $find=User::find($id);
        $find->location=$request->location;
        $find->latitude=$request->latitude;
        $find->longitude=$request->longitude;
        $find->save();
        return redirect()->back();
    }
}
