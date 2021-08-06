<?php

namespace App\Http\Controllers;
//ZHxy]1CDSerh!z-*
use App\appointments;
// use App\clinics;
use App\docsignmodel;
use App\records;
use app\Models\clinics;
use App\Models\clinicsmodel;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
// use Symfony\Component\VarDumper\VarDumper;
use App\mo;
use App\signup;

use Faker\Provider\Image;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Str as IlluminateStr;
use Psy\Util\Str;
use Illuminate\Support\Facades\Auth;

class insidecontroller extends Controller
{
 
    
        
    //  $date = date("Y-m-d H:i"); 
    
function addappointment(request $request   ){  $format = 'Y-m-d H:i';
    
    // return response()->json(auth("api")->user());
    return response()->json(auth("doctor")->user());

    return "hi";
    $type = $request->type ;

    if ($type!='mobile' &&$type !='web' ) $request->type ='mobile' ; $type = $request->type ;  
     $type.='token' ;
    $validator = Validator::make($request->all() , ['patientid' =>'exists:patient,id|required' ,
   
    'date' =>'required',
    'apitoken' =>'required'
    ,'docid' =>'required|exists:doctor,id',
    'clinicid' =>'required|exists:clinics,id',

    ]    ) ; 
 
     if ($validator->fails() )
 
return response($validator->errors(),400) ; 


$tokenmail = DB::table('patient')->where($type ,$request->apitoken)->value('id');
$docclinic = DB::table('clinics')->where('id' ,$request->clinicid)->value('docid');

if ($tokenmail != $request->patientid )        return response(  json_encode(  [ 'error'=>['current token doesnot math with the requested patientemail | make sure thta u sent right  type ! {mobile , web}']] )   , 401) ; 
if ($docclinic != $request->docid )        return response(  json_encode(  [ 'error'=>['current docid doesnot math with the requested clinicid ']] )   , 401) ; 

$d = DateTime::createFromFormat($format, $request->date);
$x= ( $d && $d->format($format) == $request->date);
if ($x== false)  return response( json_encode( "date formate must be Y-m-d H:i" ) ,400 );
$day = explode(" ",$request->date)[0]; 
$hou = explode(" ",$request->date)[1]; 
$dayname = date('D', strtotime($day)); $dayname = trim(strtolower($dayname)) ;
// return $dayname;
 $holi = DB::table('clinics')->where('id' ,$request->clinicid)->pluck('day');
 $data = json_decode( $holi )[0]; 
$clarr =  json_decode($data);
foreach($clarr as $object)
{
    $arrays[] =  (array) $object;
}
// Dump array with object-arrays
// dd($arrays);
// return $arrays[];
$holi=$arrays;
 $ok = false ; $key;
for ($i=0 ; $i<count($holi) ; $i++) {
  $holi[$i]['day'] = strtolower($holi[$i]['day']  ) ; 
    if ( $holi[$i]['day']  == $dayname ){ $ok = true ; $key =$i ; break ;}
//    else  echo ($holi[$i] )."".$dayname."<br>"  ; 
}
if (!$ok) return response( json_encode( ['error'=>["doctor doesnot work at this clinic in ".$dayname]] ) ,400 );


$starttime =$holi[$key]['start_time'] ;
$endtime =$holi[$key]['end_time'] ;
//  return $starttime ." " .$endtime ;

if ($request->date<date("Y-m-d H:i")) return response( json_encode( ['error'=>["date must be in future  " ]]) ,400 );
$f =  explode(" ",$request->date)[1] ; 
if ($f>=$endtime||$f<$starttime) return response( json_encode(['error'=>[ "date must be between doctor stat,end times  "]] ) ,400 );
$datedocid = DB::table('appointments')->where('date' ,$request->date)->pluck('docid');
$datepatient =array( DB::table('appointments')->where('date' ,$request->date)->pluck('patientid'));
$arr=explode(",",$datedocid); 

for ($i=0 ; $i<count($datepatient[0]) ; $i++) {  
    if ( $datepatient[0][$i] ==$request-> patientid ) return response( json_encode(['error'=>[ "u have an appointment already at this date   " ]]) ,400 );

}
for ($i=0 ; $i<count($datedocid) ; $i++) {  
    if ( $datedocid[$i] ==$request-> docid ) return response( json_encode(['error'=>[ "sorry but this date has already been taken by another patient " ]]) ,400 );

}
// $docimage =  DB::table('doctor')->where('id' ,$request->docid)->value('image');
// $docname=  DB::table('doctor')->where('id' ,$request->docid)->value('name');

// if (in_array($request->docid, $arr)) return response( json_encode( "sorry but this date has already been taken by another patient " ) ,400 );

     $data = appointments::create([//'docname' =>$docname ,
     'patientid' =>$request -> patientid ,
     'date' =>$request -> date ,
 
     'cost' =>$request -> cost
     ,'docid' =>$request -> docid,
     'clinicid' =>$request -> clinicid,
     //'docimage' =>$request -> $docimage
 
  ]
 );
 return json_encode( ["data"=> $data]) ; 

}

function removeappointment(request $request   ){
    $type = $request->type ;

    if ($type!='mobile' &&$type !='web' ) $request->type ='mobile' ; $type = $request->type ;  
     $type.='token' ;
    $validator = Validator::make($request->all() , [
   
    'id' =>'required|exists:appointments',
    
    'apitoken' =>'required'
    
    
    ]    ) ; 
 
     if ($validator->fails() )
 
return response($validator->errors(),400) ;  
     $tokenmail = DB::table('patient')->where($type ,$request->apitoken)->value('id');
     $currentmail =  DB::table('appointments')->where('id' ,$request->id)->value('patientid');
     if ($tokenmail != $currentmail ) 
     // ( json_encode(  [ 'error'=>['starttime formate must be H']] ) ,400 ); 
      return response(  json_encode(  [ 'error'=>['current token doesnot math with the requested patientemail | make sure thta u sent right  type ! {mobile , web}']] )   , 401) ; 
     $user = DB::table('appointments')->where('id', $request->id)->delete();
     

  return   json_encode( ["data"=> $user]) ; 

}
function addrecord(request $request   ){
    $type = $request->type ;

    if ($type!='mobile' &&$type !='web' ) $request->type ='mobile' ; $type = $request->type ;  
     $type.='token' ;
    $validator = Validator::make($request->all() , ['name' =>'required','patientid' =>'exists:patient,id|required' ,

  //  'apitoken' =>'required'
    
    
    ]    ) ; 
 
     if ($validator->fails() )
 
return response($validator->errors(),400) ;  
if ($request->result==null &&$request->resultasimage==null ) return response(json_encode([ 'error'=>["feilds {result , resultasimage} error: result must be sent as a text or image !"]]),400) ;  

    //  $tokenmail = DB::table('patient')->where($type ,$request->apitoken)->value('id');
    $tokenmail = Auth::id(); 
     if ($tokenmail == $request->patientid ) {
        $name = ""; 

        if ($request->hasFile('resultasimage')) {
       
            $image = $request->file('resultasimage');
            $name = $image->getClientOriginalName();
         //   $size = $image->getClientSize();
            $destinationPath = public_path('/images');
            $image->move($destinationPath, $name);
        }
        $file_path= $name;
        
        $data = records::create(['name' =>$request -> name ,'result' =>$request -> result ,
        'resultasimage' =>$file_path,
     'patientid' =>$request -> patientid
    
  ]
 );
     return json_encode( ["data"=> $data]) ; 
    
    }

else       return response(  json_encode(  [ 'error'=>['current token doesnot math with the requested patientemail | make sure thta u sent right  type ! {mobile , web}']] )   , 401) ; 

}
function removerecord(request $request   ){
    $type = $request->type ;

    if ($type!='mobile' &&$type !='web' ) $request->type ='mobile' ; $type = $request->type ;  
     $type.='token' ;
    $validator = Validator::make($request->all() , [
   
    'id' =>'required|exists:records',
    
    'apitoken' =>'required'
    
    
    ]    ) ; 
 
     if ($validator->fails() )
 
return response($validator->errors(),400) ;  
     $tokenmail = DB::table('patient')->where($type ,$request->apitoken)->value('id');
     $currentmail =  DB::table('records')->where('id' ,$request->id)->value('patientid');
     if ($tokenmail == $currentmail ) {$user = DB::table('records')->where('id', $request->id)->delete();

     return json_encode( ["data"=> $user]) ; 
    
    }
     else       return response(  json_encode(  [ 'error'=>['current token doesnot math with the requested patientemail | make sure thta u sent right  type ! {mobile , web}']] )   , 401) ; 
     

}

protected $columns = ['id','pseudo','email']; 
   public function scopeExclude($query, $value = []) 
    {
        return $query->select(array_diff($this->columns, (array) $value));
    }
public function doctorrsdata(){

 
    $res = docsignmodel::all();
    $res->makeHidden(['webtoken','email','password' , 'mobiletoken','cv' , 'nationalid']);
    return json_encode( ["data"=> $res]) ; 
}

public function fileUpload(Request $request) {
    $this->validate($request, [
        'input_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    if ($request->hasFile('input_img')) {
        $image = $request->file('input_img');
        $name = time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('/images');
        $image->move($destinationPath, $name);
        $this->save();

        return back()->with('success','Image Upload successfully');
    }
}

function getallrecords (request $request){
    $type = $request->type ;

    if ($type!='mobile' &&$type !='web' ) $request->type ='mobile' ; $type = $request->type ;  
     $type.='token' ;
    $validator = Validator::make($request->all() , [
   
    'patientid' =>'required|exists:patient,id',
    
    'apitoken' =>'required'
    
    
    ]    ) ; 
 
     if ($validator->fails() )
 
return response($validator->errors(),400) ;  
$doc = DB::table('doctor')->where($type ,$request->apitoken)->value($type);
//if ($doc==null)  return "no";

    $tokenmail = DB::table('patient')->where($type ,$request->apitoken)->value('id');
    if (($tokenmail == $request->patientid)|($doc!=null) ) { $user = DB::table('records')->where('patientid', $request->patientid)->get();

    return json_encode( ["data"=> $user]) ; 
}
    
    else       return response(  json_encode(  [ 'error'=>['current token doesnot math with the requested patientemail | make sure thta u sent right  type ! {mobile , web}']] )   , 401) ; 


}

function getallappointments (request $request){
    
   //  return $x[0]['name'];
    $type = $request->type ;

    if ($type!='mobile' &&$type !='web' ) $request->type ='mobile' ; $type = $request->type ;  
     $type.='token' ;
    $validator = Validator::make($request->all() , [
   
    'patientid' =>'required|exists:patient,id',
    
    'apitoken' =>'required'
    
    
    ]    ) ; 
 
     if ($validator->fails() )
 
return response($validator->errors(),400) ;   $map = array();

    $tokenid= DB::table('patient')->where($type ,$request->apitoken)->value('id');
    if ($tokenid == $request->patientid ) { $data = DB::table('appointments')->where('patientid', $request->patientid)->get();
    $doctorsdata =$this->doctorrsdata() ;  $x =json_decode($doctorsdata, true); $x=$x['data'];

for ($i = 0 ; $i<count($x) ; $i++) { 

   $map [$x[$i]['id'].'name'] = $x[$i]['name'];
   $map[$x[$i]['id'].'image'] = $x[$i]['image'];
  }
//   return $map ;
   $res = clinicsmodel::all();
      $x2 =json_decode($res, true);
    //   /return $x2;
      for ($i = 0 ; $i<count($x2) ; $i++) { 

        $map [$x2[$i]['id'].'clinicphone'] = $x2[$i]['mobile'];
        $map[$x2[$i]['id'].'clinicadress'] = $x2[$i]['address'];
       }
    //    return $map;
        $json = json_encode($data);
        $json = json_decode($json,true);
        //return $json ;
     //   return count( $json);
      for ($i = 0 ; $i<count($json) ; $i++) {
         $json[$i]['docname'] = $map[$json[$i]['docid'].'name'] ;
         $json[$i]['docimage'] = $map[$json[$i]['docid'].'image'] ;
         $json[$i]['clinicadress'] = $map[$json[$i]['clinicid'].'clinicadress'] ;
         $json[$i]['clinicphone'] = $map[$json[$i]['clinicid'].'clinicphone'] ;
      }
   
    //   return $json ;
   return json_encode( ["data"=> $json]) ; 
}
    
    else       return response(  json_encode(  [ 'error'=>['current token doesnot math with the requested patientemail | make sure thta u sent right  type ! {mobile , web}']] )   , 401) ; 


}


function editrecord(request $request){
    $type = $request->type ;

    if ($type!='mobile' &&$type !='web' ) $request->type ='mobile' ; $type = $request->type ;  
     $type.='token' ;
    $validator = Validator::make($request->all() , [
   
    'id' =>'required|exists:records',
    
    'apitoken' =>'required|exists:patient,'.$type
    
    
    ]    ) ; 
 
     if ($validator->fails() ) return response($validator->errors(),400) ;  

     $tokenmail = DB::table('patient')->where($type ,$request->apitoken)->value('id');
     $currentmail =  DB::table('records')->where('id' ,$request->id)->value('patientid');
    if ($tokenmail == $currentmail )
     { 
        if ($request ->name!=null) DB::table('records') ->where('id',$request ->id) ->update(['name' => $request ->name  ]   );
        if ($request ->result!=null) DB::table('records') ->where('id',$request ->id) ->update(['result' => $request ->result  ]   );
        if ($request ->resultasimage!=null) {

               $name = "" ; 
               $image = $request->file('resultasimage');
               $name = $image->getClientOriginalName();
            //    $size = $image->getClientSize();
               $destinationPath = public_path('/images');
               $image->move($destinationPath, $name);
               $file_path = $name ; 

            DB::table('records') ->where('id',$request ->id) ->update(['resultasimage' => $file_path  ]   );
        }
         $user = DB::table('records') ->where('id',$request ->id)->first();
           
        
    return json_encode( ["data"=> $user]) ; 
}
    
    else       return response(  json_encode(  [ 'error'=>['current token doesnot math with the requested patientemail | make sure thta u sent right  type ! {mobile , web}']] )   , 401) ; 



}
function getdocbyid(Request $request){

    $validator = Validator::make($request->all() , [ 'id' =>'required'  ]    ) ;     if ($validator->fails() ) return response($validator->errors(),400) ;  

    $data = DB::table('doctor') ->where('id',$request ->id)->first();
    $json = json_encode($data);
    $json = json_decode($json,true);
  
    unset($json['webtoken']);
    unset($json['password']);
    unset($json['mobiletoken']);
    unset($json['cv']);
    unset($json['nationalid']);

     return json_encode( ["data"=> $json]) ; 



}
function getpatientbyid(Request $request){

    $type = $request->type ;

    if ($type!='mobile' &&$type !='web' ) $request->type ='mobile' ; $type = $request->type ;  
     $type.='token' ;
    $validator = Validator::make($request->all() , [ 'patientid' =>'required' ,'apitoken' =>'required|exists:doctor,'.$type ]    ) ;     if ($validator->fails() ) return response($validator->errors(),400) ;  

    $data = DB::table('patient') ->where('id',$request ->patientid)->first();
    $json = json_encode($data);
    $json = json_decode($json,true);
  
    unset($json['webtoken']);
    unset($json['password']);
    unset($json['mobiletoken']);
   // unset($json['cv']);
    unset($json['email']);
     return json_encode( ["data"=> $json]) ; 



}
function getalldocappointments(request $request){

    $type = $request->type ;

    if ($type!='mobile' &&$type !='web' ) $request->type ='mobile' ; $type = $request->type ;  
     $type.='token' ;
    $validator = Validator::make($request->all() , [
    
    //'apitoken' =>'required|exists:doctor,'.$type
    ]    ) ; 
    $tokenmail = Auth::id(); 
return json_encode( ["data"=> $tokenmail]) ;  ; 
 
     if ($validator->fails() ) return response($validator->errors(),400) ;  
     $id =  DB::table('doctor')->where($type ,$request->apitoken)->value('id');

$user = DB::table('appointments')->where('docid', $id)->get();
$json = json_encode($user);
$json = json_decode($user,true);

    return json_encode( ["data"=> $json]) ; 
}

function rateadoctor(request $request){

    $type = $request->type ;

    if ($type!='mobile' &&$type !='web' ) $request->type ='mobile' ; $type = $request->type ;  
     $type.='token' ;
    $validator = Validator::make($request->all() , [
    
    'apitoken' =>'required|exists:patient,'.$type,
    'rating' =>'required',
    'docid' =>'required'

    ]    ) ; 
       if ($validator->fails() ) return response($validator->errors(),400) ;  
       if ($request->rating>5|| $request->rating<1 ) return response(json_encode( ['error'=>["rate must be between 1 and 5 !"]])  ,  400) ;  
       $oldrating = DB::table('doctor')->where('id' ,$request->docid)->value('rating');
       $preratecount = DB::table('doctor')->where('id' ,$request->docid)->value('raters');
       $preratecount ++ ; 
       $currentrate = ($oldrating+$request->rating) /($preratecount*5); 

       DB::table('doctor') ->where('id',$request ->docid) ->update(['rating' => $oldrating+$request->rating ]  ) ; 
       DB::table('doctor') ->where('id',$request ->docid) ->update(['raters' => $preratecount ]  ) ; 
// return response(json_encode($currentrate) ,200) ;
return json_encode( ["data"=> $currentrate]) ; 
 
    }

    function clinics (Request $request){
        $clincis = json_encode( $request->clinics);
        $data = json_decode( $clincis ); 
        $clarr =  json_decode($data);
        foreach($clarr as $object)
        {
            $arrays[] = json_decode(json_encode($object),true);
        }
        $users = $arrays;
$larr =array(); $larr['clinics']=$users; $larr['docid']=$request->docid;
//  $x =dd($users);
      // return response ( var_dump($users[0][0]));
      
        $validator = Validator::make($larr, [
    
            'docid' =>'required|exists:doctor,id',
            'clinics' =>'required',
         
        
            ]    ) ; 
               if ($validator->fails() ) return response($validator->errors(),400) ;  
       // return $request->clinics[0][''];
$dlete = DB::table('clinics')->where('docid', $request->docid)->delete();

for ($i = 0 ; $i< count( $users[0]) ; $i++)
{
     
        // for ($y = 0 ; $y< count( $request->clinics[$i]['days']) ; $y++)
        // {
               $data = clinicsmodel::create([ 'docid' =>$request->docid ,
        'day' =>json_encode($users[0] [$i]['days']),
        'mobile' =>$users[0][$i]['phone'] ,
        'address' =>json_encode($users[0][$i]['address']) 
        // 'endtime' =>$request -> clinics[$i]['days'][$y]['end_time'] ,
        // 'starttime' =>$request -> clinics[$i]['days'][$y]['start_time']  ,
    
    
     ]
    );

    

}$data = DB::table('clinics')->where('docid' ,$request->docid)->get();


 return json_encode( ["data"=> $data]) ; 
    }
     function getdocclinics (request $request){
  
        $validator = Validator::make($request->all() , [
    
            //'apitoken' =>'required|exists:doctor,'.$type
            'docid'=>'required|exists:doctor,id'
            ]  
            
            ) ;  if ($validator->fails() ) return response($validator->errors(),400) ;  
            // $tokenmail = DB::table('doctor')->where($type ,$request->apitoken)->value('id');
            // if ($tokenmail != $request->docid ) 
            // return response(  json_encode(  [ 'error'=>['current token doesnot math with the requested patientemail | make sure thta u sent right  type ! {mobile , web}']] )   , 401) ; 
            $data = DB::table('clinics')->where('docid' ,$request->docid)->get();


            return json_encode( ["data"=> $data]) ; 

    }
  function updateclinic (Request $request){   $type = $request->type ;

        if ($type!='mobile' &&$type !='web' ) $request->type ='mobile' ; $type = $request->type ;  
         $type.='token' ;
        $validator = Validator::make($request->all() , [
    
            'apitoken' =>'required|exists:doctor,'.$type,
            'clinicid'=>'required|exists:clinics,id',
          //  'clinicdata'=>'required'
            ]  
            
            ) ;  
            $doc = DB::table('doctor')->where($type ,$request->apitoken)->value('id');
            $currentmail =  DB::table('clinics')->where('id' ,$request->clinicid)->value('docid');
           // $tokenmail =  DB::table('clinics')->where('id' ,$request->clinicid)->value('id');

            if ($doc != $currentmail ) 
            // ( json_encode(  [ 'error'=>['starttime formate must be H']] ) ,400 ); 
             return response(  json_encode(  [ 'error'=>['current token doesnot math with the requested patientemail | make sure thta u sent right  type ! {mobile , web}']] )   , 401) ; 

            if ($validator->fails() ) return response($validator->errors(),400) ;  
            if ($request ->days!=null)  DB::table('clinics') ->where('id',$request ->clinicid) ->update(['day' => $request ->days  ]   );
            if ($request ->mobile!=null)  DB::table('clinics') ->where('id',$request ->clinicid) ->update(['mobile' => $request ->mobile  ]   );
            if ($request ->address!=null)  DB::table('clinics') ->where('id',$request ->clinicid) ->update(['address' => $request ->address  ]   );



$user=DB::table('clinics') ->where('id',$request ->clinicid)->get();
            return   json_encode( ["data"=> $user]) ; 
    }
   function deleteclinic (Request $request){   $type = $request->type ;

        if ($type!='mobile' &&$type !='web' ) $request->type ='mobile' ; $type = $request->type ;  
         $type.='token' ;
        $validator = Validator::make($request->all() , [
    
            'apitoken' =>'required|exists:doctor,'.$type,
            'clinicid'=>'required|exists:clinics,id',
            // 'clinicdata'=>'required'
            ]  
            
            ) ;  

            if ($validator->fails() ) return response($validator->errors(),400) ;  
            $doc = DB::table('doctor')->where($type ,$request->apitoken)->value('id');
            $currentmail =  DB::table('clinics')->where('id' ,$request->clinicid)->value('docid');
           // $tokenmail =  DB::table('clinics')->where('id' ,$request->clinicid)->value('id');
    

            if ($doc != $currentmail ) 
            // ( json_encode(  [ 'error'=>['starttime formate must be H']] ) ,400 ); 
             return response(  json_encode(  [ 'error'=>['current token doesnot math with the requested patientemail | make sure thta u sent right  type ! {mobile , web}']] )   , 401) ; 
               if (appointments::where('clinicid', $request->clinicid)->exists()) {
               return response(  json_encode(  [ 'error'=>['u cant delete this clinic until patient appointment ended']] )   , 401) ; 
 
          
        }
           $user = DB::table('clinics')->where('id', $request->clinicid)->delete();

            return   json_encode( ["data"=> $user]) ; 

    }
}
