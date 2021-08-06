<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Response;
use Validator;
use Auth;
class ArticleController extends Controller
{
    public function get_all()
    {
  $res = DB::table('articles')->get(); 
   // $res->makeHidden(['webtoken','email','password' , 'mobiletoken','cv' , 'nationalid']);
    return json_encode( ["data"=> $res]) ; 
    }

    public function write_article(Request $request)
{
    $type = $request->type ;

        if ($type!='mobile' &&$type !='web' ) $request->type ='mobile' ; $type = $request->type ;  
         $type.='token' ;
        $validator = Validator::make($request->all() , [
    
            'apitoken' =>'required|exists:doctor,'.$type,
             'title' => 'required',
            'body' => 'required',

        ]);
           if ($validator->fails() ) return response($validator->errors(),400) ;  
        else {
            $docid =    DB::table('doctor')->where($type ,$request->apitoken)->value('id');

            $article = new Article();
            $article->title = $request->title;
            $article->body = $request->body;
             $article->author_id = $docid;
            if ($request->file('image') == NULL) {
                $article->image = 'placeholder.png';
            } else {
                $filename = Str::random(20) . '.' . $request->file('image')->getClientOriginalExtension();
                $article->image = $filename;
                $request->image->move(public_path('images'), $filename);
            }
            if ($request->videolink!= NULL) {
                $article->links = $request->videolink;
            }
            $article->save();
            return $article ;
            //return Response::json(['success' => 'Article created successfully !']);
        }
    }

    //get article by id
    public function get_article_by_id(request $request)
    {
     $validator = Validator::make($request->all() , [ 'id' =>'required'  ]    ) ;    
     if ($validator->fails() ) return response($validator->errors(),400) ;  

    $data = DB::table('articles') ->where('id',$request ->id)->first();


     return json_encode( ["data"=> $data]) ; 
    }

    //search by title
    public function get_article_by_word(Request $request)
    {
        $articles = Article::where('title', 'LIKE', '%' . $request->keyword . '%')->get();
        if (count($articles) == 0) {
            return Response::json(['message' => 'No article match found !']);
        } else {
            return Response::json($articles);
        }
    }

    public function update_article(Request $request, $id)
    {
        $validators = Validator::make($request->all(), [
            'title' => 'required',
            'body' => 'required'
        ]);
        if ($validators->fails()) {
            return Response::json(['errors' => $validators->getMessageBag()->toArray()]);
        } else {
            $article = Article::where('id', $request->id)->where('author_id', Auth::user()->id)->first();
            if ($article) {
                $article->title = $request->title;
                $article->author_id = Auth::user()->id;
                $article->body = $request->body;
                if ($request->file('image') == NULL) {
                    $article->image = 'placeholder.png';
                } else {
                    $filename = Str::random(20) . '.' . $request->file('image')->getClientOriginalExtension();
                    $article->image = $filename;
                    $request->image->move(public_path('images'), $filename);
                }
                $article->save();
                return Response::json(['success' => 'Article updated successfully !']);
            } else {
                return Response::json(['error' => 'Article not found  or you not article writer']);
            }
        }
    }

    //destroy Article
    public function destroy_article(Request $request)
    {
        try {
            $article = Article::where('id', $request->id)->where('author_id', Auth::user()->id)->first();
            if ($article) {
                $article->delete();
                return Response::json(['success' => 'Article removed successfully !']);
            } else {
                return Response::json(['error' => 'Article not found or you not article writer']);
            }
        } catch (\Illuminate\Database\QueryException $exception) {
            return Response::json(['error' => 'Article belongs to comment.So you cann\'t delete this article!']);
        }
    }

    // fetch comments for a specific article
    public function comments($id){
        if(Article::where('id',$id)->first()){
            return CommentResource::collection(Comment::where('article_id',$id)->get());
        }else{
            return Response::json(['error'=>'Article not found!']);
        }
    }
    /*public function is_user_article($article)
    {
        if ( Auth::id() !== $article->user_id ) {
            throw new NotUserPost;
        }
        return true;
    }*/
}
