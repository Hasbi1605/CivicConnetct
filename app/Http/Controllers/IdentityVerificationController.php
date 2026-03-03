<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IdentityVerificationController extends Controller
{
    /**
     * Show the identity verification page.
     * Displays different states: unsubmitted, pending, approved, rejected.
     */
    public function show()
    {
        $user = Auth::user();

        return view('identity.verify', compact('user'));
    }

    /**
     * Submit identity verification (upload KTM/KTD + NIM/NIDN).
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Prevent re-submission if already pending or approved
        if ($user->isIdentityPending()) {
            return back()->with('info', 'Dokumen Anda sedang dalam proses verifikasi.');
        }
        if ($user->isIdentityVerified()) {
            return back()->with('info', 'Identitas Anda sudah terverifikasi.');
        }

        $validated = $request->validate([
            'identity_card_type' => ['required', 'in:ktm,ktd'],
            'identity_card_image' => ['required', 'image', 'max:5120'], // Max 5MB
            'nim_nidn' => ['required', 'string', 'max:50'],
        ], [
            'identity_card_type.required' => 'Pilih jenis kartu identitas.',
            'identity_card_type.in' => 'Jenis kartu harus KTM atau KTD.',
            'identity_card_image.required' => 'Upload foto kartu identitas.',
            'identity_card_image.image' => 'File harus berupa gambar.',
            'identity_card_image.max' => 'Ukuran file maksimal 5MB.',
            'nim_nidn.required' => 'Masukkan NIM atau NIDN.',
        ]);

        // Store image on local (private) disk — not publicly accessible
        $path = $request->file('identity_card_image')
            ->store('identity-cards/' . $user->id, 'local');

        $user->update([
            'identity_card_type' => $validated['identity_card_type'],
            'identity_card_image' => $path,
            'nim_nidn' => $validated['nim_nidn'],
            'identity_status' => 'pending',
            'identity_rejection_reason' => null, // Clear any previous rejection
        ]);

        // Notify all agents about new verification request
        $agents = User::where('role', 'agent')->get();
        foreach ($agents as $agent) {
            Notification::create([
                'user_id' => $agent->id,
                'type' => 'identity_submitted',
                'title' => 'Verifikasi Identitas Baru',
                'message' => $user->name . ' mengajukan verifikasi identitas (' . strtoupper($validated['identity_card_type']) . ').',
            ]);
        }

        return redirect()->route('identity.verify')
            ->with('success', 'Dokumen berhasil dikirim! CIVIC Agent akan memverifikasi identitas Anda.');
    }

    /**
     * Resubmit after rejection — same as store but clears old data.
     */
    public function resubmit(Request $request)
    {
        $user = Auth::user();

        if (!$user->isIdentityRejected()) {
            return back()->with('info', 'Anda tidak dapat mengirim ulang saat ini.');
        }

        // Delete old image
        if ($user->identity_card_image) {
            Storage::disk('local')->delete($user->identity_card_image);
        }

        // Reset status so store() can process
        $user->update([
            'identity_status' => 'unsubmitted',
            'identity_card_image' => null,
        ]);

        return $this->store($request);
    }

    /**
     * Serve identity card image (private file — only owner or agent can access).
     */
    public function serveImage(User $user)
    {
        $authUser = Auth::user();

        // Only the user themselves or an agent can view the card
        if ($authUser->id !== $user->id && !$authUser->isAgent()) {
            abort(403, 'Anda tidak memiliki izin untuk melihat dokumen ini.');
        }

        if (!$user->identity_card_image || !Storage::disk('local')->exists($user->identity_card_image)) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        return response()->file(Storage::disk('local')->path($user->identity_card_image));
    }
}
