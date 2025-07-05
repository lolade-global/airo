<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\QuotationService;
use App\Http\Requests\QuotationRequest;
use App\Http\Resources\QuotationResource;

class QuotationController extends Controller
{
    public function generateQuotation (QuotationRequest $request, QuotationService $quotationService): QuotationResource | JsonResponse
    {
        try {
            $quotation = $quotationService->generate($request);

            return (new QuotationResource($quotation))
                ->additional(['message' => 'quotation fetched successfully']);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error', 'message' => $e->getMessage()], 500);
        }
    }
}
