<p><strong>Nama:</strong> {{ $contact['name'] }}</p>
<p><strong>Email:</strong> {{ $contact['email'] }}</p>
<p><strong>Pesan:</strong></p>
<p>{!! nl2br(e($contact['message'])) !!}</p>
