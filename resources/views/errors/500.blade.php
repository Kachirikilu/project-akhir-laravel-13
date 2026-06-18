@extends('errors.layout')

@section('title', 'Server Error')
@section('code', '500')
@section('headline', 'Terjadi Masalah pada Server')
@section('message', $exception->getMessage() ?: 'Terjadi kesalahan internal sistem aplikasi. Tim teknis kami akan segera memeriksa masalah ini!')