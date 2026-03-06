@php
    $user = auth()->user();
    
    if ($user->hasRole('administrador')) {
        $layout = 'app';
    } elseif ($user->hasRole('encargado')) {
        $layout = 'encargado';
    } elseif ($user->hasRole('conductor')) {
        $layout = 'conductor';
    } else {
        $layout = 'guest';
    }
@endphp

@extends("layouts.{$layout}")