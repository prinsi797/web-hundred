<?php

use Tests\TestCase;
use Illuminate\Support\Facades\View;

class HelperFunctionsTest extends TestCase {

  /**
   * Test kview function.
   *
   * @return void
   */
  public function testKview() {
    // Arrange
    $viewPath = 'home';
    $array = [
      'is_admin' => true,
      'dashboard_cards' => [],
    ];

    // Act
    $result = kview($viewPath, $array);

    // Assert
    $this->assertInstanceOf(\Illuminate\View\View::class, $result); // Ensure it returns a View instance
    $this->assertEquals(View::make('theme.home', $array)->render(), $result->render());
    $this->assertEquals('theme.home', $result->getData()['new_view']);
  }

  public function testSeparateCountryCodeAndNumber()
    {
        $phoneNumbers = [
            "919876543210" => ["91", "9876543210"],
            "15551234567" => ["1", "5551234567"],
        ];

        foreach ($phoneNumbers as $input => $expected) {
            $result = separateCountryCodeAndNumber($input);

            if (is_array($result) && array_key_exists('countryCode', $result) && array_key_exists('phoneNumber', $result)) {
                $this->assertEquals($expected[0], $result['countryCode']);
                $this->assertEquals($expected[1], $result['phoneNumber']);
            } else {
                $this->fail('The function did not return the expected associative array');
            }
        }
    }

    public function testFormatPhoneNumber()
    {
        $this->assertEquals('9876543210', formatPhoneNumber('(987) 654-3210'));
    }

    public function testGenerateOTP()
    {
        $otp = generateOTP();
        $this->assertIsString($otp);
        $this->assertEquals(5, strlen($otp));

        $this->assertTrue(in_array($otp, array_map(function ($num) {
            return str_pad($num, 5, '0', STR_PAD_LEFT);
        }, range(0, 99999))));
    }

    public function testAddRank()
    {
        $data = [];
        addRank($data);
        $this->assertEmpty($data);
        $data = [
            ['total_count' => 10],
        ];
        addRank($data);
        $this->assertEquals([
            ['total_count' => 10, 'rank' => 1],
        ], $data);
        $data = [
            ['total_count' => 5],
            ['total_count' => 10],
            ['total_count' => 3],
            ['total_count' => 8],
        ];
        addRank($data);
        $this->assertEquals([
            ['total_count' => 10, 'rank' => 1],
            ['total_count' => 8, 'rank' => 2],
            ['total_count' => 5, 'rank' => 3],
            ['total_count' => 3, 'rank' => 4],
        ], $data);
    }

    
}
