@extends('errors.layout')

@section('title', 'Mode Pemeliharaan')
@section('code', '503')
@section('headline', 'Sistem Sedang Dimaintenance')
@section('message', $exception->getMessage() ?: 'Aplikasi sedang dalam proses pembaruan atau pemeliharaan rutin. Kami akan kembali dalam beberapa saat lagi!')