<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\EventReminder;
use App\Mail\EventUpdateNotification;
use App\Models\Event;
use App\Models\PurchasedTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

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

        $purchasedTickets = PurchasedTicket::where('event_id', $id)->with('user')->get();
        $uniqueUsers = collect();

        if ($record->save()) {
            foreach ($purchasedTickets as $ticket) {
                $user = $ticket->user;

                if (!$uniqueUsers->contains('id', $user->id)) {
                    // Add the user to the unique collection
                    $uniqueUsers->push($user);
                    // Customizing the email data
                    $data = [
                        'user' => $user,
                        'event' => $record,
                    ];
                    Mail::to($user->email)->send(new EventUpdateNotification($data));
                }
            }

            return response()->json([
                'status' => 200,
                'message' => 'Event was updated successfully.',
                'data' => $record,
                //'purchasedTickets' => $purchasedTickets
            ]);
        }
        return response()->json([
            'status' => 404,
            'message' => 'An Error Occured. Please verify the data provided.',
            'data' => $record
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


    public function getOrganizerEvents(int $organizerId)
    {
        //Verifying if the user type is organizer
        $isOrganizer = User::where('id', $organizerId)->firstOrFail();

        if ($isOrganizer->type !== 'organizer') {
            return response()->json([
                'status' => 403,
                'message' => "You don't have the right access to see organizer records.",
                'user_type' => $isOrganizer->type
            ]);
        }

        $myEvents = Event::where('organizer_id', $organizerId)->with('tickets')->get();

        return response()->json([
            'myEvents' => $myEvents
        ]);
    }

    public function getAvailableEvents()
    {
        //$currentDate = Carbon::now();   
        $currentDate = Carbon::now()->toDateString();

        $availableEvents = Event::select('id', 'title', 'date', 'time', 'location', 'status', 'organizer_id')
            ->where('status', 'available')
            ->whereDate('date', '>=', $currentDate)
            ->with('organizer')
            ->with('tickets')
            ->get();

        return response()->json([
            'availableEvents' => $availableEvents,
            'currentDate' => $currentDate
        ]);
    }

    public function sendReminder(Request $request)
    {
        $data = $request->all();
        $request->validate([
            'organizer_id' => 'required|numeric|min:1',
            'event_id' => 'required|numeric|min:1',
        ]);

        $isOrganizer = User::where('id', $data['organizer_id'])->firstOrFail();

        if ($isOrganizer->type !== 'organizer') {
            return response()->json([
                'status' => 403,
                'message' => "You don't have the right access to send event reminders.",
                'user_type' => $isOrganizer->type
            ]);
        }

        $event = Event::where('id', $data['event_id'])->firstOrFail();
        $purchasedTickets = PurchasedTicket::where('event_id', $data['event_id'])->with('user')->get();
        $uniqueUsers = collect();


        foreach ($purchasedTickets as $ticket) {
            $user = $ticket->user;

            if (!$uniqueUsers->contains('id', $user->id)) {
                // Add the user to the unique collection
                $uniqueUsers->push($user);
                // Customizing the email data
                $data = [
                    'user' => $user,
                    'event' => $event,
                ];
                Mail::to($user->email)->send(new EventReminder($data));
            }
        }

        return response()->json([
            'status' => 200,
            'message' => 'Event reminder was sent successfully.',
            'data' => $event,
            'purchasedTickets' => $purchasedTickets
        ]);
    }
}
