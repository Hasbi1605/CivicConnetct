<?php

namespace App\Http\Controllers;

use App\Models\HoaxClaim;
use App\Models\HoaxVerdict;
use App\Models\Notification;
use App\Models\PolicyBrief;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModerationController extends Controller
{
    /**
     * Display the moderation dashboard (agents only).
     */
    public function index()
    {
        $pendingPosts = Post::with('user', 'reports.user')
            ->pending()
            ->latest()
            ->paginate(10, ['*'], 'pending_page');

        $recentReviewed = Post::with('user', 'reviewer')
            ->whereIn('status', ['approved', 'rejected'])
            ->latest('reviewed_at')
            ->take(10)
            ->get();

        // Approved posts that have user reports
        $reportedPosts = Post::with('user', 'reports.user')
            ->approved()
            ->whereHas('reports')
            ->withCount('reports')
            ->orderByDesc('reports_count')
            ->get();

        $stats = [
            'pending' => Post::pending()->count(),
            'approved_today' => Post::approved()
                ->whereDate('reviewed_at', today())
                ->count(),
            'total_reports' => \App\Models\Report::where('status', 'pending')->count(),
        ];

        $pendingBriefs = PolicyBrief::with('author', 'labRoom')
            ->pending()
            ->latest()
            ->get();

        $pendingClaims = HoaxClaim::with('reporter')
            ->pending()
            ->latest()
            ->get();

        $pendingVerdicts = HoaxVerdict::with(['user', 'claim'])
            ->pending()
            ->latest()
            ->get();

        return view('moderation.index', compact('pendingPosts', 'recentReviewed', 'reportedPosts', 'pendingBriefs', 'pendingClaims', 'pendingVerdicts', 'stats'));
    }

    /**
     * Approve a post.
     */
    public function approve(Post $post)
    {
        if (!$post->isPending()) {
            return response()->json(['message' => 'Post sudah ditinjau sebelumnya.'], 422);
        }

        $post->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Notify the post author
        Notification::create([
            'user_id' => $post->user_id,
            'post_id' => $post->id,
            'type' => 'post_approved',
            'title' => 'Postingan Disetujui',
            'message' => 'Postingan Anda telah disetujui dan sekarang terlihat oleh publik.',
        ]);

        return response()->json([
            'message' => 'Postingan berhasil disetujui.',
            'status' => 'approved',
        ]);
    }

    /**
     * Reject a post.
     */
    public function reject(Request $request, Post $post)
    {
        if (!$post->isPending()) {
            return response()->json(['message' => 'Post sudah ditinjau sebelumnya.'], 422);
        }

        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $post->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Notify the post author with warning
        Notification::create([
            'user_id' => $post->user_id,
            'post_id' => $post->id,
            'type' => 'post_rejected',
            'title' => '⚠️ Postingan Ditolak',
            'message' => 'Postingan Anda ditolak oleh CIVIC Agent. Alasan: ' . $request->rejection_reason,
        ]);

        return response()->json([
            'message' => 'Postingan berhasil ditolak dan penulis telah diberitahu.',
            'status' => 'rejected',
        ]);
    }

    /**
     * Approve a policy brief.
     */
    public function approveBrief(PolicyBrief $policyBrief)
    {
        if (!$policyBrief->isPending()) {
            return response()->json(['message' => 'Policy Brief sudah ditinjau sebelumnya.'], 422);
        }

        $policyBrief->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        Notification::create([
            'user_id' => $policyBrief->user_id,
            'type' => 'brief_approved',
            'title' => 'Policy Brief Disetujui',
            'message' => 'Policy Brief "' . $policyBrief->title . '" telah dipublikasi di Policy Lab.',
        ]);

        return response()->json([
            'message' => 'Policy Brief berhasil dipublikasi.',
            'status' => 'approved',
        ]);
    }

    /**
     * Reject a policy brief.
     */
    public function rejectBrief(Request $request, PolicyBrief $policyBrief)
    {
        if (!$policyBrief->isPending()) {
            return response()->json(['message' => 'Policy Brief sudah ditinjau sebelumnya.'], 422);
        }

        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $policyBrief->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        Notification::create([
            'user_id' => $policyBrief->user_id,
            'type' => 'brief_rejected',
            'title' => '⚠️ Policy Brief Ditolak',
            'message' => 'Policy Brief "' . $policyBrief->title . '" ditolak. Alasan: ' . $request->rejection_reason,
        ]);

        return response()->json([
            'message' => 'Policy Brief berhasil ditolak.',
            'status' => 'rejected',
        ]);
    }

    // ══════════════════════════════════════════
    //  Hoax Claim Moderation
    // ══════════════════════════════════════════

    /**
     * Approve a hoax claim — opens it for community verdicts.
     */
    public function approveClaim(HoaxClaim $hoaxClaim)
    {
        if (!$hoaxClaim->isPending()) {
            return response()->json(['message' => 'Klaim sudah ditinjau sebelumnya.'], 422);
        }

        $hoaxClaim->update([
            'status' => 'open',
        ]);

        Notification::create([
            'user_id' => $hoaxClaim->user_id,
            'type' => 'claim_approved',
            'title' => 'Klaim Hoaks Disetujui',
            'message' => 'Klaim Anda "' . \Str::limit($hoaxClaim->title, 50) . '" telah disetujui dan terbuka untuk verifikasi komunitas.',
        ]);

        return response()->json([
            'message' => 'Klaim berhasil disetujui dan terbuka untuk verifikasi.',
            'status' => 'open',
        ]);
    }

    /**
     * Reject a hoax claim.
     */
    public function rejectClaim(Request $request, HoaxClaim $hoaxClaim)
    {
        if (!$hoaxClaim->isPending()) {
            return response()->json(['message' => 'Klaim sudah ditinjau sebelumnya.'], 422);
        }

        $hoaxClaim->delete();

        return response()->json([
            'message' => 'Klaim berhasil ditolak dan dihapus.',
            'status' => 'rejected',
        ]);
    }

    /**
     * Approve a verdict — then check auto-resolve.
     */
    public function approveVerdict(HoaxVerdict $hoaxVerdict)
    {
        if (!$hoaxVerdict->isPending()) {
            return response()->json(['message' => 'Putusan sudah ditinjau sebelumnya.'], 422);
        }

        $hoaxVerdict->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Check if claim should auto-resolve
        $claim = $hoaxVerdict->claim;
        $resolved = $claim->checkAutoResolve();

        $message = 'Putusan berhasil disetujui.';
        if ($resolved) {
            $message .= ' Klaim telah otomatis diselesaikan dengan konsensus: ' . $claim->finalVerdictLabel();

            // Notify claim reporter
            Notification::create([
                'user_id' => $claim->user_id,
                'type' => 'claim_resolved',
                'title' => 'Klaim Hoaks Terselesaikan',
                'message' => 'Klaim "' . \Str::limit($claim->title, 50) . '" telah diselesaikan. Putusan: ' . $claim->finalVerdictLabel(),
            ]);
        }

        return response()->json([
            'message' => $message,
            'status' => 'approved',
            'claim_resolved' => $resolved,
        ]);
    }

    /**
     * Reject a verdict.
     */
    public function rejectVerdict(Request $request, HoaxVerdict $hoaxVerdict)
    {
        if (!$hoaxVerdict->isPending()) {
            return response()->json(['message' => 'Putusan sudah ditinjau sebelumnya.'], 422);
        }

        $hoaxVerdict->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Putusan berhasil ditolak.',
            'status' => 'rejected',
        ]);
    }
}
