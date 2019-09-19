<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Repositories\RealEstateRepository;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class NewsAPIController
 * @package App\Http\Controllers\API
 */

class NewsAPIController extends AppBaseController
{
    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/news/rss.xml",
     *      summary="RSS feed of news",
     *      tags={"RSS"},
     *      description="Get RSS feed of news",
     *      produces={"application/xml"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *      )
     * )
     */
    public function showNewsRSS(Request $request)
    {
        $feed = Cache::remember('rss_feed', 600, function() {
            return file_get_contents("https://www.blick.ch/news/schweiz/rss.xml");
        });

        return response($feed, 200, [
            'Content-Type' => 'application/xml'
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/news/weather.json",
     *      summary="JSON feed of weather",
     *      tags={"weather"},
     *      description="Get json feed of weather",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *      )
     * )
     */
    public function showWeatherJSON(Request $request, RealEstateRepository $reRepo)
    {
        $zip = $this->getZip($reRepo);
        $feed = Cache::remember('weather_at_' . $zip, 60*60, function() use ($reRepo, $zip) {
            $appid = env('OPENWEATHERMAP_API_KEY');
            $countryCode = env('OPENWEATHERMAP_COUNTRY_CODE', 'ch');
            $zipCountry = $zip . ',' . $countryCode;
            $url = "http://api.openweathermap.org/data/2.5/weather?zip=" . $zipCountry . "&appid=" . $appid;

            return file_get_contents($url);
        });

        return response($feed, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
    private function getZip($reRepo)
    {
        $u = \Auth::user();
        if ($u->tenant && $u->tenant->address && $u->tenant->address->zip) {
            return $u->tenant->address->zip;
        }
        $defaultZip = 3172;
        $realEstate = $reRepo->first();
        if (empty($realEstate)) {
            return $defaultZip;
        }
        if (!isset($realEstate->address)) {
            return $defaultZip;
        }

        return $realEstate->address->zip;
    }
}
