
<h1>Event Reminder</h1>
<br />
<br />
<p>Dear {{ $data['user']['name'] }},</p>
<p>This is a friendly reminder about the upcoming event:</p>
<br />
<br />
<p><strong>Event Title:</strong> {{ $data['event']['title'] }}</p>
<p><strong>Date:</strong> {{ $data['event']['date'] }}</p>
<p><strong>Time:</strong> {{ $data['event']['time'] }}</p>
<p><strong>Location:</strong> {{ $data['event']['location'] }}</p>
<p><strong>Description:</strong> {{ $data['event']['description'] }}</p>
<p><strong>Status:</strong> {{ $data['event']['status'] }}</p>
<br />
<br />
<p>We look forward to seeing you there! If you have any questions or need further information, feel free to reply to this email.</p>
<br />
<br />

<p>Best regards, The EMSA Team.</p>