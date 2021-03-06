<?php

 

namespace noelbradford\Referral\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cookie;
use DB;

trait UserReferral
{
    public function getReferralLink()
    {
        return url(config('referral.referral_url','/register?referral=')).$this->affiliate_id;
    }
        public function getRefererName()
    {
       $refer = DB::table('users')->where('affiliate_id',$this->referred_by)->first();
        return $refer->name;
    }
     public function getReferredUsersCount() 
     { 
        $referred_users =  DB::table('users')->where('referred_by',$this->affiliate_id)->count(); 
        return $referred_users;
     }

    public static function scopeReferralExists(Builder $query, $referral)
    {
        return $query->whereAffiliateId($referral)->exists();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if ($referredBy = Cookie::get('referral')) {
                $model->referred_by = $referredBy;
            }

            $model->affiliate_id = self::generateReferral();
        });
    }

    protected static function generateReferral()
    {
        $length = config('referral.referral_length', 8);

        do {
            $referral = str_random($length);
        } while (static::referralExists($referral));

        return $referral;
    }
}
