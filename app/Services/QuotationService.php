<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Quotation;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;

class QuotationService
{
    public function __construct()
    {
        $this->fixedRate = Config::get('quotation.fixed_rate', 3);
        $this->ageLoadTable = Config::get('quotation.age_load_table', []);
    }

    public function generate($request): Quotation
    {
        $commaAge = $request->age;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $ages = array_map('intval', explode(',', $commaAge));
        $tripLength = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;

        $total = 0;

        foreach ($ages as $age) {
            $load = $this->getAgeLoad($age);
            $total += $this->fixedRate * $load * $tripLength;
        }

        $quotation = Quotation::create([
            'age' => $commaAge,
            'currency_id' => $request->currency_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total' => number_format($total, 2, '.', ''),
        ]);

        return $quotation;
    }

    private function getAgeLoad(int $age): float
    {
        foreach ($this->ageLoadTable as [$min, $max, $load]) {
            if ($age >= $min && $age <= $max) {
                return $load;
            }
        }

        throw ValidationException::withMessages([
            'age' => "Unsupported age: $age (must be between 18 and 70)",
        ]);
    }
}
