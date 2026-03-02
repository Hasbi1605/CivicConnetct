<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Show a single post with comments.
     */
    public function show(Post $post)
    {
        // Only show approved posts (or own pending/rejected posts)
        if (!$post->isApproved() && $post->user_id !== Auth::id() && !Auth::user()->isAgent()) {
            abort(404);
        }

        $post->load('user', 'votes');

        // Top-level comments sorted by top count, with replies
        $comments = $post->comments()
            ->whereNull('parent_id')
            ->with(['user', 'tops', 'replies' => function ($q) {
                $q->with(['user', 'tops'])->oldest();
            }])
            ->withCount('tops')
            ->orderByDesc('tops_count')
            ->orderByDesc('created_at')
            ->get();

        return view('posts.show', compact('post', 'comments'));
    }

    /**
     * Store a new post.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
            'category' => ['required', 'in:artikel,fact-check'],
            'image' => ['nullable', 'image', 'max:2048'],
            'citation_texts' => ['nullable', 'array', 'max:5'],
            'citation_texts.*' => ['nullable', 'string', 'max:500'],
            'citation_urls' => ['nullable', 'array', 'max:5'],
            'citation_urls.*' => ['nullable', 'string', 'max:500'],
        ]);

        $data = [
            'user_id' => Auth::id(),
            'body' => $validated['body'],
            'category' => $validated['category'],
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        // Build citations array from parallel inputs
        $citations = [];
        if (!empty($validated['citation_texts'])) {
            foreach ($validated['citation_texts'] as $i => $text) {
                if (!empty($text)) {
                    $citations[] = [
                        'text' => $text,
                        'url' => $validated['citation_urls'][$i] ?? null,
                    ];
                }
            }
        }
        if (!empty($citations)) {
            $data['citations'] = $citations;
        }

        $data['status'] = 'pending';

        Post::create($data);

        return redirect()->route('home')->with('success', 'Postingan berhasil dikirim! Menunggu peninjauan CIVIC Agent sebelum dipublikasikan.');
    }

    /**
     * Delete a post (agents only).
     */
    public function destroy(Post $post)
    {
        $user = Auth::user();

        if (!$user->isAgent()) {
            abort(403, 'Hanya CIVIC Agent yang dapat menghapus postingan.');
        }

        // Delete associated image if exists
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        // Notify the post owner
        if ($post->user_id !== $user->id) {
            Notification::create([
                'user_id' => $post->user_id,
                'type' => 'post_deleted',
                'title' => 'Postingan Dihapus',
                'message' => 'Postingan Anda telah dihapus oleh CIVIC Agent karena melanggar ketentuan komunitas.',
                'data' => json_encode(['post_body' => \Illuminate\Support\Str::limit($post->body, 100)]),
            ]);
        }

        $post->votes()->delete();
        $post->reports()->delete();
        $post->comments()->each(function ($comment) {
            $comment->tops()->delete();
        });
        $post->comments()->delete();
        $post->endorsements()->delete();
        $post->delete();

        return redirect()->route('home')->with('success', 'Postingan berhasil dihapus.');
    }
}
