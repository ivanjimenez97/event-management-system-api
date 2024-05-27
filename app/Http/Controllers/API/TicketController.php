<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\PurchasedTicketConfirmation;
use App\Models\PurchasedTicket;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tickets = Ticket::all();

        return response()->json([
            'status' => 200,
            'tickets' => $tickets,
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
            'name' => 'string|required|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'event_id' => 'required|numeric|min:1'
        ]);

        $record = new Ticket();

        $record->fill($data);

        if ($record->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Ticket created successfully.',
                'data' => $record
            ]);
        }

        return response()->json([
            'status' => 404,
            'message' => 'An Error Occured. Please verify the data provided.',
            'data' => $record
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $record = Ticket::where('id', $id)->firstOrFail();

        if (!$record) {
            return response()->json([
                'status' => 404,
                'message' => 'An Error Occured. Please verify the data provided.',
                'data' => $record
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Ticket Details.',
            'data' => $record
        ]);
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
            'name' => 'string|required|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'event_id' => 'required|numeric|min:1'
        ]);

        $record = Ticket::where('id', $id)->firstOrFail();

        $record->fill($data);

        if ($record->save()) {
            return response()->json([
                'status' => 200,
                'message' => 'Ticket updated successfully.',
                'data' => $record
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
        $record = Ticket::where('id', $id)->firstOrFail();

        if ($record->delete()) {
            return response()->json([
                'status' => 200,
                'message' => 'Ticket deleted successfully.',
                'data' => $record
            ]);
        }

        return response()->json([
            'status' => 500,
            'message' => 'An Error Occured. Please verify the data provided and try again.',
            'data' => $record
        ]);
    }

    public function purchase(Request $request)
    {
        $data = $request->all();
        //Validating data
        $request->validate([
            'user_id' => 'required|numeric|min:1',
            'ticket_id' => 'required|numeric|min:1'
        ]);

        $record = new PurchasedTicket();
        $record->fill($data);

        //Getting the user specified in the request.
        $user = User::where('id', $data['user_id'])->firstOrFail();

        if ($record->save()) {
            //Getting Purchased Ticket with Ticket and User Data.
            $purchasedTicket = PurchasedTicket::where('id', $record->id)
                ->with('user')
                ->with('event')
                ->with('ticket')
                ->firstOrFail();

            //Sending Email Confirmation to the user.
            Mail::to($user->email)->send(new PurchasedTicketConfirmation($purchasedTicket));

            return response()->json([
                'status' => 200,
                'message' => 'Ticket purchased successfully.',
                'data' => $record,
                'purchasedTicket' => $purchasedTicket
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'An Error Occured. Please verify the data provided.',
                'data' => $record
            ]);
        }
    }
}
