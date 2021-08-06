<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class signin extends Controller
{
   
function login (Request $request){

    $validator = Validator::make($request->all() , ['password' =>'exists:patient','email' =>'exists:patient' ,
                
    ] 
    
    ) ; 
    if ($validator->fails() )
             
    return $validator->errors() ; 

} 


}
