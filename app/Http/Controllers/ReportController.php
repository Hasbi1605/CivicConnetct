<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Store a report on a post (AJAX).
     */
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'reason' => ['required', 'in:hoaks,spam,ujaran-kebencian,lainnya'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        // Check if user already reported this post
        $existing = Report::where('user_id', Auth::id())
            ->where('post_id', $post->id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Anda sudah melaporkan postingan ini sebelumnya.',
            ], 422);
        }

        Report::create([
            'user_id' => Auth::id(),
            'post_id' => $post->id,
            'reason' => $request->reason,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Laporan berhasil dikirim. Terima kasih atas kontribusi Anda!',
            'reports_count' => $post->reports()->count(),
        ]);
    }
}
