@extends('errors.layout')

@section('title', 'Halaman Kadaluarsa')
@section('code', '419')
@section('headline', 'Sesi Form Telah Berakhir')
@section('message', $exception->getMessage() ?: 'Keamanan halaman telah kadaluarsa karena terlalu lama tidak aktif. Silakan segarkan (refresh) halaman dan coba lagi!')