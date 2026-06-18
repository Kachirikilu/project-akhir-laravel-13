@extends('errors.layout')

@section('title', 'Akses Dilarang')
@section('code', '403')
@section('headline', 'Anda Tidak Memiliki Akses')
@section('message', $exception->getMessage() ?: 'Peran (role) akun Anda tidak diizinkan untuk melihat dokumen atau halaman ini!')