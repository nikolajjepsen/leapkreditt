<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use App\Site;
use Illuminate\Support\Facades\DB;

class SiteController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function createSite(Request $request)
    {
        if (!$request->has(['name', 'url', 'countryCode'])) {
            return response()->json([
                'status' => 'failed',
                'message' => 'missing inputs',
                'required' => ['name', 'url', 'countryCode']
            ], 400);
        }

        // Validate request key values
        $this->validate($request, [
            'name'        => 'required',
            'url'         => 'required|unique:sites',
            'countryCode' => 'required|max:3'
        ]);

        $site = new Site();
        $site->name          = $request->input('name');
        $site->url           = $request->input('url');
        $site->country_code  = $request->input('countryCode');

        if ($site->save()) {
            return response()->json([
                'status' => 'success',
                'siteId' => $site->id
            ], 200);
        }
    }

    public function getSites()
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

    public function getSiteById($siteId)
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

    public function updateSiteById($siteId, Request $request)
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

    public function deleteSiteById($siteId)
    {
        $site = Site::findOrFail($siteId);
        if ($site->delete()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Site #' . $siteId . ' has been removed.'
            ]);
        }
    }

    private function getQuotesById($siteId) {
        $quotes = DB::table('quotes')
                    ->where('site_id', $siteId);

        return $quotes;
    }

    private function getClicksById($siteId) {
        $clicks = DB::table('clicks')
                    ->join('quotes', function($join) use ($siteId) {
                        $join->on('clicks.quote_id', '=', 'quotes.id')
                             ->where('quotes.site_id', '=', $siteId);
                    })
                    ->select('clicks.*');

        return $clicks;
    }

    private function getConversionsById($siteId) {
        $clicks = $this->getClicksById($siteId);
        $conversions = DB::table('conversions')
                        ->joinSub($clicks, 'clicks', function($join) {
                            $join->on('conversions.click_id', '=', 'clicks.id');
                        })
                        ->select('conversions.*');
        return $conversions;
    }

    public function displayQuotesById($siteId){
        $site = $this->getSiteById($siteId);
        $quotes = $this->getQuotesById($siteId);
        echo $quotes->get()->toJson();
    }
    public function displayClicksById($siteId){
        $site = $this->getSiteById($siteId);
        $clicks = $this->getClicksById($siteId);
        echo $clicks->get()->toJson();
    }
    public function displayConversionsById($siteId){
        $site = $this->getSiteById($siteId);
        $conversions = $this->getConversionsById($siteId);
        echo $conversions->get()->toJson();
    }

    public function displaySummaryById($siteId) {
        $quotes = $this->getQuotesById($siteId);
        $quotesCount = $quotes->count() ?? 0;

        $clicks = $this->getClicksById($siteId);
        $clicksCount = $clicks->count() ?? 0;

        $conversions = $this->getConversionsById($siteId);
        $conversionsCount = $conversions->count() ?? 0;

        $revenue = $conversions->sum('payout') ?? 0;

        $epc = $revenue / $clicksCount ?? 0;
        $cr = $conversionsCount / $clicksCount * 100 ?? 0;

        $summary = [
            'quotes' => $quotesCount,
            'clicks' => $clicksCount,
            'conversions' => $conversionsCount,
            'revenue' => $revenue,
            'epc' => $epc,
            'cr' => $cr
        ];

        return response()->json($summary);
    }

}
