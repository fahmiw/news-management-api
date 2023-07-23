<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use Auth;
use Redis;

class CommentController extends Controller
{
    public function create(Request $request) {
        try {
            $request->validate([
                "content" => "required|string|max:500", 
                "news_id" => "required",
            ]);
            $news = News::find($request->news_id);

            if (!$news) {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "News not found"
                ], 404);
            }

            $commentData = [
                "content" => $request->content, 
                "news_id" => $request->news_id,
                "user_id" => Auth::user()->id
            ];

            $commentId = self::getCommentId();

            // Save the comment data to Redis with the specified comment ID
            $commentKey = "comment:$commentId";
            Redis::hmset($commentKey, $commentData);

            // Add the comment ID to the set for the corresponding news_id
            $newsCommentsKey = "news_comments:{$request->input('news_id')}";
            Redis::sadd($newsCommentsKey, $commentId);

            return response()->json([
                "statusCode" => 200,
                "message" => "Successfully Created Comment!",
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                "statusCode" => 400,
                "message" => $e->getMessage(),
            ], 400);
        }
    }

    static function getCommentId() {
        if(!Redis::exists('comment')) {
		   Redis::set('comment',0);  
        }
		   
		return Redis::incr('comment'); 
    }
}
