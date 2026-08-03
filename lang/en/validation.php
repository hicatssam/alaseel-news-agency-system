<?php
return [
    'required'  => 'The :attribute field is required.',
    'email'     => 'The :attribute must be a valid email address.',
    'min'       => ['string' => 'The :attribute must be at least :min characters.'],
    'max'       => ['string' => 'The :attribute may not be greater than :max characters.'],
    'unique'    => 'The :attribute has already been taken.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'string'    => 'The :attribute must be a string.',
    'url'       => 'The :attribute format is invalid.',
    'image'     => 'The :attribute must be an image.',
    'mimes'     => 'The :attribute must be a file of type: :values.',
    'numeric'   => 'The :attribute must be a number.',
    'in'        => 'The selected :attribute is invalid.',

    'attributes' => [
        'name'     => 'name',
        'email'    => 'email',
        'password' => 'password',
        'title'    => 'title',
        'content'  => 'content',
        'subject'  => 'subject',
        'message'  => 'message',
        'phone'    => 'phone',
        'slug'     => 'slug',
    ],
];
