<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ApplicationService
{
    public function __construct()
    {
        // Example logic for initializing ApiService
    }

    public function convertToFloat($currency)
    {
        // Remove the "Rp" currency symbol and any leading or trailing spaces
        $currency = trim(str_replace('Rp', '', $currency));

        // Remove any periods (thousands separator)
        $currency = str_replace('.', '', $currency);

        // Replace the comma with a period (decimal separator)
        $currency = str_replace(',', '.', $currency);

        // Convert the string to float and return
        return (float) $currency;
    }
}
