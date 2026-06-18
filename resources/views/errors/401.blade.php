@extends('errors.layout')

@section('title', 'Akses Ditolak')
@section('code', '401')
@section('headline', 'Sesi Tidak Sah / Perlu Login')
@section('message', $exception->getMessage() ?: 'Silakan login terlebih dahulu untuk mengakses halaman ini!')