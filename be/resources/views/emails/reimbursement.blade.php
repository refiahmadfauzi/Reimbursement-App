@component('mail::message')
# New Reimbursement Request

**Submitted by:** {{ $reimbursement->user->name }}
**Title:** {{ $reimbursement->title }}
**Amount:** Rp {{ number_format($reimbursement->amount, 0, ',', '.') }}

@component('mail::button', ['url' => 'http://localhost:5173/dashboard'])
View Request
@endcomponent

Thank you,<br>
{{ config('app.name') }}
@endcomponent
