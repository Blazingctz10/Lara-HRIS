@extends('errors.layout')

@section('title', '429 Terlalu Banyak Permintaan')
@section('code', 'SYS429')
@section('variant', 'warning')

@section('icon')
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 28px; height: 28px;">
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
</svg>
@endsection

@section('heading', 'Batas Permintaan Akses Terlampaui')
@section('description', 'Sistem mendeteksi terlalu banyak permintaan dalam waktu singkat dari perangkat Anda sebagai bagian dari perlindungan keamanan perbankan.')
@section('action', 'Harap tunggu sekitar 1 menit sebelum mencoba kembali, lalu klik tombol "Muat Ulang Halaman".')
