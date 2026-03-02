<?php

namespace App\Http\Controllers;

use App\Models\HoaxClaim;
use App\Models\HoaxVerdict;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HoaxClaimController extends Controller
{
    /**
     * Display the Hoax Buster center — list of open & resolved claims.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $category = $request->query('category');
        $status = $request->query('status', 'open');

        // Build claims query
        $claimsQuery = HoaxClaim::with(['reporter', 'approvedVerdicts']);

        // Status filter
        if ($status === 'open') {
            $claimsQuery->open();
        } elseif ($status === 'resolved') {
            $claimsQuery->resolved();
        } else {
            $claimsQuery->whereIn('status', ['open', 'resolved']);
        }

        // Category filter
        if ($category && $category !== 'semua') {
            $claimsQuery->where('category', $category);
        }

        $claims = $claimsQuery->latest()->paginate(10)->appends($request->query());

        // Stats
        $totalVerified = HoaxClaim::resolved()->count();
        $totalOpen = HoaxClaim::open()->count();
        $totalPending = HoaxClaim::pending()->count();
        $myContributions = $user->isAnonim() ? 0 : HoaxVerdict::where('user_id', $user->id)->approved()->count();

        // Top contributors (leaderboard)
        $leaderboard = HoaxVerdict::approved()
            ->selectRaw('user_id, COUNT(*) as total_verdicts')
            ->groupBy('user_id')
            ->orderByDesc('total_verdicts')
            ->limit(5)
            ->with('user')
            ->get();

        return view('hoax-buster', compact(
            'claims',
            'totalVerified',
            'totalOpen',
            'totalPending',
            'myContributions',
            'leaderboard',
            'category',
            'status'
        ));
    }

    /**
     * Store a new hoax claim submission.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:2000'],
            'source_url' => ['nullable', 'url', 'max:500'],
            'source_platform' => ['required', 'in:twitter,whatsapp,facebook,instagram,website,lainnya'],
            'category' => ['required', 'in:politik,kesehatan,teknologi,sosial,lainnya'],
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';

        HoaxClaim::create($validated);

        return back()->with('success', 'Klaim berhasil dikirim! Menunggu peninjauan oleh CIVIC Agent.');
    }

    /**
     * Show a single claim detail with verdicts and verdict form.
     */
    public function show(HoaxClaim $hoaxClaim)
    {
        if ($hoaxClaim->isPending()) {
            abort(404);
        }

        $hoaxClaim->load(['reporter', 'approvedVerdicts.user']);
        $consensus = $hoaxClaim->consensusResult();
        $counts = $hoaxClaim->verdictCounts();

        $userVerdict = null;
        $hasVoted = false;
        if (Auth::check() && !Auth::user()->isAnonim()) {
            $userVerdict = $hoaxClaim->userVerdict(Auth::id());
            $hasVoted = $userVerdict !== null;
        }

        return view('hoax-buster-show', compact(
            'hoaxClaim',
            'consensus',
            'counts',
            'userVerdict',
            'hasVoted'
        ));
    }

    /**
     * Submit a verdict for a claim.
     */
    public function submitVerdict(Request $request, HoaxClaim $hoaxClaim)
    {
        $user = Auth::user();

        if ($user->isAnonim()) {
            return back()->with('error', 'Pengunjung tidak dapat memberikan putusan.');
        }

        if (!$hoaxClaim->isOpen()) {
            return back()->with('error', 'Klaim ini tidak sedang terbuka untuk verifikasi.');
        }

        if ($hoaxClaim->hasVerdictFrom($user->id)) {
            return back()->with('error', 'Anda sudah memberikan putusan untuk klaim ini.');
        }

        $validated = $request->validate([
            'verdict' => ['required', 'in:valid,misleading,hoax'],
            'evidence_url' => ['nullable', 'url', 'max:500'],
            'reasoning' => ['required', 'string', 'max:2000'],
        ]);

        HoaxVerdict::create([
            'hoax_claim_id' => $hoaxClaim->id,
            'user_id' => $user->id,
            'verdict' => $validated['verdict'],
            'evidence_url' => $validated['evidence_url'] ?? null,
            'reasoning' => $validated['reasoning'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Putusan berhasil dikirim! Menunggu peninjauan oleh CIVIC Agent.');
    }
}
