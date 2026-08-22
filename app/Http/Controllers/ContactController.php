<?php

namespace App\Http\Controllers;

use App\Models\contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = contact::latest()->paginate(10);
        return response()->json(['status' => 'success', 'data' => $contacts]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([ // fix: validate() bukan validated()
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string',
        ]);

        $contact = contact::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Contact message submitted successfully',
            'data' => $contact,
        ], 201);
    }

    public function show(contact $contact)
    {
        return response()->json(['status' => 'success', 'data' => $contact]);
    }

    public function destroy(contact $contact)
    {
        $contact->delete();
        return response()->json(['status' => 'success', 'message' => 'Contact message deleted successfully']);
    }
}