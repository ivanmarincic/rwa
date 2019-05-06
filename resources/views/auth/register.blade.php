@extends('layouts.app')
@section('included-css')
    <link href="/mi-chat/resources/sass/register.css" rel="stylesheet" type="text/css">
@stop
@section('content')
    <main class="register">
        <div class="background">
        </div>
        <div class="container">
            <div class="absolute-center card">
                <form class="form" method="POST" action="{{route('auth.register')}}">
                    @csrf
                    <div class="form-group title">
                        <h2>Registrirajte se u sustav</h2>
                    </div>
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" name="username">
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
                        <label for="confirm_password">Potvrdi lozinku:</label>
                        <input type="password" name="password_confirmation">
                        <div class="error-messages">
                            {{$errors->first('password_confirmation')}}
                        </div>
                    </div>
                    <div class="form-group">
                        <a href="{{route('login')}}">Već imate račun?</a>
                    </div>
                    <div class="form-group">
                        <button class="btn primary submit" type="submit" name="login">Registriraj se</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
@stop
