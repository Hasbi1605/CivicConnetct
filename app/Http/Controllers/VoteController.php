<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    /**
     * Store or toggle a vote on a fact-check post.
     */
    public function store(Request $request, Post $post)
    {
        // Only fact-check posts can be voted on
        if (!$post->isFactCheck()) {
            return response()->json(['error' => 'Hanya post Fact-Check yang bisa di-vote.'], 422);
        }

        $request->validate([
            'vote' => ['required', 'in:fakta,hoaks'],
        ]);

        $userId = Auth::id();
        $existingVote = Vote::where('user_id', $userId)->where('post_id', $post->id)->first();

        if ($existingVote) {
            if ($existingVote->vote === $request->vote) {
                // Same vote → remove (toggle off)
                $existingVote->delete();
            } else {
                // Different vote → update
                $existingVote->update(['vote' => $request->vote]);
            }
        } else {
            // No existing vote → create
            Vote::create([
                'user_id' => $userId,
                'post_id' => $post->id,
                'vote' => $request->vote,
            ]);
        }

        // Return updated counts
        return response()->json([
            'fakta_count' => $post->faktaCount(),
            'hoaks_count' => $post->hoaksCount(),
            'user_vote' => $post->userVote($userId),
        ]);
    }
}
