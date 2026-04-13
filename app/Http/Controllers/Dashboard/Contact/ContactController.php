<?php

namespace App\Http\Controllers\Dashboard\Contact;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    /**
     * Display a listing of contact messages.
     */
    public function index(Request $request)
    {
        $query = Contact::query()->with('subject');

        // Filter by subject
        if ($request->filled('subject_id')) {
            $query->where('contact_subject_id', $request->subject_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'replied') {
                $query->whereNotNull('replied_at');
            } elseif ($request->status === 'pending') {
                $query->whereNull('replied_at');
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $contacts = $query->latest()->paginate(15);
        $subjects = \App\Models\ContactSubject::where('is_active', true)->get();

        return view('pages.dashboard.contacts.index', compact('contacts', 'subjects'));
    }

    /**
     * Display the specified contact message.
     */
    public function show($id)
    {
        $contact = Contact::with(['subject', 'repliedBy'])->findOrFail($id);

        return view('pages.dashboard.contacts.show', compact('contact'));
    }

    /**
     * Update the reply for the specified contact message.
     */
    public function updateReply(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);

        $request->validate([
            'reply_content' => 'required|string',
        ]);

        $contact->update([
            'reply_content' => $request->reply_content,
            'replied_at' => now(),
            'replied_by' => Auth::id(),
        ]);

        return redirect()->route('dashboard.contacts.show', $contact->id)
            ->with('success', 'Reply recorded successfully.');
    }

    /**
     * Remove the specified contact message from storage.
     */
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return redirect()->route('dashboard.contacts.index')
            ->with('success', 'Message deleted successfully.');
    }
}
