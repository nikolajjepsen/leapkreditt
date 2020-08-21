<?php

return [
    'quote' => [
        'fullName' => [
            'required' => 'Indtast for- og efternavn',
        ],
        'firstname' => [
            'required' => 'Indtast fornavn'
        ],
        'lastname' => [
            'required' => 'Indtast efternavn'
        ],
        'email' => [
            'required' => 'Indtast email',
            'email' => 'Indtast en gyldig e-mail',
            'unique' => 'E-mail adressen er allerede i brug.',
        ],
        'mobile' => [
            'required' => 'Indtast telefon nr.',
            'unique' => 'Mobilnummeret er allerede i brug.',
            'invalid' => ':attribute er ikke et gyldigt mobil nummer',
        ],
        'loanAmount' => [
            'required' => 'Lånebeløb skal indtastes.',
            'numeric' => 'Ugyldigt lånebeløb.'
        ],
    ]
];