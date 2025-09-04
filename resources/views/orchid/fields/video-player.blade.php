@component($typeForm, get_defined_vars())
    <div class="video-player" data-controller="video-player">
        @isset($attributes['value'])
            <video controls src="{{ html_entity_decode($attributes['value']) }}">
            </video>
        @endisset
    </div>
@endcomponent
