<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\MetricValue;

class MetricStatsController extends Controller
{
    public function userDaily(Request $request): JsonResponse
    {
        // fetch params
        $externalId = $request->query('external_id');
        $metricKey = $request->query('metric_key');
        $date = $request->query('date', '2024-12-18'); // defalut value for date

        // verify all params
        if (!$externalId || !$metricKey) {
            return response()->json([
                'success' => false,
                'message' => 'The required parameters (external_id, metric_key) are missing.'
            ], 400);
        }

        
        $output = [];
        $output[] = ['date' => $date, 'value' => 102]; 

        return response()->json($output);
    }
}
