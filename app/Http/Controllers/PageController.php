<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    public function home()
    {
        // Public feed: only approved posts
        $posts = Post::with('user', 'votes')
            ->withCount(['comments', 'endorsements'])
            ->approved()
            ->latest()
            ->paginate(10);

        // Current user's pending/rejected posts (so they can see status)
        $myPendingPosts = collect();
        $myRejectedPosts = collect();
        if (Auth::check()) {
            $myPendingPosts = Post::where('user_id', Auth::id())
                ->pending()
                ->latest()
                ->get();
            $myRejectedPosts = Post::where('user_id', Auth::id())
                ->where('status', 'rejected')
                ->latest()
                ->get();
        }

        return view('home', compact('posts', 'myPendingPosts', 'myRejectedPosts'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('profile', compact('user'));
    }

    public function submitReport(Request $request)
    {
        $validated = $request->validate([
            'claim' => 'required|string|max:1000',
            'source' => 'nullable|url|max:500',
        ]);

        // TODO: Store report to database

        return back()->with('success', 'Laporan berhasil dikirim ke tim verifikasi');
    }
}
