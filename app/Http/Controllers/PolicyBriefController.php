<?php

namespace App\Http\Controllers;

use App\Models\PolicyBrief;
use App\Models\PolicyEndorsement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PolicyBriefController extends Controller
{
    /**
     * Display Policy Lab gallery.
     */
    public function index()
    {
        $publishedBriefs = PolicyBrief::with('author', 'labRoom', 'endorsements')
            ->published()
            ->latest()
            ->get();

        $myBriefs = PolicyBrief::with('labRoom')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('policy-lab', compact('publishedBriefs', 'myBriefs'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('policy-brief-form', [
            'brief' => null,
            'templateType' => request('template', 'standar'),
        ]);
    }

    /**
     * Store a new policy brief.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'summary' => ['required', 'string', 'max:1000'],
            'problem' => ['required', 'string'],
            'analysis' => ['required', 'string'],
            'recommendation' => ['required', 'string'],
            'template_type' => ['required', 'in:standar,data-driven,quick-response'],
            'action' => ['required', 'in:draft,submit'],
        ]);

        $status = $validated['action'] === 'submit' ? 'pending' : 'draft';

        $brief = PolicyBrief::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'summary' => $validated['summary'],
            'problem' => $validated['problem'],
            'analysis' => $validated['analysis'],
            'recommendation' => $validated['recommendation'],
            'template_type' => $validated['template_type'],
            'status' => $status,
        ]);

        $message = $status === 'pending'
            ? 'Policy Brief telah disubmit untuk review Agent.'
            : 'Draft Policy Brief berhasil disimpan.';

        return redirect()->route('policy-lab.show', $brief)->with('success', $message);
    }

    /**
     * Show a policy brief (reader view).
     */
    public function show(PolicyBrief $policyBrief)
    {
        // Only published briefs visible to all, drafts/pending only to owner/agent
        if (!$policyBrief->isApproved()) {
            if ($policyBrief->user_id !== Auth::id() && !Auth::user()->isAgent()) {
                abort(403);
            }
        }

        $policyBrief->load('author', 'labRoom', 'endorsements', 'reviewer');

        return view('policy-brief-show', ['brief' => $policyBrief]);
    }

    /**
     * Show edit form.
     */
    public function edit(PolicyBrief $policyBrief)
    {
        if ($policyBrief->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$policyBrief->isDraft() && !$policyBrief->isRejected()) {
            return back()->with('error', 'Hanya brief berstatus draft atau ditolak yang bisa diedit.');
        }

        return view('policy-brief-form', [
            'brief' => $policyBrief,
            'templateType' => $policyBrief->template_type,
        ]);
    }

    /**
     * Update a policy brief.
     */
    public function update(Request $request, PolicyBrief $policyBrief)
    {
        if ($policyBrief->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'summary' => ['required', 'string', 'max:1000'],
            'problem' => ['required', 'string'],
            'analysis' => ['required', 'string'],
            'recommendation' => ['required', 'string'],
            'template_type' => ['required', 'in:standar,data-driven,quick-response'],
            'action' => ['required', 'in:draft,submit'],
        ]);

        $status = $validated['action'] === 'submit' ? 'pending' : 'draft';

        $policyBrief->update([
            'title' => $validated['title'],
            'summary' => $validated['summary'],
            'problem' => $validated['problem'],
            'analysis' => $validated['analysis'],
            'recommendation' => $validated['recommendation'],
            'template_type' => $validated['template_type'],
            'status' => $status,
            'rejection_reason' => null,
        ]);

        $message = $status === 'pending'
            ? 'Policy Brief telah disubmit untuk review Agent.'
            : 'Draft Policy Brief berhasil diperbarui.';

        return redirect()->route('policy-lab.show', $policyBrief)->with('success', $message);
    }

    /**
     * Toggle endorsement.
     */
    public function endorse(PolicyBrief $policyBrief)
    {
        if (!$policyBrief->isApproved()) {
            return back()->with('error', 'Hanya brief yang dipublikasi yang bisa di-endorse.');
        }

        $existing = $policyBrief->endorsements()->where('user_id', Auth::id())->first();

        if ($existing) {
            $existing->delete();
            $message = 'Endorsement dicabut.';
        } else {
            PolicyEndorsement::create([
                'policy_brief_id' => $policyBrief->id,
                'user_id' => Auth::id(),
            ]);
            $message = 'Anda mendukung Policy Brief ini! 👍';
        }

        return back()->with('success', $message);
    }
}
