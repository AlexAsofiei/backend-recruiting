<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\MetricValue;
use Illuminate\Support\Facades\Validator;
use App\Models\Metric;

class DataUploadController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        $file = $request->file('file');
        $contents = file_get_contents($file->getRealPath());

        $lines = explode(PHP_EOL, $contents);
        $data = [];
        $metrics = [];
        $csvHeaders = ['achieved_at', 'metric_key', 'external_id', 'value'];
        $lines = array_slice($lines, 1);

        foreach ($lines as $line) {
            //ignore empty lines
            if (empty($line)) {
                continue;
            }

            //transform every line to array
            $row = str_getcsv($line);
            $mappedRow = array_combine($csvHeaders, $row);

            $validator = Validator::make($mappedRow, [
                'achieved_at' => 'required',
                'metric_key' => 'required',
                'external_id' => 'required',
                'value' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Invalid data']);
            }

            //check if metric_key exists in Metric
            $metricKey = $mappedRow['metric_key'];

            if (!isset($metrics[$metricKey])) {
                //if doesn't exist, we'll add it
                $metric = Metric::firstOrCreate(['metric_key' => $metricKey]);

                //save the id for further use
                $metrics[$metricKey] = $metric->id;
            }

            $mappedRow['metric_id'] = $metrics[$metricKey];

            unset($mappedRow['metric_key']);

            $data[] = $mappedRow;
        }
        MetricValue::insert($data);

        return response()->json([
            'success' => true,
            'message' => 'Data uploaded successfully',
        ]);
    }
}
