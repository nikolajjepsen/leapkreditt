<?php

namespace App\Http\Controllers\Quote;

use Illuminate\Http\Response;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Site\SiteController;
use App\Rules\ValidatePhone;
use Validator;

use App\Quote;
use App\Loan;

use App\Services\CampaignMonitorClient;
use App\Http\Resources\CampaignMonitorQuoteResource as CMQuoteResource;

class QuoteController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    public function createQuote(Request $request)
    {
        // TODO: Mobile numbers suck as is.
        // Should only validate with CC, and further push to 3rd-part apis w/ cc
        // Data in DB should be without CC.

        // TODO: SMS and E-mail services integration
        // Update all params on subscriber

        // Check request keys
        if (!$request->has(['fullName', 'email', 'mobile', 'loanAmount'])) {
            return response()->json(
                ['message' => 'failed','reason' => 'missing inputs', 'required' => ['fullName', 'email', 'mobile', 'loanAmount']],
                400 // HTTP Code
            );
        }
        $siteController = new SiteController;
        $site = $siteController->getSiteById(\Auth::guard('api')->id());
        $country = DB::table('countries')->where('id', '=', $site['country_id'])->first();

        app('translator')->setLocale(strtolower($country->short));

        $mobileBase = $request->input('mobile');

        if (substr($mobileBase, 0, 3) != '+' . $country->country_code) {
            $mobileIso = '+' . $country->country_code . $mobileBase;
        }
        $request->merge(['mobile' => $mobileIso]);

        // Error messages
        $messages = [
            'fullName.required'   => __('validation.quote.fullName.required'),
            'email.required'      => __('validation.quote.email.required'),
            'email.email'         => __('validation.quote.email.email'),
            'mobile.required'     => __('validation.quote.mobile.required'),
            'loanAmount.required' => __('validation.quote.loanAmount.required'),
            'loanAmount.numeric'  => __('validation.quote.loanAmount.numeric')
        ];
        $validator = Validator::make($request->all(), [
            'fullName'    => 'required',
            'email'       => 'required|email:rfc,dns',
            'mobile'      => ['required', new ValidatePhone],
            'loanAmount'  => 'required|numeric'
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'failed',
                'reason'  => 'validation error',
                'errors'  =>  $validator->errors()
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'email'  => 'unique:quotes',
            'mobile' => 'unique:quotes'
        ]);

        if ($validator->fails()) {
            $foundQuote = Quote::where('email', $request->input('email'))->orWhere('mobile', $mobileBase)->first();
            return response()->json([
                'message' => 'failed',
                'reason'  => 'duplication error',
                'errors'  => $validator->errors(),
                'param'   => [
                    'quoteId' => $foundQuote ? $foundQuote->id : 0
                ]
            ], 400);
        }

        // Split name into first- and lastnames in case of multiple spaces; 
        // pop name and let last index be lastname. Rest is firstname.
        $nameParts = explode(' ', $request->input('fullName'));
        if (count($nameParts) > 1) {
            $lastname   = array_pop($nameParts);
            $firstname  = implode(' ', $nameParts);
        } else {
            $firstname = $request->input('fullName');
            $lastname  = '';
        }

        $quote = new Quote();
        $quote->site_id      = $site['id'];
        $quote->firstname    = $firstname;
        $quote->lastname     = $lastname;
        $quote->email        = $request->input('email');
        $quote->mobile       = $mobileBase;
        $quote->loan_amount  = $request->input('loanAmount');

        $data = [
            'EmailAddress' => $request->input('email'),
            'Name' => $firstname . ' ' . $lastname,
            'RestartSubscriptionBasedAutoresponders' => true,
            'ConsentToTrack' => 'Yes',
            'CustomFields' => [
                [
                    'Key' => 'loan_amount',
                    'Value' => $request->input('loanAmount')
                ]
            ]
        ];

        
        if ($quote->save()) {   
            $cm = new CampaignMonitorClient();
            $cm->subscriber(
                'e8e54066c9b4042f55b9cdc58e086a1b', 
                'POST', 
                $data
            );

            return response()->json(
                ['message' => 'success','quoteId' => $quote->id],
                200 // HTTP Code
            );
        }
    }

    private function isDuplicate($email, $mobile)
    {
        $check = DB::table('quotes')
                    ->where('email', '=', $email)
                    ->orWhere('mobile', '=', $mobile)
                    ->first();
        
        if ($check) {
            return true;
        }

        return false;
    }

    public function getQuotes()
    {
        return Quote::all();
    }

    public function getQuoteById($quoteId)
    {
        $quote = Quote::findOrFail($quoteId);
        return $quote;
    }

    public function updateQuoteById($quoteId, Request $request)
    {
        // TODO: Mobile numbers suck as is.
        // Should only validate with CC, and further push to 3rd-part apis w/ cc
        // Data in DB should be without CC.

        // TODO: SMS and E-mail services integration
        // Update all params on subscriber
        if (!isset($quoteId) || !is_numeric($quoteId) || $quoteId <= 0) {
            return;
        }

        $quote = Quote::findOrFail($quoteId);
        
        // Check request keys
        if (!$request->has(['fullName', 'email', 'mobile', 'loanAmount'])) {
            return response()->json(
                [
                    'message' => 'failed',
                    'reason' => 'missing inputs', 
                    'required' => [
                        'fullName', 
                        'email', 
                        'mobile',
                        'loanAmount'
                    ]
                ],
                400 // HTTP Code
            );
        }
        $siteController = new SiteController;
        $site = $siteController->getSiteById(\Auth::guard('api')->id());
        $country = DB::table('countries')->where('id', '=', $site['country_id'])->first();
        $mobileBase = $request->input('mobile');

        app('translator')->setLocale(strtolower($country->short));

        if (substr($mobileBase, 0, 3) != '+' . $country->country_code) {
            $mobileIso = '+' . $country->country_code . $mobileBase;
        }
        $request->merge(['mobileIso' => $mobileIso]);

        // TODO: Localized error messages based on auth site id.
        // Error messages
        $messages = [
            'fullName.required'     => __('validation.quote.fullName.required'),
            'email.required'        => __('validation.quote.email.required'),
            'email.email'           => __('validation.quote.email.email'),
            'email.unique'          => __('validation.quote.email.unique'),
            'mobile.required'       => __('validation.quote.mobile.required'),
            'mobile.unique'         => __('validation.quote.mobile.unique'),
            'loanAmount.required'   => __('validation.quote.loanAmount.required'),
            'loanAmount.numeric'    => __('validation.quote.loanAmount.numeric'),
            'tenure.numeric'        => __('validation.quote.tenure.numeric'),
            'tenure.between'        => __('validation.quote.tenure.between'),
            'age.numeric'           => __('validation.quote.age.numeric'),
            'age.between'           => __('validation.quote.age.between'),
        ];

        $validator = Validator::make($request->all(), [
            'fullName'    => 'required',
            'email'       => ['required', 'email:rfc,dns', $quote->email != $request->input('email') ? 'unique:quotes' : ''],
            'mobile'      => ['required', $quote->mobile != $mobileBase ? 'unique:quotes' : ''],
            'mobileIso'   => [new ValidatePhone],
            'loanAmount'  => 'required|numeric',
            'tenure'      => 'numeric|between:1,470',
            'age'         => 'numeric|between:17,110'
        ], $messages);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'failed',
                'reason'  => 'validation error',
                'errors'  =>  $validator->errors()
            ], 400);
        }

        // Split name into first- and lastnames in case of multiple spaces; pop name and let last index be lastname. Rest is firstname.
        $nameParts = explode(' ', $request->input('fullName'));
        if (count($nameParts) > 1) {
            $lastname   = array_pop($nameParts);
            $firstname  = implode(' ', $nameParts);
        } else {
            $firstname = $request->input('fullName');
            $lastname  = '';
        }

        $tenure = !empty($request->input('tenure')) ? $request->input('tenure') : null;
        $age = !empty($request->input('age')) ? $request->input('age') : null;

        $quote->firstname    = $firstname;
        $quote->lastname     = $lastname;
        $quote->email        = $request->input('email');
        $quote->mobile       = $mobileBase;
        $quote->loan_amount  = $request->input('loanAmount');
        $quote->tenure       = $tenure;
        $quote->age          = $age;

        if ($quote->save()) {
            $data = [
                'EmailAddress' => $request->input('email'),
                'Name' => $firstname . ' ' . $lastname,
                'Resubscribe' => true,
                'RestartSubscriptionBasedAutoresponders' => true,
                'ConsentToTrack' => 'Yes',
                'CustomFields' => [
                    [
                        'Key' => 'tenure',
                        'Value' => $request->input('tenure')
                    ],
                    [
                        'Key' => 'age',
                        'Value' => $request->input('age')
                    ],
                    [
                        'Key' => 'loan_amount',
                        'Value' => $request->input('loanAmount')
                    ]
                ]
            ];
    
            $cm = new CampaignMonitorClient();
            $cm->subscriber(
                'e8e54066c9b4042f55b9cdc58e086a1b', 
                'PUT', 
                $data, 
                '?email=' . $request->input('email')
            );
            return response()->json([
                'message' => 'success',
                'updated' => $request->all()
            ], 200);
        }

        return response()->json([
            'status' => 'failed',
            'reason' => 'generic error'
        ], 404);
    }

    public function deleteQuoteById($quoteId)
    {
        $quote = Quote::findOrFail($quoteId);
        $quote->delete();
    }

    public function getQuoteClicksById($quoteId)
    {
        $quote = $this->getQuoteById($quoteId);
        echo $quote->clicks->map->only(['id', 'quote_id', 'loan_id', 'created_at']);
    }
    public function getQuoteConversionsById($quoteId)
    {
        $quote = $this->getQuoteById($quoteId);
        echo $quote->conversions->map->only(['id', 'click_id', 'payout', 'currency', 'created_at']);
    }

    public function getQuoteLoanSuggestionsById($quoteId)
    {
        $quote = $this->getQuoteById($quoteId);
        $originSite = DB::table('sites')
                        ->where('id', '=', $quote->site_id)
                        ->get()
                        ->first();
        $loanController = new \App\Http\Controllers\Loan\LoanController;
        $possibleLoans = $loanController->getMatchingLoans(
            $originSite->country_id,
            $quote->loan_amount,
            $quote->tenure,
            $quote->age
        );
        if (!empty($possibleLoans)) {
            return response()->json([
                'content' => 'catered',
                'loans' => $loanController->scoreLoans($possibleLoans)
            ]);
        } else {
            return response()->json([
                'content' => 'general',
                'loans' => $loanController->generalScoringByCid($originSite->country_id)
            ]);
        }
    }

    protected function buildFailedValidationResponse(Request $request, array $errors)
    {
        return [
            'message'=> 'failed' ,
            'reason' =>  'validation_error',
            'errors' => $errors
        ];
    }
}
