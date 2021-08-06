<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Validator;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;

class paypalcontroller extends Controller
{
    public function index(Request $request){  
        //   $type = $request->type ;
        // if ($type!='mobile' &&$type !='web' ) $request->type ='mobile' ; $type = $request->type ;  
        // $type.='token' ;
        // $validator = Validator::make($request->all() , [ 
        //     'cost' =>'required' ,
        //     'appointmetid' =>'required|exists:appointments,id' ,
        //     'apitoken' =>'required|exists:patient,'.$type

        
        // ]    ) ; 
        //     if ($validator->fails() ) return response($validator->errors(),400) ;  
        //     $tokenmail = DB::table('patient')->where($type ,$request->apitoken)->value('id');
        //     $currentmail =  DB::table('appointments')->where('id' ,$request->appointmetid)->value('patientid');
        //     if ($tokenmail != $currentmail ) 
        //     // ( json_encode(  [ 'error'=>['starttime formate must be H']] ) ,400 ); 
        //      return response(  json_encode(  [ 'error'=>['current token doesnot math with the requested patientemail | make sure thta u sent right  type ! {mobile , web}']] )   , 401) ; 
$cost = $request->cost ; 
  $apid = $request->appointmetid;
          $clientId = "AT-daPKau8630hNK-kiGgQxDZnFOKBsj3PrsFl536jjkPGtSo3b5kaxrHZH7zBinY5Ds7kthWWMs__1_";
        $clientSecret = "EPdd_DYj29_ybAsapAvyffO5foA7ugKQc0EhT2dlUVUVxpFqCqzmJHuNyjQ5PZCTVSI7KY-xzxWBlPt0";
    //     $clientId = "ARFXTUHalVMfGcMJP2BlNchnL0zoFWcXvfsQEWI29vDzzNtyUJDXJwr8vcKNlh4yIL7f_yY5yPBucY5C";
    // $clientSecret = "ECoSr9WF5s4CGdehszuJPc6XCNDeqlmnW12GocHWL6sJVBKHVeOLh6v1BZwcbaUthS0bTbQjYPVBk0UR";
        $environment = new  SandboxEnvironment ($clientId, $clientSecret);
        $client = new PayPalHttpClient($environment);
    $request = new OrdersCreateRequest();
$request->prefer('return=representation');
$request->body = [
                     "intent" => "CAPTURE",
                     "purchase_units" => [[
                         "reference_id" => "test_ref_id1",
                         "amount" => [
                             "value" =>$cost,
                             "currency_code" => "USD"
                         ]
                     ]],
                     "application_context" => [
                          "cancel_url" => route('paypal_cancel'),
                          "return_url" => route('paypal_return',['id'=>$apid])
                     ] 
                 ];

try {
    // Call API with your client and get a response for your call
    $response = $client->execute($request);
    
    // If call returns body in response, you can get the deserialized version from the result attribute of the response
    return json_encode( ["data"=> $response->result->links[1]-> href]);

}
catch (HttpException $ex) {
    echo $ex->statusCode;
    print_r($ex->getMessage());
}}

public function paypalReturn(Request $request){           //   dd(\request()->all());

    // $clientId = "ARFXTUHalVMfGcMJP2BlNchnL0zoFWcXvfsQEWI29vDzzNtyUJDXJwr8vcKNlh4yIL7f_yY5yPBucY5C";
    // $clientSecret = "ECoSr9WF5s4CGdehszuJPc6XCNDeqlmnW12GocHWL6sJVBKHVeOLh6v1BZwcbaUthS0bTbQjYPVBk0UR";
    $clientId = "AT-daPKau8630hNK-kiGgQxDZnFOKBsj3PrsFl536jjkPGtSo3b5kaxrHZH7zBinY5Ds7kthWWMs__1_";
    $clientSecret = "EPdd_DYj29_ybAsapAvyffO5foA7ugKQc0EhT2dlUVUVxpFqCqzmJHuNyjQ5PZCTVSI7KY-xzxWBlPt0";
    $environment = new SandboxEnvironment($clientId, $clientSecret);
    $client = new PayPalHttpClient($environment);
    $request = new OrdersCaptureRequest($_GET['token']);
    
$request->prefer('return=representation');
try {
    // Call API with your client and get a response for your call
    $response = $client->execute($request);
    
    // If call returns body in response, you can get the deserialized version from the result attribute of the response
    // dd($response);
     DB::table('appointments') ->where('id',$_GET['id']) ->update(['paid' => 1  ]   );
    return "thak u for using careve , order paid successfully";
}catch (HttpException $ex) {
    echo $ex->statusCode;
    print_r($ex->getMessage());
}

}

        public function paypalCancel(){
            return "order canceled";
        }
    
}
