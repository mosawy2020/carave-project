<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Message;
use App\User;

use function PHPUnit\Framework\is_null;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return Message::get()->all();

    }
    public function fetchUsers() {
        $contacts = User::where('id', '!=', auth()->user()->id)->orderBy('name')->get();

        return response()->json($contacts);
    }
    public function fetchMessages($id) {
        Message::where('user_id', $id)->where('receiver_id', auth()->user()->id)->update(['is_read' => true]);
        $messages = Message::where(function($query) use ($id){
            $query->where('receiver_id', $id)->where('user_id', auth()->user()->id);
        })
            ->orWhere(function($query) use ($id) {
                $query->where('user_id', $id)->where('receiver_id', auth()->user()->id);
            })->orderBy('created_at', 'DESC')
            ->get();
        return response()->json($messages);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $recivertype ="doctor" ; 
        $request->merge(['id' => $request->receiver_id]);

        // return response()->json(auth()->user()->nationalid);
     if (auth()->user()->nationalid!=null)  $recivertype = "users" ; 

        $fileName='';
         $this->validate($request, [
             'messages' => 'nullable',
             'id'=>'required|exists:'.$recivertype,
             'file'=>'nullable|mimes:pdf,jpg,bmp,png',
         ]);

        // if($request->receiver_id==auth()->user()->id)
        // {
        //     return response()->json('unauthorized,sender id == receiver id',401);

        // }
        
        if ($request->hasFile('file')){
            $fileName = hexdec(uniqid()) . "." . $this->file->extension();
            $request->file('file')->storeAs('media/',$fileName,'public');
        }
        if(null==($request->messages)&&null==($request->file))
        {
            return response()->json('empty message', 400);

        }else{
            $message = Message::create([
                'messages' => $request->messages,
                'user_id' => auth()->user()->id,
                'receiver_type'=>$recivertype,
                'receiver_id' => $request->receiver_id,
                'file' => $fileName,
            ]);
    
            return response()->json($message, 201);
        }
       
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
    // Output all messages for the user with the information of the friend
    public function getAllConversation(Request $request)
    {
        $id=$request->id;

        $conversations[] = Message::with('receiver')->where('user_id', $id)->get();
        $conversations[] = Message::with('sender')->where('receiver_id', $id)->get();

        $allConversations = json_decode(json_encode($conversations), true);

        return response()->json($allConversations, 200);

    }
    public function allConversationForDetectUser(Request $request){
        $id=auth()->user()->id;
        $conversations[] =Message::where([['user_id', $id],['receiver_id',$request->receiver_id]])
            ->orWhere([['user_id', $request->receiver_id],['receiver_id',$id]])->get();
        return response()->json($conversations);
    }

    public function privateMessage(Request $request)
    {
        $userId = $request->user;
        $friend = $request->friend;

        // Get messages for private chat
        $messages = Message::where(['user_id' => $userId, 'receiver_id' => $friend])
            ->orWhere(function($query) use($userId, $friend) {
                $query->where(['user_id' => $friend, 'receiver_id' => $userId]);
            })->get();

        return response()->json($messages, 200);

    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $message = Message::findOrFail($id);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $message = Message::findOrFail($id);

        $message->delete();

        return ['message' => 'Deleted successfully'];
    }
    public function notification() {


        $SERVER_API_KEY = 'AAAAtBjzmgM:APA91bE84_YjijVjZA03CSX1t428UCHIX37_oaUirpwEhz2FaWqcvg21JMO_A0Z-FSpv24IQQ-jMIqQp5J2Os9v3CYx0qsMDrrDcDL4jeaLIWWoUrJ69GarnzOSNp87IqNYIJiWyr0nu	';
    
        $token_1 = 'dSWna2wRSrCqI3pjdcOoG6:APA91bHw-ObC8F28wePI5sAeLYJl6FGfiDUBSuxCvP0QolSm7umzCV97KQGbIBr1Xu0SXCo_8fcrtFJ478HsifGbWd4NJPbP9ibPvy1u6fWEDYezorYnffuO8xVri6UcI5jE5j6kVJNz';
    
        $data = [
    
            "registration_ids" => [
                $token_1
            ],
    
            "notification" => [
    
                "title" => 'Welcome',
    
                "body" => 'Description',
    
                "sound"=> "default" // required for sound on ios
    
            ],
    
        ];
    
        $dataString = json_encode($data);
    
        $headers = [
    
            'Authorization: key=' . $SERVER_API_KEY,
    
            'Content-Type: application/json',
    
        ];
    
        $ch = curl_init();
    
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    
        curl_setopt($ch, CURLOPT_POST, true);
    
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
    
        $response = curl_exec($ch);
    }
}