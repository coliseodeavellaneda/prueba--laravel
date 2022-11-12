<?php

namespace App\Http\Controllers;

use App\Http\Services\GoogleAnalyticsService;
use App\Models\Registro;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoogleAnalyticsController extends Controller
{
    protected GoogleAnalyticsService $googleAnalyticsService;

    public function __construct()
    {
        $this->googleAnalyticsService = new GoogleAnalyticsService;
    }

    public function index(Request $request)
    {
        $data = $request->all();

        if (
            key_exists('inicio', $data)
            && key_exists('fin', $data)
        ) {
            $from = date($data['inicio']);
            $to = date($data['fin']);
            $registros = Registro::whereBetween('fecha', [$from, $to])->orderBy('fecha', 'asc')->get();
        } else {
            $registros = Registro::all();
        }

        $sesiones = [];
        $fechas = [];

        if (key_exists('inicio', $data) && key_exists('fin', $data)) {
            $period = CarbonPeriod::create(
                $data['inicio'],
                Carbon::createFromFormat('Y-m-d', $registros->pluck('fecha')->all()[0])
                    ->subDay(1)->format('Y-m-d')
            );
            foreach ($period as $date) {
                $sesiones[] = 0;
                $fechas[] = $date->format('Y-m-d');
            }
        }

        $sesiones = array_merge($sesiones, $registros->pluck('sesiones')->all());
        $fechas = array_merge($fechas, $registros->pluck('fecha')->all());

        if (key_exists('inicio', $data) && key_exists('fin', $data)) {
            $period = CarbonPeriod::create(
                Carbon::createFromFormat('Y-m-d', $fechas[array_key_last($fechas)])
                    ->addDay(1)->format('Y-m-d'),
                $data['fin']
            );
            foreach ($period as $date) {
                $sesiones[] = 0;
                $fechas[] = $date->format('Y-m-d');
            }
        }

        return new JsonResponse([
            'sesiones' => $sesiones,
            'categorias' => $fechas
        ]);
    }

    public function store()
    {
        try {
            $accessToken = $this->googleAnalyticsService
                ->getAccessToken();

            $url = env("GOOGLE_ANALYTICS_RETRIEVE_URL");

            $startDate = "2022-09-01";
            $endDate = "2022-10-31";
            $day = 0;
            while (
                Carbon::createFromFormat('Y-m-d', $startDate)

                ->addDay($day + 1)->format('Y-m-d') != $endDate
            ) {

                $sum = 0;

                $fromDate = Carbon::createFromFormat('Y-m-d', $startDate)
                    ->addDay($day)->format('Y-m-d');


                $toDate = Carbon::createFromFormat('Y-m-d', $startDate)
                    ->addDay($day + 1)->format('Y-m-d');


                $data = $this->googleAnalyticsService

                    ->getDataFromApi($url, $accessToken, $fromDate, $toDate);

                $data = $data["rows"];

                foreach ($data as $value) {
                    $sum += $value['metricValues'][0]['value'];
                }
                $storeData = ['fecha' => $fromDate, 'sesiones' => $sum];

                Registro::create($storeData);
                $day++;
            }
        } catch (\Exception $e) {
        }
        return view('chart');
    }
}
