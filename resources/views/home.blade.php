@extends('layouts.app')
@section('included-css')
    <link href="/mi-chat/resources/sass/home.css" rel="stylesheet" type="text/css">
@stop
@inject('chats', 'App\Repositories\ChatRepository')
@inject('users', 'App\Repositories\UserRepository')
@section('content')
    <main class="home d-flex flex-column">
        <div class="container d-flex flex-column flex-fill">
            <div class="row flex-fill flex-nowrap">
                <div class="col-md-5 col-lg-4 col-12 d-flex flex-column chats">
                    <div class="card d-flex flex-column flex-fill">
                        <div class="top-bar d-flex flex-row flex-shrink-0">
                            <input class="flex-grow-1 search" type="search" placeholder="Pretraži" autocomplete="off">
                            <button id="open-add-dialog" class="flex-shrink-0 btn primary circle send">
                                <i class="material-icons">add</i>
                            </button>
                        </div>
                        <div class="chats-list flex-grow-1">
                            <?php $user = \Illuminate\Support\Facades\Auth::user()?>
                            @forelse ($chats->allForUser($user) as $chat)
                                <div class="chat" data-id="{{$chat->id}}">
                                    <div class="image">
                                        @foreach ($chat->participants as $participant)
                                            @if ($participant->user->id != $user->id)
                                                @if (is_null($participant->user["profile_image"]))
                                                    <img src="/mi-chat/resources/images/default_user.png"/>
                                                @else
                                                    <img
                                                        src="{{route("user.image.get", [ "filename" => $participant->user["profile_image"]])}}"/>
                                                @endif
                                            @endif
                                        @endforeach
                                    </div>
                                    <div class="name">
                                        {{ $chat->name }}
                                    </div>
                                    <span class="unread"></span>
                                </div>
                            @empty
                                <p class="empty">Nemate razgovora</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="col-md-7 col-lg-8 col-12 d-flex flex-column selected-chat">
                    <div class="card d-flex flex-column flex-fill">
                        <div class="tool-bar d-flex flex-row flex-shrink-0 align-items-center">
                            <i class="material-icons back flex-shrink-0">arrow_back_ios</i>
                            <p class="name flex-grow-1">Ime chata</p>
                            <div class="options flex-shrink-0 menu icon-button align-right">
                                <i class="material-icons selected-item">more_vert</i>
                                <div class="dropdown">
                                    <div class="item" data-action="participants">
                                        Učesnici
                                    </div>
                                    <div class="item" data-action="leave">
                                        Napusti razgovor
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="conversation flex-grow-1">
                            <div class="wrapper">
                            </div>
                            <div id="message-popup" class="menu absolute"
                                 data-target=".selected-chat .message .options">
                                <div class="dropdown">
                                    <div class="item" data-action="edit">
                                        Uredi
                                    </div>
                                    <div class="item" data-action="delete">
                                        Izbriši
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="input-bar d-flex flex-row flex-shrink-0">
                            <input class="flex-grow-1 message" type="text" name="message" placeholder="Poruka"
                                   autocomplete="off">
                            <button class="flex-shrink-0 btn primary circle send">
                                <i class="material-icons">send</i>
                            </button>
                            <button class="flex-shrink-0 btn primary circle cancel-edit">
                                <i class="material-icons">close</i>
                            </button>
                        </div>
                        <div class="empty">
                            <span>Odaberite razgovor ili napravite novi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
@section('dialogs')
    @component('components.dialog')
        @slot('title')
            Napravi novi chat
        @endslot
        @slot('dialogId')
            new-chat
        @endslot
        @slot('content')
            <div class="form add-new-chat">
                <div class="form-group flex-shrink-0">
                    <div class="d-flex flex-row align-items-center">
                        <label for="name">Naziv: </label>
                        <input type="text" name="name">
                    </div>
                </div>
                <div class="users-list d-flex flex-column align-items-stretch flex-grow-1">
                    <div class="search-bar flex-shrink-0">
                        <input id="filter-users" type="search" name="search" placeholder="Pretraži korisnike">
                    </div>
                    <div class="filtered-list-wrapper flex-grow-1">
                        <div class="filtered-list">
                            @forelse ($users->allButMe() as $user)
                                <div class="item" data-id="{{ $user->id }}">
                                    <span class="name">{{ $user->username }}</span>
                                    <span class="selection">
                                    <i class="material-icons">done</i>
                                </span>
                                </div>
                            @empty
                                <p class="empty">Nema korisnika</p>
                            @endforelse
                        </div>
                    </div>
                    <div class="selected-users">

                    </div>
                </div>
                <div class="form-group flex-shrink-0">
                    <button class="btn primary save" name="save">Spremi</button>
                </div>
            </div>
        @endslot
    @endcomponent
    @component('components.dialog')
        @slot('title')
            Informacije korisnika
        @endslot
        @slot('dialogId')
            user-info
        @endslot
        @slot('content')
            <div class="info-row profile-image d-flex justify-content-center align-items-center">
                <img/>
            </div>
            <div class="info-row d-flex flex-row">
                <p>Korisničko Ime:</p>
                <p class="username"></p>
            </div>
            <div class="info-row d-flex flex-row">
                <p>Ime i Prezime:</p>
                <p class="name"></p>
            </div>
            <div class="info-row d-flex flex-row">
                <p>Email:</p>
                <p class="email"></p>
            </div>
        @endslot
    @endcomponent
    @component('components.dialog')
        @slot('title')
            Učesnici razgovora
        @endslot
        @slot('dialogId')
            chat-participants
        @endslot
        @slot('content')
            <div class="participants-header">
                <div class="name">
                    Ime
                </div>
                <div class="is-admin">
                    Admin
                </div>
            </div>
            <div class="participants-list">
            </div>
            <div>
                <button class="btn primary save" name="save">Spremi</button>
            </div>
        @endslot
    @endcomponent
@endsection
@section('included-js')
    <script src="/mi-chat/resources/js/app/home.js"></script>
@stop
