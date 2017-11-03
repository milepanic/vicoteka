<?php

namespace App\Http\Controllers;

use App\Category;
use App\Comment;
use App\Post;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $comments = Comment::all();

        if(Auth::check()) {
            $blocked = DB::table('category_user_block')->where('user_id', $user->id)->get()->pluck(['category_id'])->toArray();
            $posts = Post::whereNotIn('category_id', $blocked)->latest()->paginate(10);
        } else {
            $posts = Post::latest()->paginate(10);
        }
        
        return view('pages.welcome', compact('user', 'posts', 'comments'));
    }

    public function profile()
    {
        return view('pages.profile');
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
