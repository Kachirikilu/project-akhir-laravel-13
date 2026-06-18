@extends('errors.layout')

@section('title', 'Halaman Tidak Ditemukan')
@section('code', '404')
@section('headline', 'Data atau Halaman Tidak Ditemukan')
@section('message', $exception->getMessage() ?: 'Halaman yang Anda tuju tidak tersedia, telah dipindahkan, atau Anda tidak memiliki akses ke data tersebut!')