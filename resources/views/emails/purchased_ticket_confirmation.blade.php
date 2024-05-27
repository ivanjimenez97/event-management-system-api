    <h1>Purchased Ticket Confirmation</h1>
    <br />
    <br />
    Dear {{ $data['user']['name'] }},
    <br />
    Congratulations! Your ticket purchase for The {{ $data['ticket']['event']['title'] }} has been successfully confirmed. 
    <br />
    <br />
    <strong>Event:</strong> {{ $data['ticket']['event']['title'] }}
    <br />
    <strong>Date:</strong> {{ $data['ticket']['event']['date'] }}
    <br />
    <strong>Time:</strong> {{ $data['ticket']['event']['time'] }}
    <br />
    <strong>Description:</strong> {{ $data['ticket']['event']['description'] }}
    <br />
    <strong>Location:</strong> {{ $data['ticket']['event']['location'] }}
    <br />
    <strong>Ticket Type:</strong> {{ $data['ticket']['name'] }}
    <br />
    <strong>Total Amount:</strong> {{ $data['ticket']['price'] }}