<?php

namespace App\Http\Controllers\Tracking;

use Illuminate\Http\Response;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Conversion;
use App\Click;

class ConversionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function registerConversion($clickId, Request $request)
    {
        if (!isset($clickId) || empty($clickId)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'missing inputs',
                'required' => ['clickId']
            ], 400);
        }
        $click = Click::findOrFail($clickId);
        
        $args = [
            'payout' => $request->input('payout'),
            'currency' => $request->input('currency'),
            'ex1' => $request->input('ex1'),
            'ex2' => $request->input('ex2'),
            'ex3' => $request->input('ex3'),
        ];
        $args['payout'] = str_replace(',', '.', $args['payout']);

        $conversion = new Conversion;
        $conversion->click_id  = $clickId;
        $conversion->payout    = $args['payout'];
        $conversion->currency  = $args['currency'];
        $conversion->ex1       = $args['ex1'];
        $conversion->ex2       = $args['ex2'];
        $conversion->ex3       = $args['ex3'];

        if ($conversion->save()) {
            return response()->json([
                'status' => 'success',
                'conversionId' => $conversion->id
            ], 200);
        }
    }
}
