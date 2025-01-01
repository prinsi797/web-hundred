<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\File;

class HeifValidationRule implements Rule
{
    public function passes($attribute, $value)
    {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'heif', 'heic'];
        $mimeType = File::mimeType($value);
        $extension = strtolower($value->getClientOriginalExtension());

        return $mimeType && in_array($extension, $allowedExtensions);
    }

    public function message()
    {
        return 'The :attribute must be a valid image (jpg, jpeg, png, heif).';
    }
}
