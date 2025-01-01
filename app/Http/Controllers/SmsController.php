<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class SmsController extends Controller {
    private $sid;
    private $token;
    private $serviceId;
    private $isTwilioEnable;
    private $twilioClient;

    public function __construct() {
        $this->sid =  getenv("TWILIO_SID");
        $this->token = getenv("TWILIO_TOKEN");
        $this->serviceId = getenv("TWILIO_SERVICE_ID");
        $this->isTwilioEnable = env('TWILIO_ENABLE');
        if ($this->isTwilioEnable == 1) {
            $this->twilioClient = new Client($this->sid, $this->token);
        }
    }

    public function sendVerificationCode($phoneNumber) {
        $countryCode = "+1";
        $phoneNumberWithCountryCode = $countryCode . $phoneNumber;
        $response = null;
        $dunkyPhoneNumbers = dunkyPhoneNumbers();
       
        if ($this->isTwilioEnable == 1 && !in_array($phoneNumber, $dunkyPhoneNumbers)) {
            $response = $this->twilioClient->verify->v2->services($this->serviceId)
                ->verifications
                ->create($phoneNumberWithCountryCode, "sms");
            Log::info("Twilio Verification Code Sent Attempt: ", ['response' => $response]);
        } else {
            Log::info("Twilio Verification Code is Disabled: ", ['phoneNumber' => $phoneNumber]);
        }
        return $response;
    }

    public function verifyCode($phoneNumber, $code) {
        $countryCode = "+1";
        $phoneNumberWithCountryCode = $countryCode . $phoneNumber;
        $response = null;
        $dunkyPhoneNumbers = dunkyPhoneNumbers();
       
        if ($this->isTwilioEnable == 1 && !in_array($phoneNumber, $dunkyPhoneNumbers)){
            $response = $this->twilioClient->verify->v2->services($this->serviceId)
                ->verificationChecks
                ->create([
                    'to' => $phoneNumberWithCountryCode,
                    'code' => $code,
                ]);
            Log::info("Twilio Verification Result: ", ['response' => $response]);
        } else {
            Log::info("Twilio Verification Code is Disabled: ", ['phoneNumber' => $phoneNumber]);
        }
        return $response;
    }
}
