@extends('layouts.app')
@section('included-css')
    <link href="/mi-chat/resources/sass/splash.css" rel="stylesheet" type="text/css">
@stop
@section('content')
    <main class="splash">
        <div class="background">
        </div>
        <div class="container">
            <div class="absolute-center">
                <h2 class="text align-content-center">
                    Dobrodošli, prijavite se ili registrirajte za pristup
                </h2>
                <div class="buttons">
                    <a href="{{route('register')}}">
                        <button class="btn primary register">Registracija</button>
                    </a>
                    <a href="{{route('login')}}">
                        <button class="btn primary login">Prijava</button>
                    </a>
                </div>
            </div>
        </div>
    </main>
@stop
