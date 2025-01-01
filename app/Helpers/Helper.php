<?php

use App\Models\Contact;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

/**
 * Created by Akram Chauhan
 */

function kview($view_path, $array = []) {
  $new_v_path = 'theme.' . $view_path;
  $array['new_view'] = $new_v_path;
  return view($new_v_path, $array);
}

function prettyDate($timestamp) {
  return Date('d M, Y', $timestamp);
}

function separateCountryCodeAndNumber($phoneNumber) {
  $countryCode = null;
  $cleanedNumber = $phoneNumber;

  if (substr($phoneNumber, 0, 2) === '91' && strlen($phoneNumber) > 10) {
      $countryCode = '91';
      $cleanedNumber = substr($phoneNumber, 2);
  } elseif (substr($phoneNumber, 0, 1) === '1') {
      $countryCode = '1';
      $cleanedNumber = substr($phoneNumber, 1);
  }

  return [
      'countryCode' => $countryCode,
      'phoneNumber' => $cleanedNumber,
  ];
}

function default_permissions() {
  return [
    'list',
    'update',
    'add',
  ];
}

function verifySlug($table, $slug_name, $str) {
  $existing_slug =  $table::where($slug_name, 'like', $str . '%')->orderBy('id', 'desc');
  if ($existing_slug->count() > 0) {
    $db_obj = $existing_slug->first();
    $slug_name = $db_obj->slug;
    $slug_arr = explode("-", $slug_name);
    $count_slug_arr = count($slug_arr);
    $last_slug_str = $slug_arr[$count_slug_arr - 1];
    // dd(($last_slug_str));
    if (ctype_digit($last_slug_str)) {
      $last_slug_num = (int)$last_slug_str + 1;
      $slug_arr[$count_slug_arr - 1] = $last_slug_num;
    } else {
      $slug_arr[$count_slug_arr] = 1;
    }
    $str = implode("-", $slug_arr);
  }
  return $str;
}

function logos() {
  return [
    'black' => asset('assets/images/logo_black.png'),
    'white' => asset('assets/images/logo_white.png'),
  ];
}

function getStates() {
  return [
    'AL' => 'Alabama',
    'AK' => 'Alaska',
    'AZ' => 'Arizona',
    'AR' => 'Arkansas',
    'CA' => 'California',
    'CO' => 'Colorado',
    'CT' => 'Connecticut',
    'DE' => 'Delaware',
    'DC' => 'District of Columbia',
    'FL' => 'Florida',
    'GA' => 'Georgia',
    'HI' => 'Hawaii',
    'ID' => 'Idaho',
    'IL' => 'Illinois',
    'IN' => 'Indiana',
    'IA' => 'Iowa',
    'KS' => 'Kansas',
    'KY' => 'Kentucky',
    'LA' => 'Louisiana',
    'ME' => 'Maine',
    'MD' => 'Maryland',
    'MA' => 'Massachusetts',
    'MI' => 'Michigan',
    'MN' => 'Minnesota',
    'MS' => 'Mississippi',
    'MO' => 'Missouri',
    'MT' => 'Montana',
    'NE' => 'Nebraska',
    'NV' => 'Nevada',
    'NH' => 'New Hampshire',
    'NJ' => 'New Jersey',
    'NM' => 'New Mexico',
    'NY' => 'New York',
    'NC' => 'North Carolina',
    'ND' => 'North Dakota',
    'OH' => 'Ohio',
    'OK' => 'Oklahoma',
    'OR' => 'Oregon',
    'PA' => 'Pennsylvania',
    'RI' => 'Rhode Island',
    'SC' => 'South Carolina',
    'SD' => 'South Dakota',
    'TN' => 'Tennessee',
    'TX' => 'Texas',
    'UT' => 'Utah',
    'VT' => 'Vermont',
    'VA' => 'Virginia',
    'WA' => 'Washington',
    'WV' => 'West Virginia',
    'WI' => 'Wisconsin',
    'WY' => 'Wyoming',
  ];
}

function convertNullToEmptyStrings($array) {
  foreach ($array as $key => $value) {
    if ($value == null) $array[$key] = '';
  }
  return $array;
}

function formatPhoneNumber($phoneNumber) {
  return preg_replace('/[^0-9]/', '', $phoneNumber);
}

function generateOTP() {
  return str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT);
}

function generateRandomUsername() {
  $randomDigits = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
  $randomChars = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);
  $username =  $randomDigits . $randomChars;
  return $username;
}

function addRank(&$data) {
  usort($data, function ($a, $b) {
    return $b['total_count'] - $a['total_count'];
  });

  $rank = 1;
  foreach ($data as &$item) {
    $item['rank'] = $rank++;
  }
}

function dunkyPhoneNumbers() {
  return [
    '8511692061',
    '9601106151',
  ];
}