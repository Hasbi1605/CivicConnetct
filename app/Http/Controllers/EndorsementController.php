<?php

namespace App\Http\Controllers;

use App\Models\Endorsement;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EndorsementController extends Controller
{
    /**
     * Toggle endorsement on an artikel post (AJAX).
     */
    public function toggle(Post $post)
    {
        // Endorsements only for artikel posts
        if ($post->isFactCheck()) {
            return response()->json(['error' => 'Post Fact-Check menggunakan sistem voting Fakta/Hoaks.'], 422);
        }

        $userId = Auth::id();
        $existing = Endorsement::where('user_id', $userId)->where('post_id', $post->id)->first();

        if ($existing) {
            $existing->delete();
            $endorsed = false;
        } else {
            Endorsement::create([
                'user_id' => $userId,
                'post_id' => $post->id,
            ]);
            $endorsed = true;
        }

        return response()->json([
            'success' => true,
            'endorsed' => $endorsed,
            'endorsement_count' => $post->endorsementCount(),
        ]);
    }
}
