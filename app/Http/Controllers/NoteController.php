<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {    
        // Get only notes belonging to logged-in user
        $notes = auth()->user()->notes()->latest()->paginate(10);
        return view('note.index', compact('notes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('note.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate input
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
    ]);
    
    // Create note and associate with logged-in user
    auth()->user()->notes()->create($validated);

    
    // Redirect with success message
    return redirect()->route('notes.index')
        ->with('success', 'Note created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        if ($note->user_id !== auth()->id()) {
        abort(403, 'Unauthorized');
    }
    
    return view('note.show', compact('note'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note)
    {
       if ($note->user_id !== auth()->id()) {
        abort(403);
    }
    
    return view('note.edit', compact('note')); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
         if ($note->user_id !== auth()->id()) {
        abort(403);
    }
    
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
    ]);
    
    // Update
    $note->update($validated);
    
    return redirect()->route('notes.index')
        ->with('success', 'Note updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        if ($note->user_id !== auth()->id()) {
        abort(403);
    }
    
    $note->delete();
    
    return redirect()->route('notes.index')
        ->with('success', 'Note deleted successfully!');
    }

    public function adminIndex()
    {
        // Get all notes with user information
 $notes = Note::with('user')->latest()->paginate(15);
 return view('admin.notes.index', compact('notes'));
    }

}
