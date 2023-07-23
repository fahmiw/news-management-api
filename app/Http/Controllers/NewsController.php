<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use App\Models\User;
use App\Events\NewsActivity;

use Auth;
use Storage;
use Image;
use DB;
use Exception;
use Redis;

class NewsController extends Controller
{
    public function create(Request $request) {
        try {
            DB::beginTransaction();
            $request->validate([
                "title" => "required|string",
                "content" => "required|string",
                "image_file" => "required"
            ]);

            $filename = NULL;
            if($request->file('image_file') != NULL) {
                $file       = $request->file('image_file');
                $filename   = 'img_' . date('Ymd') . '_NEWS_' . time() . '.' .$file->getClientOriginalExtension();
                $path       = '/app/public/assets/news/' . $filename;

                if (!Storage::disk('public')->exists('assets/news'))
                {  
                    Storage::disk('public')->makeDirectory('assets/news');
                }

                $imageFit   = Image::make($file);
                $imageFit->save(storage_path($path));
            }

            $news = News::create([
                "title" => $request->title,
                "content" => $request->content,
                "image_name" => $filename,
                "user_id" => Auth::user()->id
            ]);
            event(new NewsActivity('created', $news, auth()->user()));
            DB::commit();

            return response()->json([
                "statusCode" => 200,
                "message" => "Successfully Created News!",
                "data" => $news
            ], 200);

        } catch (\Exception $e) {
            if(isset($filename)) {
                Storage::disk('public')->delete('assets/news/'. $filename);
            }
            DB::rollback();
            return response()->json([
                "statusCode" => 400,
                "message" => $e->getMessage(),
            ], 400);
        }
    }

    public function update(Request $request, $id) {
        try {
            DB::beginTransaction();
            $news = News::find($id);

            if($news == NULL) {
                throw new Exception('News Not Found');
            }
            
            $news->title = $request->title;
            $news->content = $request->content;

            $filename = NULL;
            if($request->image_file != NULL) {
                if(Storage::disk('public')->exists('assets/news/' . $news->image_name)) {
                    Storage::disk('public')->delete('assets/news/' . $news->image_name);
                } 
                $extension = explode('/', explode(':', substr($request->image_file, 0, strpos($request->image_file, ';')))[1])[1];
                $filename   = 'img_' . date('Ymd') . '_NEWS_' . time() . '.' . $extension;
                $replace = substr($request->image_file, 0, strpos($request->image_file, ',')+1); 

                $image = str_replace($replace, '', $request->image_file);
                $image = str_replace(' ', '+', $image); 

                $path       = '/app/public/assets/news/' . $filename;

                if (!Storage::disk('public')->exists('assets/news'))
                {  
                    Storage::disk('public')->makeDirectory('assets/news');
                }

                $imageFit   = Image::make(base64_decode($image));
                $imageFit->save(storage_path($path));
                $news->image_name = $filename;
            }

            $news->save();
            event(new NewsActivity('updated', $news, auth()->user()));

            DB::commit();

            return response()->json([
                "statusCode" => 200,
                "message" => "Successfully Updated News!",
                "data" => $news
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                "statusCode" => 400,
                "message" => $e->getMessage(),
            ], 400);
        }
    }

    public function delete($id) {
        try {
            DB::beginTransaction();
            $news = News::find($id);
            
            if($news == NULL) {
                throw new Exception('News Not Found');
            }

            if (!Storage::disk('public')->exists('assets/news/'. $news->image_name))
            {  
                Storage::disk('public')->delete('assets/news/'. $news->image_name);
            }
            $news->delete();
            event(new NewsActivity('deleted', $news, auth()->user()));

            DB::commit();

            return response()->json([
                "statusCode" => 200,
                "message" => "Successfully Delete News!"
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                "statusCode" => 400,
                "message" => $e->getMessage(),
            ], 400);
        }
    }

    public function getAll() {
        try {
            $news = News::paginate(10);

            return response()->json([
                "statusCode" => 200,
                "message" => "Success get news detail",
                "data" => $news
            ]);

        } catch (\Exception $e) {
            return response()->json([
                "statusCode" => 400,
                "message" => $e->getMessage(),
            ], 400);
        }
    }

    public function detail($id)
    {
        try {
            $news = News::find($id);

            if (!$news) {
                return response()->json([
                    "statusCode" => 404,
                    "message" => "News not found"
                ], 404);
            }

            // Fetch comment IDs associated with the news from the set in Redis
            $newsCommentsKey = "news_comments:$id";
            $commentIds = Redis::smembers($newsCommentsKey);

            $comments = [];
            foreach ($commentIds as $commentId) {
                // Fetch the comment data using the comment ID
                $redisKey = "comment:$commentId";
                $commentData = Redis::hgetall($redisKey);
                $user = User::find($commentData['user_id']);

                $comments[] = [
                    "content" => $commentData['content'],
                    "user_name" => $user->name,
                    "role" => ($user->role == 1 ? "Admin" : "User")
                ];
            }

            // Combine news and comments data
            $newsWithComments = [
                "news" => $news,
                "comments" => $comments,
            ];

            return response()->json([
                "statusCode" => 200,
                "message" => "Success get news detail",
                "data" => $newsWithComments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "statusCode" => 400,
                "message" => $e->getMessage(),
            ], 400);
        }
    }
}
