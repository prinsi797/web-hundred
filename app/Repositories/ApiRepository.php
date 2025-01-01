<?php

namespace App\Repositories;

use App\Models\AppUser;
use App\Models\AppUserBenchpress;
use App\Models\AppUserContact;
use App\Models\AppUserDeadlift;
use App\Models\AppUserFriend;
use App\Models\AppUserHeight;
use App\Models\AppUserPowerclean;
use App\Models\AppUserSchool;
use App\Models\AppUserSquat;
use App\Models\AppUserWeight;
use App\Models\Feedback;
use App\Models\School;
use App\Models\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ApiRepository {
    public function userRegister($userData) {
        return AppUser::create($userData);
    }

    public function loginProcess($formattedPhoneNumber) {
        return AppUser::where('phone_number', $formattedPhoneNumber)->first();
    }
    public function verifyOTP($phoneNumber, $securityCode) {
        $phoneNumber = formatPhoneNumber($phoneNumber);

        $user = AppUser::where('phone_number', $phoneNumber)
            ->where('security_code', $securityCode)
            ->first();

        if ($user) {
            $user->security_code = null;
            $user->save();
        }
        return $user;
    }

    public function searchSchool() {
        $schools = School::get();

        return $schools->map(function ($school) {
            $imageUrl = $school->image_url;
            if ($imageUrl) {
                $fullUrl = Storage::disk('public')->url('SchoolImage/' . $imageUrl);
                $school->image_url = $fullUrl;
            }

            return [
                'id' => $school->id,
                'name' => $school->name,
                'short_name' => $school->short_name,
                'image_url' => $school->image_url,
            ];
        });
    }

    public function appUserSchoolStore($userId, $schoolId) {
        $existingSchool = AppUserSchool::where('app_user_id', $userId)
            ->first();

        if ($existingSchool) {
            $existingSchool->update(['school_id' => $schoolId]);
        } else {
            AppUserSchool::create([
                'app_user_id' => $userId,
                'school_id' => $schoolId,
            ]);
        }
    }

    public function schoolAdd($school, $request) {
        $school->save();

        if ($request->hasFile('image_url')) {
            $uploadedFile = $request->file('image_url');
            $filename = $uploadedFile->getClientOriginalName();
            $path = $uploadedFile->storeAs('SchoolImage', $filename, 'public');
            $school->image_url = $filename;
            $school->save();
        }

        if ($school->image_url) {
            $fullUrl = Storage::disk('public')->url('SchoolImage/' . $school->image_url);
            $school->image_url = $fullUrl;
        }

        return $school;
    }

    public function benchPressStore($requestData) {
        $user = Auth::user();
        $date = isset($requestData['date']) ? Carbon::parse($requestData['date'])->toDateString() : Carbon::now()->toDateString();

        $bench = AppUserBenchpress::where('app_user_id', $user->id)
            ->whereDate('date', $date)
            ->first();

        if ($bench) {
            $bench->bench_press = (int)$requestData['bench_press'];
            $bench->date = $date;
            $bench->save();
        } else {
            $bench = new AppUserBenchpress();
            $bench->bench_press = (int)$requestData['bench_press'];
            $bench->app_user_id = $user->id;
            $bench->date = $date;
            $bench->save();
        }
        return $bench;
    }

    public function deadliftStore($requestData, $date = null) {
        $user = Auth::user();
        $date = isset($requestData['date']) ? Carbon::parse($requestData['date'])->toDateString() : Carbon::now()->toDateString();

        $listType = AppUser::where('id', $user->id)->first();

        if ($listType) {
            $listType->lift_type = $requestData['lift_type'];
            $listType->save();
        }

        $deadlift = AppUserDeadlift::where('app_user_id', $user->id)
            ->whereDate('date', $date)
            ->first();

        if ($deadlift) {
            $deadlift->deadlift = (int)$requestData['deadlift'];
            $deadlift->date = $date;
            $deadlift->save();
        } else {
            $deadlift = new AppUserDeadlift();
            $deadlift->deadlift = (int)$requestData['deadlift'];
            $deadlift->app_user_id = $user->id;
            $deadlift->date = $date;
            $deadlift->save();
        }
        return $deadlift;
    }

    public function squatsStore($requestData) {
        $user = Auth::user();
        $date = isset($requestData['date']) ? Carbon::parse($requestData['date'])->toDateString() : Carbon::now()->toDateString();

        $squat = AppUserSquat::where('app_user_id', $user->id)
            ->whereDate('date', $date)
            ->first();

        if ($squat) {
            $squat->squat = (int)$requestData['squat'];
            $squat->date = $date;
            $squat->save();
        } else {
            $squat = new AppUserSquat();
            $squat->squat = (int)$requestData['squat'];
            $squat->app_user_id = $user->id;
            $squat->date = $date;
            $squat->save();
        }
        return $squat;
    }

    public function powercleansStore($requestData) {
        $user = Auth::user();
        $date = isset($requestData['date']) ? Carbon::parse($requestData['date'])->toDateString() : Carbon::now()->toDateString();

        $powerclean = AppUser::where('id', $user->id)->first();

        if ($powerclean) {
            $powerclean->lift_type = $requestData['lift_type'];
            $powerclean->save();
        }

        $powerclean = AppUserPowerclean::where('app_user_id', $user->id)
            ->whereDate('date', $date)
            ->first();

        if ($powerclean) {
            $powerclean->power_clean = (int)$requestData['power_clean'];
            $powerclean->date = $date;
            $powerclean->save();
        } else {
            $powerclean = new AppUserPowerclean();
            $powerclean->power_clean = (int)$requestData['power_clean'];
            $powerclean->app_user_id = $user->id;
            $powerclean->date = $date;
            $powerclean->save();
        }
        return $powerclean;
    }

    public function weightStore($requestData) {
        $user = Auth::user();
        $date = isset($requestData['date']) ? Carbon::parse($requestData['date'])->toDateString() : Carbon::now()->toDateString();

        $weight = AppUserWeight::where('app_user_id', $user->id)
            ->whereDate('date', $date)
            ->first();

        if ($weight) {
            $weight->weight = (int)$requestData['weight'];
            $weight->date = $date;
            $weight->save();
        } else {
            $weight = new AppUserWeight();
            $weight->weight = (int)$requestData['weight'];
            $weight->app_user_id = $user->id;
            $weight->date = $date;
            $weight->save();
        }
        return $weight;
    }

    public function liftTypeStore($requestData) {
        $user = Auth::user();
        $listType = AppUser::where('id', $user->id)->first();

        if ($listType) {
            $listType->lift_type = $requestData['lift_type'];
            $listType->save();
        }
        return $listType;
    }

    public function heightStore($requestData) {
        $user = Auth::user();
        $date = isset($requestData['date']) ? Carbon::parse($requestData['date'])->toDateString() : Carbon::now()->toDateString();

        $height = AppUserHeight::where('app_user_id', $user->id)
            ->whereDate('date', $date)
            ->first();

        if ($height) {
            $height->fit = $requestData['fit'];
            $height->inch = $requestData['inch'];
            $height->date = $date;
            $height->save();
        } else {
            $height = new AppUserHeight();
            $height->fit = $requestData['fit'];
            $height->inch = $requestData['inch'];
            $height->app_user_id = $user->id;
            $height->date = $date;
            $height->save();
        }
        return $height;
    }


    public function getUserStats($userId) {
        return Stat::where('app_user_id', $userId)->get();
    }

    public function getUserContacts($userId) {
        return AppUserContact::select('id', 'app_user_id', 'contact_firstname', 'contact_lastname', 'contact_phone_number')->where('app_user_id', $userId)->get();
    }

    public function getAppUserContacts($userId) {
        $userContacts = AppUserContact::select('id', 'app_user_id', 'contact_firstname', 'contact_lastname', 'contact_phone_number')->where('app_user_id', $userId)->get();

        $contactsNotInApp = [];
        foreach ($userContacts as $contact) {
            $user = AppUser::where('phone_number', $contact->contact_phone_number)->first();
            if (!$user) {
                $contactsNotInApp[] = $contact;
            }
        }
        return $contactsNotInApp;
    }

    public function getContactInApp($userId) {
        $userContacts = AppUserContact::where('app_user_id', $userId)->get();

        $contactsInApp = [];
        foreach ($userContacts as $contact) {
            $user = AppUser::where('phone_number', $contact->contact_phone_number)->first();
            if ($user) {
                $isAdded = AppUserFriend::where('app_user_id', $userId)
                    ->where('app_friend_id', $user->id)
                    ->exists();

                $contactsInApp[] = [
                    'id' => $contact->id,
                    'app_user_id' => $contact->app_user_id,
                    'user_id' => $contact->user_id,
                    'contact_firstname' => $contact->contact_firstname,
                    'contact_lastname' => $contact->contact_lastname,
                    'contact_phone_number' => $contact->contact_phone_number,
                    'is_added' => $isAdded, // Add is_added field
                ];
            }
        }
        return $contactsInApp;
    }

    public function storeUserContacts($userId, $contacts, $userPhoneNumber) {
        $createdContacts = [];

        foreach ($contacts as $contactData) {
            $normalizedPhoneNumber = formatPhoneNumber($contactData['contact_phone_number']);
            if ($normalizedPhoneNumber === $userPhoneNumber) {
                continue;
            }  

            $existingContact = AppUserContact::where('app_user_id', $userId)
                ->where('contact_phone_number', $normalizedPhoneNumber)
                ->first();

            if ($existingContact) {
                continue;
            }
            $phoneArray = separateCountryCodeAndNumber($normalizedPhoneNumber);
            $contact = AppUserContact::create([
                'contact_firstname' => $contactData['contact_firstname'],
                'contact_lastname' => $contactData['contact_lastname'],
                'contact_phone_number' => $phoneArray['phoneNumber'],
                'country_code' => $phoneArray['countryCode'],
                'app_user_id' => $userId,
            ]);

            $createdContacts[] = $contact;
        }

        return $createdContacts;
    }

    public function addFriend($userId, $friendId) {
        if ($friendId == $userId) {
            throw new \InvalidArgumentException('You cannot invite yourself');
        }

        if (AppUserFriend::where('app_user_id', $userId)->where('app_friend_id', $friendId)->exists()) {
            throw new \LogicException('Invitation already sent');
        }

        return AppUserFriend::create([
            'app_user_id' => $userId,
            'app_friend_id' => (int)$friendId,
            'is_added' => true,
        ]);
    }

    public function findById($userId) {
        return AppUser::find($userId);
    }

    //

    public function getLatestDeadlift($userId) {
        return AppUserDeadlift::select('id', 'date', 'deadlift')->where('app_user_id', $userId)->latest('date')->first();
        // return AppUserDeadlift::where('app_user_id', $userId)->latest('date')->first();
    }

    public function getLatestPowerClean($userId) {
        return AppUserPowerclean::select('id', 'date', 'power_clean')->where('app_user_id', $userId)->latest('date')->first();
        // return AppUserPowerclean::where('app_user_id', $userId)->latest('date')->first();
    }

    public function getLatestBenchPress($userId) {
        return AppUserBenchpress::select('id', 'date', 'bench_press')->where('app_user_id', $userId)->latest('date')->first();
        // return AppUserBenchpress::where('app_user_id', $userId)->latest('date')->first();
    }

    public function getLatestSquat($userId) {
        return AppUserSquat::select('id', 'date', 'squat')->where('app_user_id', $userId)->latest('date')->first();
        // return AppUserSquat::where('app_user_id', $userId)->latest('date')->first();
    }

    public function getUserSchools($userId) {
        return AppUserSchool::where('app_user_id', $userId)->get();
    }

    public function getSchoolById($schoolId) {
        return School::find($schoolId);
    }

    //
    public function getLatestLifts($userId) {
        $user = AppUser::find($userId);
        if (!$user) {
            return null;
        }
        $latestLifts = null;

        if ($user->lift_type === 'deadlift') {
            $latestDeadlift = AppUserDeadlift::select('id', 'date', 'deadlift')->where('app_user_id', $userId)->orderBy('date', 'desc')
                ->take(10)->get();
            $latestBenchPress = AppUserBenchpress::select('id', 'date', 'bench_press')->where('app_user_id', $userId)->orderBy('date', 'desc')
                ->take(10)->get();
            $latestSquat = AppUserSquat::select('id', 'date', 'squat')->where('app_user_id', $userId)->orderBy('date', 'desc')
                ->take(10)->get();

            $latestLiftsNull = $latestDeadlift === null && $latestBenchPress === null && $latestSquat === null;

            $latestLifts = $latestLiftsNull ? null : [
                'deadlift' => $latestDeadlift ?? null,
                'bench_press' => $latestBenchPress ?? null,
                'squat' => $latestSquat ?? null,
            ];

            if ($latestDeadlift->isEmpty() && $latestBenchPress->isEmpty() && $latestSquat->isEmpty()) {
                return null;
            }
        } elseif ($user->lift_type === 'power_clean') {
            $latestPowerClean = AppUserPowerclean::select('id', 'date', 'power_clean')->where('app_user_id', $userId)->orderBy('date', 'desc')
                ->take(10)->get();
            $latestBenchPress = AppUserBenchpress::select('id', 'date', 'bench_press')->where('app_user_id', $userId)->orderBy('date', 'desc')
                ->take(10)->get();
            $latestSquat = AppUserSquat::select('id', 'date', 'squat')->where('app_user_id', $userId)->orderBy('date', 'desc')
                ->take(10)->get();

            $latestLiftsNull = $latestPowerClean === null && $latestBenchPress === null && $latestSquat === null;

            $latestLifts = $latestLiftsNull ? null : [
                'power_clean' => $latestPowerClean ?? null,
                'bench_press' => $latestBenchPress ?? null,
                'squat' => $latestSquat ?? null,

            ];

            if ($latestPowerClean->isEmpty() && $latestBenchPress->isEmpty() && $latestSquat->isEmpty()) {
                return null;
            }
        }

        return $latestLifts;
    }

    public function createFeedback($userId, $message) {
        return Feedback::create([
            'app_user_id' => $userId,
            'message' => $message,
        ]);
    }
}
