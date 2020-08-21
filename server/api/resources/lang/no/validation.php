<?php

return [
    'quote' => [
        'fullName' => [
            'required' => 'Angi for- og etternavn',
        ],
        'firstname' => [
            'required' => 'Angi fornavn'
        ],
        'lastname' => [
            'required' => 'Angi etternavn'
        ],
        'email' => [
            'required' => 'Angi e-postadressen',
            'email' => 'Angi en gyldig e-postadresse',
            'unique' => 'E-postadressen er allerede i bruk.',
        ],
        'mobile' => [
            'required' => 'Angi mobilnummer.',
            'unique' => 'Mobilnummeret er allerede i bruk.',
            'invalid' => 'Angi et gyldig mobilnummer',
        ],
        'loanAmount' => [
            'required' => 'Lånebeløpet må angis.',
            'numeric' => 'Ugyldig lånebeløp.'
        ],
        'tenure' => [
            'numeric' => 'Løpetid kan bare være tall.',
            'between' => 'Angi en gyldig løpetid.'
        ],
        'age' => [
            'numeric' => 'Alder kan bare være tall.',
            'between' => 'Angi en gyldig alder.',
        ],
    ]
];