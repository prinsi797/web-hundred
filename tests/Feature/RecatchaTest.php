<?php

use Tests\Mocks\MockRecaptcha;
use Tests\TestCase;

class RecaptchaTest extends TestCase {
  public function testRecaptchaValidationPasses() {
    $rule = new MockRecaptcha();
    $this->assertTrue($rule->passes('g-recaptcha-response', 'valid-recaptcha-response'));
  }

  public function testRecaptchaValidationFails() {
    $rule = new MockRecaptcha();
    $this->assertFalse($rule->passes('g-recaptcha-response', 'invalid-recaptcha-response'));
  }
}
