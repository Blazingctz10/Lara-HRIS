@extends('errors.layout')

@section('title', '419 Sesi Kedaluwarsa')
@section('code', 'AUTH003')

@section('icon')
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 28px; height: 28px;">
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
</svg>
@endsection

@section('heading', 'Sesi Formulir Berakhir (CSRF)')
@section('description', 'Demi perlindungan keamanan transaksi perbankan, sesi formulir otomatis dihentikan apabila halaman dibiarkan terlalu lama tanpa aktivitas.')
@section('action', 'Silakan klik tombol "Muat Ulang Halaman" di bawah, lalu kirimkan kembali data formulir Anda.')
