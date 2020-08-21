<?php

namespace App\Http\Controllers\Loan;

use Illuminate\Http\Response;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Loan;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function create(Request $request)
    {
        $loan = new Loan($request->all());

        return response()->json([
            'status' => 'loan created',
            'loan_id' => $loan->id
        ], 201);
    }

    public function getAll()
    {
        return Site::all()->map->only([
            'id',
            'name',
            'image_name',
            'url',
            'country_id',
            'created_at'
        ]);
    }

    public function getById($siteId)
    {
        return Site::findOrFail($siteId, [
            'id',
            'name',
            'image_name',
            'url',
            'country_id',
            'created_at'
        ]);
    }

    public function updateById($siteId, Request $request)
    {
        $site = Site::findOrFail($siteId);
        if ($site->update($request->all())) {
            return response()->json([
                'status' => 'success',
                'message' => $request->all()
            ], 200);
        }
        return false;
    }

    public function deleteById($siteId)
    {
        $site = Site::findOrFail($siteId);
        if ($site->delete()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Site #' . $siteId . ' has been removed.'
            ]);
        }
    }

    public function getMatchingLoans($country_id, $amount = null, $tenure = null, $age = null) {
        $possibleLoans = Loan::where('active', 1)
                            ->where('country_id', $country_id)
                            ->when(!is_null($amount) && $amount > 0, function($q) use ($amount) {
                                $q->where('min_amount', '<=', $amount);
                                $q->where('max_amount', '>=', $amount);
                            })
                            ->when(!is_null($tenure) && $tenure > 0, function($q) use ($tenure) {
                                $q->where('min_tenure', '<=', $tenure);
                                $q->where('max_tenure', '>=', $tenure);
                            })
                            ->when(!is_null($tenure) && is_numeric($age) && $age > 0, function ($q) use ($age) {
                                $q->where('min_age', '>=', $age);
                            })
                            ->get()->toArray();
        return $possibleLoans;
    }

    public function scoreLoans($loans) {
        // Basic scoring; further sort array by either CR or EPC - select n entries. 
        // TODO: Handle currencies
        for ($i = 0; $i < count($loans); $i++) {
            $clicks = DB::table('clicks')
                    ->where('loan_id', '=', $loans[$i]['id'])
                    ->get();

            $conversions = DB::table('conversions')
                            ->select(
                                DB::raw('count(id) as totalConversions'), 
                                DB::raw('sum(payout) as totalPayout')
                            )
                            ->whereIn('click_id', $clicks->pluck('id'))
                            ->groupBy('id')
                            ->get();

            $totalConversions = $conversions->pluck('totalConversions')->first() ?? 0;
            $totalPayout      = $conversions->pluck('totalPayout')->first() ?? 0;
            $totalClicks      = count($clicks) ?? 0;

            if ($totalClicks == 0) {
                $loans[$i]['epc'] = 0;
                $loans[$i]['cr'] = 0;
            } else {
                $loans[$i]['epc'] = round(($totalPayout / $totalClicks), 3);
                $loans[$i]['cr'] = round(($totalConversions / $totalClicks) * 100, 3);
            }
        }
        return $loans;
    }

    public function generalScoringByCid($countryId) {
        $loans = $this->getMatchingLoans($countryId);
        return $this->scoreLoans($loans);
    }


}