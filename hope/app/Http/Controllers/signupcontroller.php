<?php

namespace App\Http\Controllers;

use App\docsignmodel;
use App\mo;
use App\signup;
use DateTime;
use Faker\Provider\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str as IlluminateStr;
use Psy\Util\Str;

class signupcontroller extends Controller
{
    //
//     function index (Request $request ){
// $data = mo::all();
// return $data ; 


//     }
    function patientsignup (Request $request ){

        $type = $request->type ; 
if ($type!='mobile' &&$type !='web' ) $request->type ='mobile' ; $type = $request->type ;  
        $type.='token' ;
        

   $validator = Validator::make($request->all() , ['','name' =>'required','email' =>'unique:patient|unique:doctor|required|
    string|
    min:10|            
    regex:/[a-z]/|
    regex:/[@$!%*#?&]/ |regex:/^.+@.+$/i',
   'password' =>'required',
   'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
   ]    ) ; 

    if ($validator->fails() )

    return response($validator->errors(),400) ; 

$name = ""; 

    if ($request->hasFile('image')) {
   
        $image = $request->file('image');
        $name = $image->getClientOriginalName();
     //   $size = $image->getClientSize();
        $destinationPath = public_path('/images');
        $image->move($destinationPath, $name);

    
       
    }
    $file_path= $name;

        $data = signup::create(['name' =>$request -> name ,
        'email' =>$request -> email ,
        'password' =>$request -> password ,
        $type =>  IlluminateStr::random(60) ,
        'image' =>$file_path,
        'bloodtype' => $request -> bloodtype ,
        'adress' =>$request -> adress ,
        'mobile' =>$request -> mobile 
    
     ]
    );
    $json = json_encode($data);
    $json = json_decode($json,true);
    $json['apitoken'] = $json[$type];
    
    unset($json[$type]);
        return response($json,200) ; 

  }


            function docsignup (Request $request ){ $format = 'H';

                $type = $request->type ;  
        if ($type!='mobile' &&$type !='web' ) $request->type ='mobile' ; $type = $request->type ;  

                $type.='token' ;

                $validator = Validator::make($request->all() , ['','name' =>'required','email' =>'unique:patient|unique:doctor|required|string|
                min:10|            
                regex:/[a-z]/|
                regex:/[@$!%*#?&]/ |regex:/^.+@.+$/i' ,
                'password' =>'required',
                'cost' =>'required',
                'cv' =>'required',
                // 'holidaydays' =>'required',
                // 'starttime' =>'required',
                // 'endtime' =>'required',
                'nationalid' =>'required|unique:doctor,nationalid',
   'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
                
                ]    ) ; 
             
                 if ($validator->fails() )
             
                return response( $validator->errors() , 400) ;
//                 $d = DateTime::createFromFormat($format, $request->starttime);
// $x= ( $d && $d->format($format) == $request->starttime);
// if ($x== false)  return response( json_encode(  [ 0 =>['starttime formate must be H']] ) ,400 ); 
//            $d = DateTime::createFromFormat($format, $request->endtime);
// $x= ( $d && $d->format($format) == $request->endtime);

// if ($x== false)  return response( json_encode(  [ 0 =>['endtime formate must be H']] ) ,400 );

// if ($request->endtime <=$request->starttime&&$request->endtime !='00')  return response( json_encode(  [ 0 =>['endtime must be greater than start time']] ) ,400 );                 $name = "" ;  $cvname = ""; 
                 if ($request->hasFile('image')) {
   
                    $image = $request->file('image');
                    $name = $image->getClientOriginalName();
                  //  $size = $image->getClientSize();
                    $destinationPath = public_path('/images');
                    $image->move($destinationPath, $name);
                   
                }
                
              
   
                    $cvimage = $request->file('cv');
                    $cvname = $cvimage->getClientOriginalName();
                   // $cvsize = $cvimage->getClientSize();
                    $cvdestinationPath = public_path('/images');
                    $cvimage->move($cvdestinationPath, $cvname);
                   
                    $file_path = $name ; 
                $cvfile_path= $cvname;

                     $data = docsignmodel::create(['name' =>$request -> name ,
                   'email' =>$request -> email ,
                     'password' =>$request -> password ,
                     $type =>  IlluminateStr::random(60) ,
                     'image' =>$file_path,
                    // 'holidaydays' => $request -> holidaydays ,
                     'adress' =>$request -> adress ,
                     'mobile' =>$request -> mobile ,
                     'cost' =>$request -> cost ,
                     'nationalid' =>$request -> nationalid ,
                   //  'endtime' =>$request -> endtime ,
                   //  'starttime' =>$request -> starttime ,
                     'cv' =>$cvfile_path 
                 
                  ]
                 ); 
                    $json = json_encode($data);
                 $json = json_decode($json,true);
                 $json['apitoken'] = $json[$type];
                 
                 unset($json[$type]);
                     return response($json,200) ; 
             
    }


function patientupdateprofile (request $request){
    $type = $request->type ;  
    if ($type!='mobile' &&$type !='web' )$request->type ='mobile' ; $type = $request->type ;  

    $type.='token' ;


    $validator = Validator::make($request->all() , [
        'apitoken' =>'required'
   
   ]    ) ;  if ($validator->fails() )  
    {
       return response( $validator->errors() , 400) ;
    }
$currenttoken  = DB::table('patient') ->where($type,$request ->apitoken) ->value($type );
if ($currenttoken==null)    return response( 'check ur apitoken !' , 400) ;
$name = ""; 

    if ($request->hasFile('image')) {
   
        $image = $request->file('image');
        $name = $image->getClientOriginalName();
      //  $size = $image->getClientSize();
        $destinationPath = public_path('/images');
        $image->move($destinationPath, $name);

    
       
    }
    $file_path= $name;
if ($request ->name!=null) DB::table('patient') ->where($type,$request ->apitoken) ->update(['name' => $request ->name  ]   );

if ($request ->email!=null) 
{ 
    $validator = Validator::make(  $request->all() ,  [ 'email' =>'unique:doctor' ]    ) ; 

    if ($validator->fails() )
    {
       return response( $validator->errors() , 400) ;
    }
    DB::table('patient') ->where($type,$request ->apitoken) ->update(['email' => $request ->email  ]   );
 
}


if ($request ->password!=null) DB::table('patient') ->where($type,$request ->apitoken) ->update(['password' => $request ->password  ]   );
if ($request ->image!=null) DB::table('patient') ->where($type,$request ->apitoken) ->update(['image' => $file_path  ]   );
if ($request ->bloodtype!=null) DB::table('patient') ->where($type,$request ->apitoken) ->update(['bloodtype' => $request ->bloodtype  ]   );
if ($request ->adress!=null) DB::table('patient') ->where($type,$request ->apitoken) ->update(['adress' => $request ->adress  ]   );
if ($request ->mobile!=null) DB::table('patient') ->where($type,$request ->apitoken) ->update(['mobile' => $request ->mobile  ]   );
                  
$user = DB::table('patient') ->where($type,$request ->apitoken)->first();
           
$json = json_encode($user);

$json = json_decode($json,true);
$json['apitoken'] = $request->apitoken ; 

unset($json['mobiletoken']);
unset($json['webtoken']) ; 
return $json  ;

}

function doctorupdateprofile (request $request){
    $type = $request->type ;  
  
    if ($type!='mobile' &&$type !='web' )$request->type ='mobile' ; $type = $request->type ;  
    $type.='token' ; 

     $validator = Validator::make($request->all() , [
        'apitoken' =>'required'
   
   ]    ) ;  if ($validator->fails() )  
    {
       return response( $validator->errors() , 400) ;
    }
$currenttoken  = DB::table('doctor') ->where($type,$request ->apitoken) ->value($type );
if ($currenttoken==null)    return response( 'check ur apitoken !' , 400) ;

   
if ($request ->name!=null) DB::table('doctor') ->where($type,$request ->apitoken) ->update(['name' => $request ->name  ]   );

if ($request ->email!=null) 
{ 
    $validator = Validator::make(  $request->all() ,  [ 'email' =>'unique:patient' ]    ) ; 

    if ($validator->fails() )
    {
       return response( $validator->errors() , 400) ;
    }
    DB::table('doctor') ->where($type,$request ->apitoken) ->update(['email' => $request ->email  ]   );
 
}
$name = ""; 

    if ($request->hasFile('image')) {
   
        $image = $request->file('image');
        $name = $image->getClientOriginalName();
        //$size = $image->getClientSize();
        $destinationPath = public_path('/images');
        $image->move($destinationPath, $name);

    
       
    }
    $file_path= $name;

if ($request ->password!=null) DB::table('doctor') ->where($type,$request ->apitoken) ->update(['password' => $request ->password  ]   );
if ($request ->image!=null) DB::table('doctor') ->where($type,$request ->apitoken) ->update(['image' =>  $file_path  ]   );
if ($request ->holidaydays!=null) DB::table('doctor') ->where($type,$request ->apitoken) ->update(['holidaydays' => $request ->holidaydays  ]   );
if ($request ->adress!=null) DB::table('doctor') ->where($type,$request ->apitoken) ->update(['adress' => $request ->adress  ]   );
if ($request ->mobile!=null) DB::table('doctor') ->where($type,$request ->apitoken) ->update(['mobile' => $request ->mobile  ]   );
if ($request ->cost!=null) DB::table('doctor') ->where($type,$request ->apitoken) ->update(['cost' => $request ->cost  ]   );
if ($request ->starttime!=null) DB::table('doctor') ->where($type,$request ->apitoken) ->update(['starttime' => $request ->starttime  ]   );
if ($request ->endtime!=null) DB::table('doctor') ->where($type,$request ->apitoken) ->update(['endtime' => $request ->endtime  ]   );

$user = DB::table('doctor') ->where($type,$request ->apitoken)->first();
           
$json = json_encode($user);

$json = json_decode($json,true);
$json['apitoken'] = $request->apitoken ; 

unset($json['mobiletoken']);
unset($json['webtoken']) ; 
return $json  ;

}


}
