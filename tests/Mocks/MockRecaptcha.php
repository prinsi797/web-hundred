<?php

namespace Tests\Mocks;

use Illuminate\Contracts\Validation\Rule;

class MockRecaptcha implements Rule {

    public function passes($attribute, $value) {
        // Check the provided value to determine the verification outcome
        if ($value === 'valid-recaptcha-response') {
            // Simulate successful reCAPTCHA verification
            return true;
        } else {
            // Simulate failed reCAPTCHA verification
            return false;
        }
    }

    public function message() {
        // You can optionally provide a custom validation message here
        return 'The reCAPTCHA verification failed.';
    }
}
