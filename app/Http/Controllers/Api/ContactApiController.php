<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource;
use App\Http\Resources\ContactSubjectResource;
use App\Models\Contact;
use App\Models\ContactSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactApiController extends Controller
{
    /**
     * Get all active contact subjects.
     */
    public function subjects()
    {
        $subjects = ContactSubject::where('is_active', true)->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Contact subjects retrieved successfully',
            'data' => ContactSubjectResource::collection($subjects)
        ]);
    }

    /**
     * Store a new contact message.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'contact_subject_id' => 'required|exists:contact_subjects,id',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $contact = Contact::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Your message has been sent successfully',
            'data' => new ContactResource($contact->load('subject'))
        ], 201);
    }
}
