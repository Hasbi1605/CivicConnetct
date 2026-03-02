<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentTop;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Store a new comment on a post (AJAX).
     */
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'body' => ['required', 'string', 'max:2000'],
            'parent_id' => ['nullable', 'exists:comments,id'],
        ]);

        // If replying, ensure parent belongs to same post
        if ($request->parent_id) {
            $parent = Comment::where('id', $request->parent_id)->where('post_id', $post->id)->first();
            if (!$parent) {
                return response()->json(['error' => 'Komentar induk tidak ditemukan.'], 422);
            }
            // Only allow 1 level of nesting
            if ($parent->parent_id !== null) {
                return response()->json(['error' => 'Balasan tidak dapat ditumpuk lebih dari 1 level.'], 422);
            }
        }

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $post->id,
            'parent_id' => $request->parent_id,
            'body' => $request->body,
        ]);

        $comment->load('user');

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'body' => $comment->body,
                'parent_id' => $comment->parent_id,
                'created_at' => $comment->created_at->diffForHumans(null, true) . ' lalu',
                'top_count' => 0,
                'is_topped' => false,
                'user' => [
                    'name' => $comment->user->name,
                    'avatar_url' => $comment->user->avatar_url,
                    'role' => $comment->user->role,
                    'role_badge' => $comment->user->role_badge,
                    'jurusan' => $comment->user->jurusan,
                ],
            ],
            'comment_count' => $post->commentCount(),
        ]);
    }

    /**
     * Delete a comment (owner or agent only).
     */
    public function destroy(Post $post, Comment $comment)
    {
        if ($comment->post_id !== $post->id) {
            return response()->json(['error' => 'Komentar tidak ditemukan.'], 404);
        }

        $user = Auth::user();
        if ($comment->user_id !== $user->id && !$user->isAgent()) {
            return response()->json(['error' => 'Anda tidak memiliki izin.'], 403);
        }

        $comment->tops()->delete();
        $comment->replies()->each(function ($reply) {
            $reply->tops()->delete();
            $reply->delete();
        });
        $comment->delete();

        return response()->json([
            'success' => true,
            'comment_count' => $post->commentCount(),
        ]);
    }

    /**
     * Toggle top on a comment (AJAX).
     */
    public function toggleTop(Post $post, Comment $comment)
    {
        if ($comment->post_id !== $post->id) {
            return response()->json(['error' => 'Komentar tidak ditemukan.'], 404);
        }

        $userId = Auth::id();
        $existing = CommentTop::where('user_id', $userId)->where('comment_id', $comment->id)->first();

        if ($existing) {
            $existing->delete();
            $isTopped = false;
        } else {
            CommentTop::create([
                'user_id' => $userId,
                'comment_id' => $comment->id,
            ]);
            $isTopped = true;
        }

        return response()->json([
            'success' => true,
            'top_count' => $comment->topCount(),
            'is_topped' => $isTopped,
        ]);
    }
}
