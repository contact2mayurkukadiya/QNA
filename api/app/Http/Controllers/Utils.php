<?php
/**
 * Created by PhpStorm.
 * User: ob-7
 * Date: 11/25/2017
 * Time: 10:38 AM
 */

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Response;
use DB;
use Log;
use Config;



class Utils extends Controller
{
    // get base url
    public function getBaseUrl()
    {
          return Config::get('constant.ACTIVATION_LINK_PATH');
//        $destinationPath ='http://192.168.0.110/EmployeeManagement_2';
//        return $destinationPath;
    }

    // generate otp
    public  function generateOTP()
    {
        $string = '0123456789';
        $string_shuffled = str_shuffle($string); //Randomly shuffles a string->The input string
        $otp = substr($string_shuffled, 1, 4);
        return $otp;
    }

    // get interval in minutes from time stamp
    public function getInterval($end_at,$start_at)
    {
        return round(abs(strtotime($end_at) - strtotime($start_at)) / 60,2);
    }
}
