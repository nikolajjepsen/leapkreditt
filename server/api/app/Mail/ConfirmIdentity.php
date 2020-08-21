<?php
namespace App\Mail;
 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Quote;
use App\ConfirmCode;
use App\Site;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\DB;

class ConfirmIdentity extends Mailable {
 
    use Queueable,
        SerializesModels;
    

    public $quote;
    public $code;
    public $siteName;
    public $siteEmail;
    public $siteUrl;
    public $siteCountryId;

    public function __construct (Quote $quote, $code) {
        $this->quote = $quote;
        $this->code = $code;

        $site = Site::where('id', $this->quote->site_id)->firstOrFail();
        $this->siteName = $site->name;
        $this->siteUrl = $site->url;
        $this->siteEmail = $site->email;

        $country = DB::table('countries')
                    ->where('id', $site->country_id)
                    ->first();

        $this->countryShort = $country->short;
        app('translator')->setLocale(strtolower($this->countryShort));
    }

    
    //build the message.
    public function build() {
        
        return $this->from($this->siteEmail, $this->siteName)
            ->view('mail.' . $this->quote->site_id . '.confirm-identity')
            ->subject(
                __('mail.confirmation.subject', [
                    'code' => $this->code, 
                    'siteName' => $this->siteName
                ])
            )
            ->with([
                'confirmCode' => $this->code,
                'siteName' => $this->siteName, 
                'siteUrl' => $this->siteUrl,
            ]);
    }
}