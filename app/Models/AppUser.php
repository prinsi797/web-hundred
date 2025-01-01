<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppUser extends Model implements JWTSubject {
    use HasApiTokens, HasFactory, SoftDeletes;
    protected $table = "app_users";

    protected $fillable = [
        'name',
        'dob',
        'phone_number',
        'security_code',
        'profile_photo_url',
        'username',
        'lift_type',
    ];

    public function stats() {
        return $this->hasOne(Stat::class);
    }

    public function getJWTIdentifier() {
        return $this->getKey();
    }
    public function getJWTCustomClaims() {
        return [];
    }

    public function schools() {
        return $this->belongsToMany(School::class, 'app_user_schools', 'app_user_id', 'school_id');
    }

    public function friends() {
        return $this->hasMany(AppUserFriend::class, 'app_user_id', 'id');
    }

    public function getTotalExerciseCountAttribute() {
        $latestDeadlift = $this->deadlifts()->first();
        $latestPowerClean = $this->powercleans()->first();
        $latestBenchPress = $this->benchpresses()->first();
        $latestSquat = $this->squats()->first();

        $totalDeadlift = $latestDeadlift ? $latestDeadlift->deadlift : 0;
        $totalPowerClean = $latestPowerClean ? $latestPowerClean->power_clean : 0;
        $totalBenchPress = $latestBenchPress ? $latestBenchPress->bench_press : 0;
        $totalSquat = $latestSquat ? $latestSquat->squat : 0;

        return $totalDeadlift + $totalPowerClean + $totalBenchPress + $totalSquat;
    }

    public function benchpresses() {
        return $this->hasMany(AppUserBenchpress::class, 'app_user_id', 'id')->latest('date');
    }

    public function deadlifts() {

        return $this->hasMany(AppUserDeadlift::class, 'app_user_id', 'id')->latest('date');
    }

    public function powercleans() {
        return $this->hasMany(AppUserPowerclean::class, 'app_user_id', 'id')->latest('date');
    }

    public function squats() {
        return $this->hasMany(AppUserSquat::class, 'app_user_id', 'id')->latest('date');
    }

//
    public function userSquats()
    {
        return $this->hasMany(AppUserSquat::class);
    }

    public function userDeadlifts()
    {
        return $this->hasMany(AppUserDeadlift::class);
    }

    public function userSchools()
    {
        return $this->hasMany(AppUserSchool::class);
    }

    public function userPowerCleans()
    {
        return $this->hasMany(AppUserPowerclean::class);
    }

    public function userBenchpress()
    {
        return $this->hasMany(AppUserBenchpress::class);
    }

    public function userFriends()
    {
        return $this->hasMany(AppUserFriend::class);
    }
    public function userWeights()
    {
        return $this->hasMany(AppUserWeight::class);
    }

    public function userHeights()
    {
        return $this->hasMany(AppUserHeight::class);
    }

    public function userContacts()
    {
        return $this->hasMany(AppUserContact::class);
    }
    
    public function userFeedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    public function deleteDeadlift() {
        $userDeadlifts = $this->userDeadlifts;
        if ($userDeadlifts) {
        foreach ($userDeadlifts as $userDeadlift) {
            $userDeadlift->forceDelete();
        }
    }
    }
    public function deleteFeedbacks() {
        $userFeedbacks = $this->userFeedbacks;
        if ($userFeedbacks) {
        foreach ($userFeedbacks as $userFeedback) {
            $userFeedback->forceDelete();
        }
    }
    }

    public function deleteSquats() {
        $userSquats = $this->userSquats;
        if ($userSquats) {
            foreach ($userSquats as $userSquat) {
                $userSquat->forceDelete();
            }
        }
    }

    public function deleteStatus() {
        $userStats = $this->userStats;
        if ($userStats) {
            foreach ($userStats as $userStat) {
                $userStat->forceDelete();
            }
        }
    }

    public function deleteSchools() {
        $userSchools = $this->userSchools;
        if ($userSchools) {
         foreach ($userSchools as $userSchool) {
            $userSchool->forceDelete();
         }
      }
    }
    public function deletePoweCleans() {
        $userPowerCleans = $this->userPowerCleans;
        if ($userPowerCleans) {
        foreach ($userPowerCleans as $userPowerClean) {
            $userPowerClean->forceDelete();
        }
    }
    }
    public function deleteBenchpress(){
        $userBenchpress = $this->userBenchpress;
        if ($userBenchpress) {
        foreach ($userBenchpress as $userBenchpres) {
            $userBenchpres->forceDelete();
        }
    }
    }
    public function deletefriends(){
        $userFriends = $this->userFriends;
        if ($userFriends) {
        foreach ($userFriends as $userFriend) {
            $userFriend->delete();
        }
    }
    }
    public function deleteWeights() {
        $userWeights = $this->userWeights;
        if ($userWeights) {
        foreach ($userWeights as $userWeight) {
            $userWeight->forceDelete();
        }
    }
    }
    public function deleteHeights(){
        $userHeights = $this->userHeights;
        if ($userHeights) {
        foreach ($userHeights as $userHeight) {
            $userHeight->forceDelete();
        }
    }
    }
    public function deleteContacts(){
        $userContacts = $this->userContacts;
        if ($userContacts) {
        foreach ($userContacts as $userContact) {
            $userContact->forceDelete();
        }
    }
    }
}
