@component('mail::message')
# Notifikasi Pengaturan Ulang Kata Sandi

Halo,

Anda menerima email ini karena kami menerima permintaan pengaturan ulang kata sandi untuk akun Anda.

@component('mail::button', ['url' => config('app.url') . '/reset-password/' . $token])
Atur Ulang Kata Sandi
@endcomponent

Tautan pengaturan ulang kata sandi ini akan kedaluwarsa dalam 60 menit.

Jika Anda tidak meminta pengaturan ulang kata sandi, silakan abaikan email ini atau hubungi dukungan jika Anda memiliki kekhawatiran.

Jika Anda mengalami kesulitan mengeklik tombol "Atur Ulang Kata Sandi", salin dan tempel URL di bawah ini ke browser Anda:

{{ config('app.url') }}/reset-password/{{ $token }}

Terima kasih,<br>
{{ config('app.name') }}

<small>Jika Anda tidak meminta pengaturan ulang kata sandi ini, silakan abaikan email ini atau hubungi dukungan jika Anda memiliki kekhawatiran.</small>
@endcomponent