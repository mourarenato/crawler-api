<?php

namespace App\Jobs;

use App\Models\Iso4217;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaveIso4217DataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;
    public $tries = 1;

    public function __construct(
        private string $data,
    ){}

    public function handle(): void
    {
        try {
            $this->saveIso4217Data();
            Log::info('Data saved with success!');
        } catch (Exception $e) {
            Log::error('Error when trying to save iso4217 data', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function saveIso4217Data()
    {
        $dataArray = json_decode($this->data, true);
        $listNumber = [];

        foreach ($dataArray as $item) {
            $listNumber[] = $item['number'];
        }

        $records = DB::table('iso4217')
            ->whereIn('number', $listNumber)
            ->get();

        if ($records->isEmpty()) {
            foreach ($dataArray as $item) {
                $model = new Iso4217();
                $model->code = $item['code'];
                $model->number = $item['number'];
                $model->decimal = $item['decimal'];
                $model->currency = $item['currency'];
                $currencyLocationsData = implode(',', $item['currency_locations']);
                $model->currency_locations = $currencyLocationsData;
                $model->save();
            }
        }
    }
}