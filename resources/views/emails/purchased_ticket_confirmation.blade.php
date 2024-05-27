@component('mail::message')
    # Purchased Ticket Confirmation
    <br />
    Dear {{ $data['user']['name'] }},
    <br />
    Congratulations! Your ticket purchase for The {{ $data['event']['title'] }} has been successfully confirmed. 
    <br />
    <br />
    <strong>Event:</strong> {{ $data['event']['title'] }}
    <br />
    <strong>Date:</strong> {{ $data['event']['date'] }}
    <br />
    <strong>Description:</strong> {{ $data['event']['description'] }}
    <br />
    <strong>Location:</strong> {{ $data['event']['location'] }}
    <br />
    <strong>Ticket Type:</strong> {{ $data['ticket']['name'] }}
    <br />
    <strong>Total Amount:</strong> {{ $data['ticket']['price'] }}
@endcomponent
