<?php
return [
    'required'  => 'Le champ :attribute est obligatoire.',
    'email'     => 'Le champ :attribute doit être une adresse e-mail valide.',
    'min'       => ['string' => 'Le champ :attribute doit contenir au moins :min caractères.'],
    'max'       => ['string' => 'Le champ :attribute ne peut pas dépasser :max caractères.'],
    'unique'    => 'La valeur du champ :attribute est déjà utilisée.',
    'confirmed' => 'La confirmation du champ :attribute ne correspond pas.',
    'string'    => 'Le champ :attribute doit être une chaîne de caractères.',
    'url'       => 'Le format du champ :attribute est invalide.',
    'image'     => 'Le champ :attribute doit être une image.',
    'mimes'     => 'Le champ :attribute doit être un fichier de type : :values.',
    'numeric'   => 'Le champ :attribute doit être un nombre.',
    'in'        => 'La valeur sélectionnée pour :attribute est invalide.',

    'attributes' => [
        'name'     => 'nom',
        'email'    => 'e-mail',
        'password' => 'mot de passe',
        'title'    => 'titre',
        'content'  => 'contenu',
        'subject'  => 'sujet',
        'message'  => 'message',
        'phone'    => 'téléphone',
        'slug'     => 'identifiant URL',
    ],
];
