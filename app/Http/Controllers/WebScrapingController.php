<?php

namespace App\Http\Controllers;
use App\Jobs\SaveIso4217DataJob;
use App\Models\Iso4217;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Services\SpiderService;

class WebScrapingController extends Controller
{
    public function scrapeData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'nullable|string',
                'code_list' => 'nullable|array|min:1',
                'number' => 'nullable|numeric',
                'number_list' => 'nullable|array|min:1',
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->errors());
            }

            $expectedInputs = ['code', 'code_list', 'number', 'number_list'];

            $providedInputs = array_values(array_intersect($expectedInputs, array_keys($request->all())));

            if (count($providedInputs) !== 1) {
                Log::error('Invalid input data');
                throw new Exception('Invalid input data');
            }

            $inputType = $providedInputs[0];
            $inputValue = $request->input($inputType);

            $result = match ($inputType) {
                'code' => $this->processUniqueInputValue($inputValue, 'code'),
                'code_list' => $this->processListInputValue($inputValue, 'code'),
                'number' => $this->processUniqueInputValue($inputValue, 'number'),
                'number_list' => $this->processListInputValue($inputValue, 'number'),
                default => throw new Exception('Input value not supported'),
            };

            return $result;

        } catch (Exception $e) {
            Log::error('Error when trying to scraping data', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function dispatchSaveIso4217DataJob(string $inputValue): void
    {
        SaveIso4217DataJob::dispatch($inputValue);
    }

    private function callSpiderService($codes)
    {
        $spider = new SpiderService($codes, new Client(['http_errors' => false]));
        return $spider->callSpider();
    }

    private function processUniqueInputValue($inputValue, string $dbColumn)
    {
        if ($result = Iso4217::where($dbColumn, $inputValue)->first()) {
            $result->currency_locations = explode(',', $result->currency_locations);
            return response()->json($result, 200);
        }
        return $this->processInputValue($inputValue);
    }

    private function processListInputValue($inputValue, string $dbColumn)
    {
        $records = DB::table('iso4217')
            ->whereIn($dbColumn, $inputValue)
            ->get();

        foreach ($records as $item) {
            $item->currency_locations = explode(',', $item->currency_locations);
        }

        if (!$records->isEmpty()) {
            return response()->json($records, 200);
        }
        return $this->processInputValue($inputValue);
    }

    private function processInputValue($inputValue)
    {
        if (is_array($inputValue)) {
            $output = $this->callSpiderService($inputValue);
        }

        if (!is_array($inputValue)) {
            $output = $this->callSpiderService([$inputValue]);
        }

        if (!$output) {
            return response()->json(['response' => 'Error when trying to scrape data'], 500);
        }

        if ($output->getStatusCode() === 404) {
            return response()->json(['data' => []], 200);
        }

        $data = json_decode($output->getBody(), true);
        $data = json_decode($data['response']);

        $result = [];

        foreach ($data as $row) {
            $result[] = $row;
        }

        $this->dispatchSaveIso4217DataJob(json_encode($result));

        return response()->json($result, 200);
    }
}