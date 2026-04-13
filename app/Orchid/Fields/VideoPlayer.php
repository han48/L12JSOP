<?php

namespace App\Orchid\Fields;

use Orchid\Screen\Field;

/**
 * Custom Orchid field that renders a video player UI component.
 *
 * Extends Orchid's base Field class and delegates rendering to the
 * Blade view `orchid.fields.video-player`. The field accepts a URL
 * (or any text value) via a standard text input and displays a
 * video player alongside it in the admin panel edit screens.
 *
 * Usage: `VideoPlayer::make('video_url')->title('Video URL')`
 *
 * Inline HTML attributes exposed: placeholder, value, type, name.
 *
 * @see \Orchid\Screen\Field
 * @satisfies Requirements 14.1
 */
class VideoPlayer extends Field
{
    /**
     * The Blade view used to render the field.
     *
     * @var string
     */
    protected $view = 'orchid.fields.video-player';

    /**
     * Default attributes for the field.
     *
     * @var array
     */
    protected $attributes = [
        'placeholder' => 'Enter text...',
        'class'       => 'form-control',
        'type'        => 'text',
    ];

    /**
     * List of attributes available for the HTML tag.
     *
     * @var array
     */
    protected $inlineAttributes = [
        'placeholder',
        'value',
        'type',
        'name'
    ];
}
