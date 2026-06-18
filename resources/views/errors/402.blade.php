@extends('errors.layout')

@section('title', 'Diperlukan Pembayaran')
@section('code', '402')
@section('headline', 'Fitur Memerlukan Akses Khusus')
@section('message', $exception->getMessage() ?: 'Halaman atau fitur ini memerlukan status akun atau pembayaran yang valid!')