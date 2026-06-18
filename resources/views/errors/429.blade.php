@extends('errors.layout')

@section('title', 'Terlalu Banyak Request')
@section('code', '429')
@section('headline', 'Batasan Akses Terlampaui')
@section('message', $exception->getMessage() ?: 'Terlalu banyak permintaan dalam waktu singkat. Tolong tunggu beberapa menit sebelum mencoba kembali!')