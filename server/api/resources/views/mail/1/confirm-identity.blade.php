@extends('mail.base')


@section('siteName', 'leapkreditt')

@section('preContent')
    <b>Hei, {{ $quote->firstname }}</b><br /><br />
    Vi må bekrefte e-postadressen din hvis vi skal kunne vise deg anbefalingene våre.
    Dette er et sikkerhetstrinn, sånn at vi kan sikre at informasjonen din forblir helt trygg.
@endsection

@section('confirmCodeBody')
    Bruk <b style="color:#b3547b;">{{ $confirmCode }}</b> som bekreftelseskoden din. Angi den i skjemaets felt.
@endsection


@section('footer')
    Denne e-posten er sendt til {{ $quote->firstname }} {{ $quote->lastname }} på {{ $quote->email }} angående bekreftelse av e-postadressen hos <a href="{{ $siteUrl }}">{{ $siteName }}</a>
@endsection