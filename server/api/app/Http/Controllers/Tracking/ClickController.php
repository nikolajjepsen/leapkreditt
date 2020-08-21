<?php

namespace App\Http\Controllers\Tracking;

use Illuminate\Http\Response;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Click;
use App\Loan;
use Illuminate\Support\Facades\DB;

class ClickController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function registerClick($quoteId, $loanId)
    {
        if (
            (!isset($quoteId) || empty($quoteId)) ||
            (!isset($loanId) || empty($loanId))
        ) {
            return response()->json([
                'status' => 'failed',
                'message' => 'missing/invalid inputs',
                'required' => ['quoteId', 'loanId']
            ], 400);
        }

        $click = new Click;
        $click->quote_id = $quoteId;
        $click->loan_id = $loanId;
        if ($click->save()) {
            $loan = Loan::where('id', $loanId)->firstOrFail();
            $url = str_replace('{clickId}', $click->id, $loan->url);

            return response()->json([
                'status' => 'success',
                'clickId' => $click->id,
                'redirectLink' => $url
            ], 200);
        }
    }
}
