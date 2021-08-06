<?php

namespace App\Http\Controllers;

use App\mo;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class mocontroller extends Controller
{

    function login (Request $request){
      $credentials = request(['email', 'password']);
// return  request(['email', 'password']);
      if (! $token = auth('api')->attempt($credentials)) {
        //  return response()->json(['error' => 'Unauthorized'], 401);
      }

     else  return $this->respondWithToken($token);
      $credentials = request(['email', 'password']);

      if (! $token = auth('doctor')->attempt($credentials)) {
          return response()->json(['error' => 'Unauthorized'], 401);
      }

      return $this->respondWithToken($token);
      
      $type = $request->type ;

      if ($type!='mobile' &&$type !='web' ) $request->type ='mobile' ; $type = $request->type ;  
       $type.='token' ;
       $hide = ($type=='mobiletoken'?'webtoken':'mobiletoken') ; 

      $validator = Validator::make($request->all() , ['password' =>'required|exists:patient','email' =>'required|exists:patient' ,
            
      ] 
      
      ) ; 
     

      if ($validator->fails() ) { 
        
        $validator = Validator::make($request->all() , ['password' =>'required|exists:doctor','email' =>'required|exists:doctor' ,
        'type' =>'required'   
      ] 
      
      ) ; 
      if ($validator->fails() ) return response( $validator->errors() , 400) ;
      $id = DB::table('doctor')->where('email' ,$request->email)->value('id');
      $pass = DB::table('doctor')->where('email' ,$request->email)->value('password');
      if ($pass!= $request->password) return response(json_encode(['error' => ["email or password error"]]),400) ; 
    
      $inserttoken = DB::table('doctor')
              ->where('id', $id)
              ->update(array($type => Str::random(60)));
              $user = DB::table('doctor')->where('id', $id)->first();
            
              $json = json_encode($user);
              $json = json_decode($json,true);
              $json['apitoken'] = $json[$type];
              unset($json[$hide]);
              unset($json[$type]);
              return response($json,200) ;  
    }

    $id = DB::table('patient')->where('email' ,$request->email)->value('id');
    $pass = DB::table('patient')->where('email' ,$request->email)->value('password');
    if ($pass!=$request->password) return response(json_encode(['error' => ["email or password error"]]),400) ; 
    $inserttoken = DB::table('patient')
            ->where('id', $id)
            ->update(array($type => Str::random(60)));
            $user = DB::table('patient')->where('id', $id)->first();
          
            $json = json_encode($user);
            $json = json_decode($json,true);
            $json['apitoken'] = $json[$type];
            unset($json[$hide]);
            unset($json[$type]);
            
            
    return response($json,200) ; 
   
  
  } 

  function logout (request $request) {

    $type = $request->type ;

    if ($type!='mobile' &&$type !='web' ) $request->type ='mobile' ; $type = $request->type ;  
     $type.='token' ;

    $validator = Validator::make($request->all() , ['apitoken' =>'required','email' =>'required|exists:patient' ,
                  
    ] 
    
    ) ; 
   

    if ($validator->fails() ) { 
      
      $validator = Validator::make($request->all() , ['apitoken' =>'required','email' =>'required|exists:doctor' ,
                
    ] 
    
    ) ; 
    
    if ($validator->fails() ) return response( $validator->errors() , 400) ;
    $token = DB::table('doctor')->where('email' ,$request->email)->value($type);
  //$pass = DB::table('patient')->where('email' ,$request->email)->value('password');
  if ($token!=$request->apitoken) return response(json_encode(['error' => ["email or password error"]]),400) ; 
  
    $inserttoken = DB::table('doctor')
            ->where($type, $token)
            ->update(array($type => null));
            
            return response("logged out successfully !",200) ;  
  }

  $token = DB::table('patient')->where('email' ,$request->email)->value($type);
  //$pass = DB::table('patient')->where('email' ,$request->email)->value('password');
  if ($token!=$request->apitoken) return response(json_encode(['error' => ["email or password error"]]),400) ; 
  $inserttoken = DB::table('patient')
          ->where($type, $token)
          ->update(array($type => null));
          
         
        
          return response("logged out successfully !",200) ;  
  }
  protected function respondWithToken($token)
  {
      return response()->json([
          'access_token' => $token,
          'token_type' => 'bearer',
          'expires_in' =>null
          // 'expires_in' => auth('api')->factory()->getTTL() * 60

      ]);
  }
}
