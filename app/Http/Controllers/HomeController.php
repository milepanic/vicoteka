<?php

namespace App\Http\Controllers;

use App\Category;
use App\Comment;
use App\Post;
use App\User;
use Auth;
use Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // $request->user()
        $user = Auth::user();

        if(Auth::check()) {
            // Gets all posts which category the user did not block
            // Gets 'id' of where blocked = 1 from pivot table and shows posts which category does not have that 'id'
            $blocked = $user->categories()->where('blocked', 1)->get()->pluck(['id'])->toArray();
            $posts = Post::whereNotIn('category_id', $blocked)->with('comments.user', 'user', 'category')->latest()->paginate(10);
        } else {
            $posts = Post::with('user', 'category')->latest()->paginate(10);
        }        
        return view('pages.welcome', compact('user', 'posts', 'comments.user'));
    }

    public function profile($slug)
    {
        $user = User::where('slug', $slug)->first();
        // $posts = $user->posts()->get();
        $posts = $user->favoritePosts()->get();
        // withCount()

        return view('pages.profile', compact('user', 'posts'));
    }

    public function edit($slug)
    {
        if(Auth::user()->slug !== $slug)
            abort(403); // postaviti gate

        $user = Auth::user()->where('slug', $slug)->first();

        return view('pages.editUser', compact('user'));
    }

    public function update($id, Request $request)
    {
        if(!Auth::user()->id === $id)
            abort(403);
            
        $user = Auth::user()->find($id);

        $name = request('name');
        $email = request('email');
        $description = request('description');
        $slug = str_slug($name);

        $user->name = $name;
        $user->email = $email;
        $user->description = $description;
        $user->slug = $slug;

        if($request->file('image') !== null)
        {
            $image = $request->file('image');
            $location = public_path('images/users/' . $user->id);

            Image::make($image)->resize(300, 300)->save($location);           
        }

        $user->save();

        return redirect('profile/' . $user->slug);
    }

    public function submit()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        return view('pages.submit', compact('categories'));
    }

    public function create()
    {
        return view('pages.create');
    }


    // ADMIN ROUTES

    public function dashboard()
    {
        $userCount = User::count();
        $postCount = Post::count();
        $originalPostCount = Post::where('original', '=', '1')->count();

        return view('admin.dashboard', compact('userCount', 'postCount', 'originalPostCount'));
    }

    public function users()
    {
        $users = User::all();
        
        return view('admin.users', compact('users'));;
    }

    public function posts()
    {
        $posts = Post::all();
        return view('admin.posts', compact('posts'));
    }

    public function categories()
    {
        $categories = Category::all();

        return view('admin.categories', compact('categories'));
    }

    public function medals()
    {
        return view('admin.medals');
    }
}
