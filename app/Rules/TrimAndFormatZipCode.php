<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class TrimAndFormatZipCode implements Rule
{
    public function __construct()
    {
        //
    }

    public function passes($attribute, $value)
    {
        $value = preg_replace('/\s+/', '', $value);

        $zipCodes = explode(',', $value);
        $formattedZipCodes = [];
    
        foreach ($zipCodes as $zipCode) {
            $cleanZip = preg_replace('/[^0-9]/', '', $zipCode);
            if (!empty($cleanZip)) {
                $formattedZipCodes[] = trim($cleanZip);
            }
        }
        if (empty($formattedZipCodes)) {
            return false;
        }
        request()->merge([$attribute => implode(',', $formattedZipCodes)]);
        return true;
    }
    public function message()
    {
        return 'Please enter at least one valid zip code.';
    }
}
