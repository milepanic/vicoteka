<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'content'   => 'required|string',
            'category'  => 'required',
            'original'  => 'nullable|boolean',
        ]);

    	Post::create([
    		'content'       => $request->content,
    		'category_id'   => $request->category,
    		'original'      => $request->original,
            'user_id'       => Auth::id()
    	]);

    	return redirect('submit');
    }

    public function view($id)
    {
        $post       = Post::with('category', 'user')->find($id);
        $user       = Auth::user();
        $comments   = Comment::with('user')->where('post_id', $post->id)->get();

        return view('pages.view', compact('post', 'user', 'comments'));
    }

    public function edit($id)
    {
        return view('pages.edit')->withPost(Post::find($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'content'   => 'required|string',
            'original'  => 'nullable|boolean',
        ]);

        $post = Post::find($id);
        $user = $request->user();

        $post->content  = $request->content;
        $post->original = $request->original ? 1 : null;

        $post->save();

        // return redirect('v/' . $post->id); ?????
        return view('pages.view', compact('post', 'user'));
    }

    public function delete($id)
    {
        Post::destroy($id);

        return redirect('/');
    }

    public function vote(Request $request)
    {
        $id     = $request->id;
        $type   = $request->type;
        $post   = Post::find($id);

        if ($type === 'upvote')
            $vote = 1;
        elseif ($type === 'downvote')
            $vote = -1;

        // ako korisnik nije glasao - upisi $vote u tabelu
        if (!$post->votedBy($request->user())->count() > 0) {
            $request->user()->postVote()->attach($id, ['vote' => $vote]);

            return response()->json('Vas glas je upisan');
        }

        // ako korisnik zeli da ponisti svoj vote (vote === $vote) - obrisi red iz tabele
        if ($post->votedBy($request->user())->first() === $vote) 
            $request->user()->postVote()->detach($id);
        
        // ako korisnik promijeni vote (vote !== $vote) - update-uj vote
        elseif ($post->votedBy($request->user())->first() !== $vote) 
            $request->user()->postVote()->updateExistingPivot($id, ['vote' => $vote]);
        
        return response()->json('Vas glas je promijenjen');
    }

    public function favorite(Request $request)
    {
        $id = $request->id;

        $request->user()->favoritePosts()->toggle($id);

        return response()->json('Vic je dodat u omiljene');
    }
}
