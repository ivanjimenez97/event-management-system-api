<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;


class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::all();

        return response()->json([
            'events' => $events,
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
        $data = $request->all();
        $request->validate([
            'title' => 'string|required|max:255',
            'description' => 'required|string',
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i',
            'location' => 'required|string',
            'organizer_id' => 'required|numeric|min:1'
        ]);


        $data['status'] = 'available';

        $record = new Event();
        $record->fill($data);

        //Verifying if the user type is organizer

        $isOrganizer = User::where('id', $data['organizer_id'])->firstOrFail();

        if ($isOrganizer->type !== 'organizer') {
            return response()->json([
                'status' => 403,
                'message' => "You don't have the right access to create events.",
                'user_type' => $isOrganizer->type
            ]);
        }

        if ($record->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Event created successfully.',
                'data' => $record
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'An Error Occured. Please verify the data provided.',
                'data' => $record
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $record = Event::where('id', $id)->firstOrFail();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $request->validate([
            'title' => 'string|required|max:255',
            'description' => 'required|string',
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i',
            'location' => 'required|string',
            'status' => 'required|string',
        ]);

        $record = Event::where('id', $id)->firstOrFail();
        $record->fill($data);

        //Verifying if the user type is organizer
        $isOrganizer = User::where('id', $data['organizer_id'])->firstOrFail();

        if ($isOrganizer->type !== 'organizer') {
            return response()->json([
                'status' => 403,
                'message' => "You don't have the right access to update events.",
                'user_type' => $isOrganizer->type
            ]);
        }

        if ($record->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Event was updated successfully.',
                'data' => $record
            ]);
        }
        return response()->json([
            'status' => 404,
            'message' => 'An Error Occured. Please verify the data provided.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $record = Event::where('id', $id)->firstOrFail();

        if ($record->delete()) {
            return response()->json([
                'status' => 200,
                'message' => 'Event deleted successfully.',
                'data' => $record
            ]);
        }

        return response()->json([
            'status' => 500,
            'message' => 'An Error Occured. Please verify the data provided and try again.',
            'data' => $record
        ]);
    }


    public function getOrganizerEvents(int $organizerId){

        //Verifying if the user type is organizer
        $isOrganizer = User::where('id', $organizerId)->firstOrFail();

        if ($isOrganizer->type !== 'organizer') {
            return response()->json([
                'status' => 403,
                'message' => "You don't have the right access to see organizer records.",
                'user_type' => $isOrganizer->type
            ]);
        }

        $myEvents = Event::where('organizer_id', $organizerId)->get();

        return response()->json([
            'myEvents' => $myEvents
        ]);
    }

    public function getAvailableEvents(){
         
        //$currentDate = Carbon::now();   
        $currentDate = Carbon::now()->toDateString();   

        $availableEvents = Event::select('title', 'date', 'time', 'location', 'status', 'organizer_id')
            ->where('status', 'available')
            ->whereDate('date', '>=', $currentDate)
            ->with('organizer')
            ->get();

        return response()->json([
            'availableEvents' => $availableEvents
        ]);
    }
}
