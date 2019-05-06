@extends('layouts.app')
@section('included-css')
    <link href="/mi-chat/resources/sass/login.css" rel="stylesheet" type="text/css">
@stop
@section('content')
    <main class="login">
        <div class="background">
        </div>
        <div class="container">
            <div class="absolute-center card">
                <form class="form" method="POST" action="{{route('auth.login')}}">
                    @csrf
                    <div class="form-group title">
                        <h2>Prijavite se u sustav</h2>
                    </div>
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" name="username" value="{{session('username') ?? ''}}">
                        <div class="error-messages">
                            {{$errors->first('username')}}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password">Lozinka:</label>
                        <input type="password" name="password">
                        <div class="error-messages">
                            {{$errors->first('password')}}
                        </div>
                    </div>
                    <div class="form-group">
                        <a href="{{route('register')}}">Još nemate račun?</a>
                    </div>
                    <div class="form-group">
                        <button class="btn primary submit" type="submit" name="login">Prijavi se</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
@stop
