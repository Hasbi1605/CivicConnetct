<?php

namespace App\Http\Controllers;

use App\Models\LabDiscussion;
use App\Models\LabRoom;
use App\Models\LabSource;
use App\Models\PolicyBrief;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LabRoomController extends Controller
{
    /**
     * Display L.A.B Room dashboard.
     */
    public function index()
    {
        $rooms = LabRoom::with('host', 'participants')
            ->latest()
            ->get();

        $completedRooms = LabRoom::with('host', 'participants')
            ->completed()
            ->latest()
            ->take(6)
            ->get();

        $myRooms = LabRoom::with('host', 'participants')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $joinedRooms = Auth::user()->joinedRooms()
            ->with('host', 'participants')
            ->wherePivot('user_id', Auth::id())
            ->latest()
            ->get();

        $stats = [
            'active_rooms' => LabRoom::active()->count(),
            'total_participants' => \DB::table('lab_participants')->distinct('user_id')->count('user_id'),
            'completed_rooms' => LabRoom::completed()->count(),
        ];

        return view('lab-room', compact('rooms', 'completedRooms', 'myRooms', 'joinedRooms', 'stats'));
    }

    /**
     * Create a new L.A.B Room.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:1000'],
            'category' => ['required', 'string', 'in:fact-check,kebijakan,sosial,lainnya'],
            'target' => ['nullable', 'string', 'in:Policy Brief,Fact-Check,Video Edukasi'],
            'is_private' => ['nullable', 'boolean'],
        ]);

        $room = LabRoom::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'],
            'target' => $validated['target'] ?? null,
            'is_private' => $validated['is_private'] ?? false,
        ]);

        // Host auto-joins as participant
        $room->participants()->attach(Auth::id(), ['joined_at' => now()]);

        return redirect()->route('lab-room.show', $room)->with('success', 'Room L.A.B berhasil dibuat!');
    }

    /**
     * Show a specific L.A.B Room.
     */
    public function show(LabRoom $labRoom)
    {
        $labRoom->load([
            'host',
            'participants',
            'sources.user',
            'discussions' => fn($q) => $q->whereNull('parent_id')->with('user', 'replies.user')->latest(),
            'policyBrief',
        ]);

        return view('lab-room-show', compact('labRoom'));
    }

    /**
     * Join a room.
     */
    public function join(LabRoom $labRoom)
    {
        if ($labRoom->isParticipant(Auth::id())) {
            return back()->with('error', 'Anda sudah bergabung di room ini.');
        }

        if ($labRoom->isFull()) {
            return back()->with('error', 'Room sudah penuh (maks ' . $labRoom->max_participants . ' peserta).');
        }

        if ($labRoom->status === 'completed') {
            return back()->with('error', 'Room ini sudah selesai.');
        }

        $labRoom->participants()->attach(Auth::id(), ['joined_at' => now()]);

        // Update status to in_progress if needed
        if ($labRoom->status === 'open' && $labRoom->participants()->count() > 1) {
            $labRoom->update(['status' => 'in_progress']);
        }

        return back()->with('success', 'Berhasil bergabung ke room!');
    }

    /**
     * Leave a room.
     */
    public function leave(LabRoom $labRoom)
    {
        if ($labRoom->isHost(Auth::id())) {
            return back()->with('error', 'Host tidak bisa meninggalkan room. Hapus room jika ingin keluar.');
        }

        $labRoom->participants()->detach(Auth::id());

        return redirect()->route('lab-room.index')->with('success', 'Anda telah keluar dari room.');
    }

    /**
     * Advance room phase (host only).
     */
    public function advancePhase(LabRoom $labRoom)
    {
        if (!$labRoom->isHost(Auth::id())) {
            return back()->with('error', 'Hanya host yang dapat memajukan fase.');
        }

        $nextPhase = match ($labRoom->phase) {
            'literasi' => 'analisis',
            'analisis' => 'output',
            'output' => null,
            default => null,
        };

        if (!$nextPhase) {
            // Complete the room
            $labRoom->update(['status' => 'completed']);
            return back()->with('success', 'Room L.A.B telah selesai! 🎉');
        }

        $labRoom->update([
            'phase' => $nextPhase,
            'status' => 'in_progress',
        ]);

        return back()->with('success', 'Fase berhasil dimajukan ke: ' . $labRoom->phaseLabel());
    }

    /**
     * Add source (Literasi phase).
     */
    public function addSource(Request $request, LabRoom $labRoom)
    {
        if (!$labRoom->isParticipant(Auth::id())) {
            return back()->with('error', 'Anda bukan peserta room ini.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'url' => ['required', 'url', 'max:500'],
            'summary' => ['nullable', 'string', 'max:1000'],
        ]);

        $labRoom->sources()->create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'url' => $validated['url'],
            'summary' => $validated['summary'] ?? null,
        ]);

        return back()->with('success', 'Sumber berhasil ditambahkan.');
    }

    /**
     * Add discussion comment (Analisis phase).
     */
    public function addDiscussion(Request $request, LabRoom $labRoom)
    {
        if (!$labRoom->isParticipant(Auth::id())) {
            return back()->with('error', 'Anda bukan peserta room ini.');
        }

        $validated = $request->validate([
            'claim' => ['required', 'string', 'max:2000'],
            'evidence' => ['nullable', 'string', 'max:2000'],
            'parent_id' => ['nullable', 'exists:lab_discussions,id'],
        ]);

        $labRoom->discussions()->create([
            'user_id' => Auth::id(),
            'claim' => $validated['claim'],
            'evidence' => $validated['evidence'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        return back()->with('success', 'Argumen berhasil ditambahkan.');
    }

    /**
     * Submit policy brief from room (Output phase).
     */
    public function submitBrief(Request $request, LabRoom $labRoom)
    {
        if (!$labRoom->isHost(Auth::id())) {
            return back()->with('error', 'Hanya host yang dapat menyusun Policy Brief.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'summary' => ['required', 'string', 'max:1000'],
            'problem' => ['required', 'string'],
            'analysis' => ['required', 'string'],
            'recommendation' => ['required', 'string'],
            'action' => ['required', 'in:draft,submit'],
        ]);

        $status = $validated['action'] === 'submit' ? 'pending' : 'draft';

        $brief = $labRoom->policyBrief;

        if ($brief) {
            $brief->update([
                'title' => $validated['title'],
                'summary' => $validated['summary'],
                'problem' => $validated['problem'],
                'analysis' => $validated['analysis'],
                'recommendation' => $validated['recommendation'],
                'status' => $status,
            ]);
        } else {
            $brief = PolicyBrief::create([
                'user_id' => Auth::id(),
                'lab_room_id' => $labRoom->id,
                'title' => $validated['title'],
                'summary' => $validated['summary'],
                'problem' => $validated['problem'],
                'analysis' => $validated['analysis'],
                'recommendation' => $validated['recommendation'],
                'status' => $status,
            ]);
        }

        $message = $status === 'pending'
            ? 'Policy Brief telah disubmit untuk review Agent.'
            : 'Draft Policy Brief berhasil disimpan.';

        return back()->with('success', $message);
    }
}
