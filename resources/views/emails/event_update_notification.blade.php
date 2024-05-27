
    <h1>Event Updated</h1>
    <p>Hello {{ $data['user']['name'] }}, The event details have been updated:</p>
    <p><strong>Title:</strong> {{ $data['event']['title'] }}</p>
    <p><strong>Description:</strong> {{ $data['event']['description'] }}</p>
    <p><strong>Date:</strong> {{ $data['event']['date'] }}</p>
    <p><strong>Time:</strong> {{ $data['event']['time'] }}</p>
    <p><strong>Location:</strong> {{ $data['event']['location'] }}</p>
    <p><strong>Status:</strong> {{ $data['event']['status'] }}</p>
    <p>Thank you for using our application!</p>