<p>Dear {{ $inquiry->name }},</p>
<p>Thank you for your interest in <strong>{{ $inquiry->artwork_title ?: 'this artwork' }}</strong>.</p>
<p>We have received your inquiry and our team will contact you shortly.</p>
<p>Regards,<br>{{ $siteTitle }}</p>
