<?php

namespace App\Http\Controllers\Quote;

use Illuminate\Http\Response;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Site\SiteController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Validator;

use App\Mail\ConfirmIdentity;
use App\ConfirmCode;
use App\Quote;

use Carbon\Carbon;

class ConfirmController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }
    
    /**
     * validateCrationTime
     * Validates if creation time is within xx minutes.
     * @param  mixed $creationTime Y-m-d H:i:s
     * @param  mixed $periodLimit Number of minutes since creation for validity
     *
     * @return void
     */
    private function validateCreationTime($creationTime, $periodLimit)
    {
        $creation = Carbon::createFromFormat('Y-m-d H:i:s', $creationTime);
        $expirationTime = $creation->addMinutes($periodLimit);

        // If expiration time is greater than current time it's valid
        // 28-10-19 15:00 (exp) > 28-10-19 14:30 (current)
        if (Carbon::now()->lessThan($expirationTime)) {
            return true;
        }

        return false;
    }

    private function isActive($codeId, $quoteId)
    {
        $code = ConfirmCode::where('id', $codeId)->where('quote_id', $quoteId)->firstOrFail();
        if ($code->active == 1) {
            return true;
        }

        return false;
    }

    private function validateCode($codeId, $quoteId, $creationTime, $periodLimit)
    {
        if (!$this->validateCreationTime($creationTime, $periodLimit)) {
            return false;
        }
        
        if (!$this->isActive($codeId, $quoteId)) {
            return false;
        }

        return true;
    }

    private function generateConfirmCode($quoteId)
    {
        $code = new ConfirmCode;
        $code->quote_id = $quoteId;
        $code->code = mt_rand(100000, 999999);
        if ($code->save()) {
            return $code->code;
        } else {
            throw new \Exception('Unable to generate code');
        }
    }

    public function sendConfirmMail($quoteId)
    {

        $quote = Quote::findOrFail($quoteId);
        
        $code = null;
        $latestCode = ConfirmCode::where('quote_id', $quote->id)->orderBy('created_at', 'desc')->first();
        if ($latestCode) {
            if ($this->validateCode($latestCode->id, $quote->id, $latestCode->created_at, 5)) {
                $code = $latestCode->code;
            }
        }

        if ($code) {
            $confirmCode = $code;
        } else {
            try {
                $confirmCode = $this->generateConfirmCode($quote->id);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'failed',
                    'reason' => 'unable to generate code'
                ]);
            }
        }

        Mail::to($quote->email)
            ->send(new ConfirmIdentity($quote, $confirmCode));
        return response()->json([
            'status' => 'success',
            'reason' => 'Confirmation mail sent.'
        ], 200);
    }

    public function validateConfirmCode(Request $request, $quoteId)
    {
        // Check request keys
        if (!$request->has(['code']) || !is_numeric($request->input('code'))) {
            return response()->json(
                ['message' => 'failed','reason' => 'invalid arguments', 'required' => ['code']],
                400 // HTTP Code
            );
        }

        $quote = Quote::findOrFail($quoteId);
        $code = ConfirmCode::where('quote_id', $quote->id)->where('code', $request->input('code'))->firstOrFail();

        if (!$this->isActive($code->id, $quote->id)) {
            return response()->json([
                'status' => 'failed',
                'reason' => 'code no longer active'
            ], 410);
        }
        // Validate @ 10 minutes old code.
        if (!$this->validateCreationTime($code->created_at, 50)) {
            return response()->json([
                'status' => 'failed',
                'reason' => 'code expired'
            ], 410);
        }

        $code->active = 0;
        $code->save();

        return response()->json([
            'status' => 'success'
        ], 200);
    }
}
