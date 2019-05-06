@extends('layouts.app')
@section('included-css')
    <link href="/mi-chat/resources/sass/profile.css" rel="stylesheet" type="text/css">
@stop
@section('content')
    @php
        $user = Auth::user();
        $passwordVisible = $errors->has('password') && $errors->has('password-confirm');
    @endphp
    <main class="profile d-flex flex-column">
        <div class="container d-flex flex-column flex-fill">
            <div class="row flex-fill flex-nowrap">
                <div class="col-12 d-flex flex-column">
                    <div class="card d-flex flex-column flex-fill">
                        <form class="form" method="POST" action="{{route('user.update')}}">
                            <div class="tool-bar d-flex flex-row flex-shrink-0 align-items-center">
                                <i class="material-icons back flex-shrink-0">arrow_back_ios</i>
                                <p class="name">Uredi profil</p>
                            </div>
                            <div
                                class="form-wrapper d-flex flex-row justify-content-center align-items-start flex-grow-1">
                                <div class="form d-flex flex-row row flex-grow-1">
                                    <div class="col-12 col-md-8">
                                        <div class="username form-group d-flex flex-column align-items-md-start">
                                            <label for="username">Username:</label>
                                            <input type="text" name="username" value="{{ $user->username }}" readonly>
                                        </div>
                                        <div class="fullname form-group d-flex flex-column align-items-md-start">
                                            <label for="full_name">Ime i Prezime:</label>
                                            <input type="text" name="full_name" value="{{ $user->full_name ?? ''}}">
                                        </div>
                                        <div class="email form-group d-flex flex-column align-items-md-start">
                                            <label for="email">Email:</label>
                                            <input type="text" name="email" value="{{ $user->email ?? ''}}">
                                        </div>
                                        <div class="change-password form-group d-flex flex-column align-items-md-start">
                                            <button type="button" class="btn primary">Izmijeni lozinku</button>
                                        </div>
                                        <div class="passwords-container" <?php if ($passwordVisible) { ?> style="display:block;"<?php }?>>
                                            <?php if ($passwordVisible) { ?>
                                            <input type="hidden" name="change_password" value="true">
                                            <?php }?>
                                            <div class="password form-group d-flex flex-column align-items-md-start">
                                                <label for="password">Lozinka:</label>
                                                <input type="password" name="password">
                                                <div class="error-messages">
                                                    {{$errors->first('username')}}
                                                </div>
                                            </div>
                                            <div
                                                class="password-confirm form-group d-flex flex-column align-items-md-start">
                                                <label for="password_confirmation">Potvrdi lozinku:</label>
                                                <input type="password" name="password_confirmation">
                                                <div class="error-messages">
                                                    {{$errors->first('password-confirm')}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="profile-image form-group d-flex flex-column align-items-md-start">
                                            <label for="profile-image">Slika:</label>
                                            <div class="preview d-flex justify-content-center align-items-center">
                                                <div class="fix-aspect"></div>
                                                <div class="overlay">
                                                    <div class="progress-bar"></div>
                                                </div>
                                                <img
                                                    src="{{route("user.image.get", ["filename" => $user->profile_image])}}"/>
                                            </div>
                                            <div class="error-messages">
                                            </div>
                                            <input id="image-picker" type="file" class="picker"
                                                   accept="image/png, image/jpeg"
                                                   name="profile-image">
                                            <label for="image-picker">
                                                Odaberi sliku...
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-buttons d-flex flex-row row align-self-center flex-shrink-0">
                                <div class="col-md-8 col-12 d-flex flex-column align-items-end">
                                    <button type="submit" class="btn primary save">Spremi</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
@section('included-js')
    <script src="/mi-chat/resources/js/app/profile.js"></script>
@stop
