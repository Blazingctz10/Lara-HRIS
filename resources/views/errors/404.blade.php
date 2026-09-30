@extends('errors.layout')

@section('title', '404 Tidak Ditemukan')
@section('code', 'SYS404')

@section('icon')
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 28px; height: 28px;">
    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6" />
</svg>
@endsection

@section('heading', 'Halaman Tidak Ditemukan')
@section('description', 'Tautan yang Anda tuju mungkin telah dipindahkan, dihapus, atau Anda memasukkan alamat URL yang keliru.')
@section('action', 'Periksa kembali ejaan tautan pada bilah alamat peramban Anda atau kembali ke Dashboard utama.')
