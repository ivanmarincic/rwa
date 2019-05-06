<div id="{{$dialogId}}" class="dialog">
    <div class="title-bar d-flex flex-row align-items-center">
        <div class="flex-grow-1 title">
            {{$title}}
        </div>
        <div class="flex-shrink-0 controls">
            <i class="close material-icons">
                close
            </i>
        </div>
    </div>
    <div class="dialog-content">
        {{ $content }}
    </div>
</div>
