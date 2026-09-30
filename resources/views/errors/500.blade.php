@extends('errors.layout')

@section('title', '500 Kesalahan Server')
@section('code', $exceptionInfo['code'] ?? 'SYS500')

@section('icon')
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 28px; height: 28px;">
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
</svg>
@endsection

@section('heading', $exceptionInfo['title'] ?? 'Terjadi Kesalahan Server Internal')
@section('description', $exceptionInfo['description'] ?? 'Server mengalami hambatan yang tidak terduga saat mengeksekusi permintaan Anda.')
@section('action', $exceptionInfo['action'] ?? 'Silakan muat ulang halaman. Apabila kendala masih berlanjut, hubungi Tim IT BPR Banda Raya dengan menyertakan kode error di atas.')

@if(config('app.debug') && isset($exception))
    @section('technical_details')
File: {{ $exception->getFile() }}:{{ $exception->getLine() }}
Message: {{ $exception->getMessage() }}

Stack Trace:
{{ $exception->getTraceAsString() }}
    @endsection
@endif
