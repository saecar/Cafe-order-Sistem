<?php

namespace App\Http\Controllers;

use App\Models\contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!request()->bearerToken()){
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }
        $contacts = contact::latest()->paginate(10);
        return response()->json([
            'status' => 'success',
            'data' => $contacts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $contact = contact::create($request->validated([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string',
        ]));

        return response()->json([
            'status' => 'success',
            'message' => 'Contact message submitted successfully',
            'data' => $contact,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(contact $contact)
    {
        if (!request()->bearerToken()){
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }
        return response()->json([
            'status' => 'success',
            'data' => $contact,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, contact $contact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(contact $contact)
    {
        if (!request()->bearerToken()){
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }
        $contact->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Contact message deleted successfully',
        ]);
    }
}
