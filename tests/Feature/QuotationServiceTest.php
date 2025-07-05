<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\QuotationService;
use App\Http\Requests\QuotationRequest;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuotationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_correct_total_for_given_ages_and_trip_length()
    {
        $service = new QuotationService();
        $request = new QuotationRequest([
            'age' =>'28,35', 'currency_id' => 'EUR', 'start_date' => '2020-10-01', 'end_date' => '2020-10-30'
        ]);

        $quotation = $service->generate($request);

        $this->assertEquals('117.00', $quotation->total); // Quotation model
        $this->assertEquals('EUR', $quotation->currency_id);
        $this->assertNotEmpty($quotation->id);
    }
}
