<?php

namespace App\Http\Controllers\API;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController;

class PostController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['posts'] = Post::where(["username" => auth()->user()->id])->get();
        return $this->sendResponse('All post data fetched successfully', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatePost = Validator::make(
            $request->all(),
            [
                'title' => 'required',
                'description' => 'required',
                'image' => 'required|mimes:png,jpg,jpeg,gif',
            ]
        );

        if ($validatePost->fails()) {
            return $this->sendError('Validation error', $validatePost->errors()->all(), 401);
        }

        $img = $request->image;
        $ext = $img->getClientOriginalExtension();
        $imageName = time() . "." . $ext;
        $img->move(public_path() . "/uploads", $imageName);

        //Here we are not storing the image directly in the database . We had created a unique name for each uploaded image and stored it in the db . we had stored the original image in the uploads folder 

        $post = Post::create([
            'username' => auth()->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imageName
        ]);

        return $this->sendResponse('Post created successfully', $post);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data['post'] = Post::select(
            'id',
            'title',
            'description',
            'image'
        )->where(['id' => $id,'username'=> auth()->user()->id])->get();

        return $this->sendResponse('Your single post returned successfully', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatePost = Validator::make(
            $request->all(),
            [
                'title' => 'required',
                'description' => 'required',
                'image' => 'nullable|image|mimes:png,jpg,jpeg,gif',
            ]
        );

        if ($validatePost->fails()) {
            return $this->sendError('Validation error', $validatePost->errors()->all(), 401);
        }

        //The below code deletes the old image and saves the new image in the uploads folder

        $postImage = Post::select('id', 'image')->where(['id' => $id])->get();

        if ($request->image != '') {
            $path = public_path() . '/uploads/';
            if ($postImage[0]->image != '' && $postImage[0]->image != null) {
                $old_file = $path . $postImage[0]->image;
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
            $img = $request->image;
            $ext = $img->getClientOriginalExtension();
            $imageName = time() . "." . $ext;
            $img->move(public_path() . "/uploads", $imageName);
        } else {
            $imageName = $postImage[0]->image;
        }



        $post = Post::where(['id' => $id])->update([
            'username' => auth()->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imageName
        ]);
        return $this->sendResponse('Post Updated successfully', $post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $imageName = Post::select('image')->where(['id' => $id,'username' => auth()->user()->id])->get();
        $filePath = public_path() . '/uploads/' . $imageName[0]['image'];
        unlink($filePath);

        $post = Post::where(['id' => $id,'username' => auth()->user()->id])->delete(); //deletes the post details
        //the below code delete the post image

        return $this->sendResponse('Your post has been removed', $post);
    }
}
