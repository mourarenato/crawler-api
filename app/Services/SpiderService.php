<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SpiderService
{
    public function __construct(
        private array $codes,
        private Client $client
    ){}

    public function callSpider()
    {
        try {
            $url = 'http://10.10.0.92:5000/scrape';
            $formattedCodes = $this->formatCodes();

            $response = $this->client->post($url, [
                'json' => ['codes' => $formattedCodes],
            ]);

            return $response;
        } catch (Exception $e) {
            Log::error('Error when trying to scraping data', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function formatCodes()
    {
        return implode(",", $this->codes);
    }

}