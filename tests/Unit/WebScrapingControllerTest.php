<?php

namespace Unit;

use App\Http\Controllers\WebScrapingController;
use App\Models\Iso4217;
use App\Services\SpiderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;
use GuzzleHttp\Psr7\Response;

class WebScrapingControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testScrapeDataWithSuccessWhenCodeParamIsSent()
    {
        $scrapeData = Iso4217::factory()->create();

        $spiderServiceMock = $this->getMockBuilder(SpiderService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $spiderServiceMock->expects($this->any())
            ->method('callSpider')
            ->willReturn(new Response(200, [], json_encode(["response" => ["row0" => $scrapeData]])));

        $controller = new WebScrapingController();

        $request = new Request(['code' => $scrapeData->code]);

        $response = $controller->scrapeData($request);

        $this->assertEquals($scrapeData->number, $response->getData()->number);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testScrapeDataWithSuccessWhenCodeListParamIsSent()
    {
        $row0 = Iso4217::factory()->create();
        $row1 = Iso4217::factory()->create([
            'code' => 'GBP',
            'number' => 826,
            'decimal' => 2,
            'currency' => 'Libra Esterlina',
            'currency_locations' => 'Reino Unido,Ilha de Man,Guernesey,Jersey',
        ]);

        $spiderServiceMock = $this->getMockBuilder(SpiderService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $spiderServiceMock->expects($this->any())
            ->method('callSpider')
            ->willReturn(new Response(200, [], json_encode(["response" => ["row0" => $row0, "row1" => $row1]])));

        $controller = new WebScrapingController();

        $request = new Request(['code_list' => [$row0->code, $row1->code]]);

        $response = $controller->scrapeData($request);

        $this->assertEquals($row0->number, $response->getData()[0]->number);
        $this->assertEquals($row1->number, $response->getData()[1]->number);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testScrapeDataWithSuccessWhenNumberParamIsSent()
    {
        $scrapeData = Iso4217::factory()->create();

        $spiderServiceMock = $this->getMockBuilder(SpiderService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $spiderServiceMock->expects($this->any())
            ->method('callSpider')
            ->willReturn(new Response(200, [], json_encode(["response" => ["row0" => $scrapeData]])));

        $controller = new WebScrapingController();

        $request = new Request(['number' => $scrapeData->number]);

        $response = $controller->scrapeData($request);

        $this->assertEquals($scrapeData->number, $response->getData()->number);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testScrapeDataWithSuccessWhenNumberListParamIsSent()
    {
        $row0 = Iso4217::factory()->create();
        $row1 = Iso4217::factory()->create([
            'code' => 'GBP',
            'number' => 826,
            'decimal' => 2,
            'currency' => 'Libra Esterlina',
            'currency_locations' => 'Reino Unido,Ilha de Man,Guernesey,Jersey',
        ]);

        $spiderServiceMock = $this->getMockBuilder(SpiderService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $spiderServiceMock->expects($this->any())
            ->method('callSpider')
            ->willReturn(new Response(200, [], json_encode(["response" => ["row0" => $row0, "row1" => $row1]])));

        $controller = new WebScrapingController();

        $request = new Request(['number_list' => [$row0->number, $row1->number]]);

        $response = $controller->scrapeData($request);

        $this->assertEquals($row0->number, $response->getData()[0]->number);
        $this->assertEquals($row1->number, $response->getData()[1]->number);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testScrapeDataFailsWhenInvalidParamFormatIsSent()
    {
        $request = new Request(['code' => 32]);

        $controller = new WebScrapingController();

        $response = $controller->scrapeData($request);

        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testScrapeDataFailsWhenInvalidInputDataIsSent()
    {
        $request = new Request(['hello' => "BRL"]);

        $controller = new WebScrapingController();

        $response = $controller->scrapeData($request);

        $this->assertEquals('Invalid input data', $response->getData()->message);
        $this->assertEquals(500, $response->getStatusCode());
    }
}
