<?php

namespace App\Filters;

use App\Filters\ApiFilter;
use Illuminate\Http\Request;

class UsrFilter extends ApiFilter {

    protected $safeParams = [
        'name' => ['eq'],
        'email' => ['eq'],
        'emailverifiedat' => ['eq'],
        'twofactorconfirmedat' => ['eq'],
        'currentteamid' => ['eq'],
        'profilephotopath' => ['eq'],
        'fbid' => ['eq'],
        'profilephotourl' => ['eq'],
    ];
    protected $columnMap = [
        'emailverifiedat' => 'email_verified_at',
        'twofactorconfirmedat' => 'two_factor_confirmed_at',
        'currentteamid' => 'current_team_id',
        'profilephotopath' => 'profile_photo_path',
        'fbid' => 'fb_id',
        'profilephotourl' => 'profile_photo_url',
    ];
    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
    ];


}
