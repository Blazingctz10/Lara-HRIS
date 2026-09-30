@extends('errors.layout')

@section('title', '403 Akses Ditolak')
@section('code', 'AUTH005')

@section('icon')
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 28px; height: 28px;">
    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
</svg>
@endsection

@section('heading', 'Izin Akses Ditolak')
@section('description', 'Akun Anda saat ini tidak memiliki hak otorisasi peran (Role Permission) yang memadai untuk membuka atau mengelola menu ini.')
@section('action', 'Jika Anda merasa ini adalah kekeliruan atau membutuhkan akses ini untuk tugas operasional, silakan hubungi Super Administrator atau Divisi SDM / HR BPR Banda Raya.')
