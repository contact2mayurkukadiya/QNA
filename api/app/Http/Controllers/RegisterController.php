<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use DB;
use Response;
use Exception;
use QueryException;
use Config;
use Hash;
use JWTAuth;
use Auth;
use Cache;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use File;
use UploadedFile;
use Image;
use JWTException;
use Mail;
use App\Jobs\SMSJob;
use Hashids\Hashids;

class RegisterController extends Controller
{
    /**
     * @api {post} registerUserDeviceByDeviceUdid registerUserDeviceByDeviceUdid
     * @apiName registerUserDeviceByDeviceUdid
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "user_id":11,
     * "device_carrier": "",
     * "device_country_code": "IN",
     * "device_reg_id": "115a1a110",  //Mandatory
     * "device_default_time_zone": "Asia/Calcutta",
     * "device_language": "en",
     * "device_library_version": "1",
     * "device_application_version":"",
     * "device_local_code": "NA",
     * "device_model_name": "Micromax AQ4501",
     * "device_os_version": "6.0.1",
     * "device_platform": "android",  //Mandatory
     * "device_registration_date": "2016-05-06T15:58:11 +0530",
     * "device_resolution": "480x782",
     * "device_type": "phone",
     * "device_udid": "109111aa1121", //Mandatory
     * "device_vendor_name": "Micromax"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Device registered successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function registerUserDeviceByDeviceUdid(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('device_udid', 'device_reg_id', 'device_platform'), $request)) != '')
                return $response;

            $user_id = isset($request->user_id) ? $request->user_id : NULL;

            $device_udid = $request->device_udid;
            $device_reg_id = $request->device_reg_id;
            $device_platform = $request->device_platform;
            //  Log::debug('after_login_device_reg_id', ['Exception' => $device_reg_id]);
            //Optional field
            $device_model_name = isset($request->device_model_name) ? $request->device_model_name : '';
            $device_vendor_name = isset($request->device_vendor_name) ? $request->device_vendor_name : '';
            $device_os_version = isset($request->device_os_version) ? $request->device_os_version : '';
            $device_resolution = isset($request->device_resolution) ? $request->device_resolution : '';
            $device_carrier = isset($request->device_carrier) ? $request->device_carrier : '';
            $device_country_code = isset($request->device_country_code) ? $request->device_country_code : '';
            $device_language = isset($request->device_language) ? $request->device_language : '';
            $device_local_code = isset($request->device_local_code) ? $request->device_local_code : '';
            $device_default_time_zone = isset($request->device_default_time_zone) ? $request->device_default_time_zone : '';
            $device_application_version = isset($request->device_application_version) ? $request->device_application_version : '';
            $device_type = isset($request->device_type) ? $request->device_type : '';
            $device_registration_date = isset($request->device_registration_date) ? $request->device_registration_date : '';
            $device_library_version = isset($request->device_library_version) ? $request->device_library_version : '';
            DB::beginTransaction();

            $result = DB::select('SELECT 1 FROM device_master WHERE device_udid = ?', [$device_udid]);
            // Log::info('registerUserDeviceByDeviceUdid', ['total device having udid from request' => sizeof($result)]);
            if (sizeof($result) == 0) {
                // Log::info('registerUserDeviceByDeviceUdid', ['device_reg_id' => $device_reg_id]);
                $result = DB::insert('INSERT INTO device_master
                            (user_id,
                            device_reg_id,
                            device_platform,
                            device_model_name,
                            device_vendor_name,
                            device_os_version,
                            device_udid,
                            device_resolution,
                            device_carrier,
                            device_country_code,
                            device_language,
                            device_local_code,
                            device_default_time_zone,
                            device_library_version,
                            device_application_version,
                            device_type,
                            device_registration_date)
                            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ',
                    [$user_id, $device_reg_id,
                        $device_platform,
                        $device_model_name,
                        $device_vendor_name,
                        $device_os_version,
                        $device_udid,
                        $device_resolution,
                        $device_carrier,
                        $device_country_code,
                        $device_language,
                        $device_local_code,
                        $device_default_time_zone,
                        $device_library_version,
                        $device_application_version,
                        $device_type,
                        $device_registration_date
                    ]);
                // log::info(['result' => $result]);
                DB::commit();


            } else {
                $result = DB::update('UPDATE device_master
                            SET device_reg_id = ?
                                WHERE device_udid = ?',
                    [$device_reg_id, $device_udid]);
                //log::info(['update result' => $result]);


            }

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Device registered successfully.', 'cause' => '', 'data' => json_decode("{}")));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'register device.', 'cause' => '', 'data' => json_decode("{}")));
            Log::error('registerUserDeviceByDeviceUdid', ['Exception' => $e->getMessage()]);
            DB::rollBack();
        }
        return $response;
    }

    //==========================| signupUser |==========================//

    /**
     * @api {post} signupUser signupUser
     * @apiName signupUser
     * @apiGroup User Login
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "first_name":"test",
     * "last_name":"test",
     * "phone_no":7896541230,
     * "email_id":"test@grr.la",
     * "password":"123456",
     * "signup_type":1
     * }
     * * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "User registered successfully.",
     * "cause": "",
     * "data": {
     * "user_registration_temp_id": 6
     * }
     * }
     */
    public function signupUser(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('first_name', 'last_name', 'phone_no', 'email_id', 'password', 'signup_type'), $request)) != '')
                return $response;

            $email_id = $request->email_id;
            $phone_no = $request->phone_no;

            //--------email verification-----
            if (!filter_var($email_id, FILTER_VALIDATE_EMAIL)) {
                $response = Response::json(array('code' => 201, 'message' => 'E-mail is in the wrong format.', 'cause' => '', 'data' => ''));
                return $response;
            }

            $result = DB::select('select um.email_id from user_master as um,user_detail as ud WHERE um.is_active=1 and um.email_id=? and um.id=ud.user_id', [$email_id]);
            if (count($result) == 1) {
                return Response::json(array('code' => 201, 'message' => 'User already exists.', 'cause' => '', 'data' => json_decode("{}")));

            }

            $create_time = date("Y-m-d H:i:s");
            $eid = DB::select('select * from user_master WHERE email_id = ?', [$email_id]);

            if (count($eid) == 0) {
                $request_json = json_encode($request);
                DB::beginTransaction();

                $data = array('email_id' => $email_id,
                    'request_json' => $request_json,
                    'create_time' => $create_time,
                );

                $user_reg_temp_id = DB::table('user_registration_temp')->insertGetId($data);

                $otp_token = (new Utils())->generateOTP();
                $otp_token_expire = date(Config::get('constant.DATE_FORMAT'), strtotime('+' . Config::get('constant.OTP_EXPIRATION_TIME') . ' minutes'));

                DB::insert('INSERT INTO otp_codes
                            (email_id,otp_token,otp_token_expire)
                            values (? ,? ,?)',
                    [$email_id, $otp_token, $otp_token_expire]);

                DB::commit();
                $result = ['registration_id' => $user_reg_temp_id, 'email_id' => $email_id];
                $response = Response::json(array('code' => 200, 'message' => 'User registered successfully.', 'cause' => '', 'data' => ['user_registration_temp_id' => $user_reg_temp_id]));
                //  Log::info('result_info', ['response code' => $result]);

                $message_body = 'You are ready to start using the ' . Config::get('constant.PROJECT_NAME_FOR_MSG') . ' app! Please enter your verification code in the app. Your verification code is ' . $otp_token . '.';
                $api_name = 'signupUser';
                $api_description = 'Send mail for OTP verification.';

                $this->dispatch(new SMSJob($user_reg_temp_id, $email_id, $phone_no, $message_body, $api_name, $api_description));

            } else {
                Log::error('User alredy exist.', ['id' => $eid[0]->id]);
                $response = Response::json(array('code' => 201, 'message' => 'User alredy exist.', 'cause' => '', 'data' => json_decode("{}")));
            }

        } catch (Exception $e) {
            Log::error("signupUser : ", ["error" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'signup user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} verifyOTPForRegisterUser verifyOTPForRegisterUser
     * @apiName verifyOTPForRegisterUser
     * @apiGroup User Login
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "user_registration_temp_id":14, //compulsory
     * "otp_token": 8924,             //compulsory
     * "device_info":{
     * "device_carrier": "",
     * "device_country_code": "IN",
     * "device_reg_id": "115a1a110",
     * "device_default_time_zone": "Asia/Calcutta",
     * "device_language": "en",
     * "device_library_version": "1",
     * "device_application_version":"",
     * "device_local_code": "NA",
     * "device_model_name": "Micromax AQ4501",
     * "device_os_version": "6.0.1",
     * "device_platform": "android",
     * "device_registration_date": "2016-05-06T15:58:11 +0530",
     * "device_resolution": "480x782",
     * "device_type": "phone",
     * "device_udid": "109111aa1121",
     * "device_vendor_name": "Micromax"
     * }
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Login successfully.",
     * "cause": "",
     * "data": {
     * "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEyLCJpc3MiOiJodHRwOi8vbG9jYWxob3N0L3F1ZXN0aW9uX2Fuc3dlci9hcGkvcHVibGljL2FwaS92ZXJpZnlPVFBGb3JSZWdpc3RlclVzZXIiLCJpYXQiOjE1NTUwODc4MTcsImV4cCI6MTU1NjI5NzQxNywibmJmIjoxNTU1MDg3ODE3LCJqdGkiOiJsTndxODdvcnRpOWY0amRGIn0.t_12bY72-z3Gx2McPBsYIhjIUm1fqe8jcWQVZgv2qFA",
     * "user_detail": {
     * "user_id": 12,
     * "first_name": "John",
     * "last_name": "Peter",
     * "email_id": "john@grr.la",
     * "gender": 1,
     * "coins": 0,
     * "phone_no": "7896541230",
     * "is_active": 1,
     * "create_time": "2019-04-12 16:50:15",
     * "update_time": "2019-04-12 22:20:16"
     * }
     * }
     * }
     */
    public function verifyOTPForRegisterUser(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());

            $response = (new VerificationController())->validateRequiredParameter(array('otp_token', 'user_registration_temp_id', 'device_info'), $request);
            if ($response != '') {
                return $response;
            }

            $device_info = $request->device_info;
            if (($response = (new VerificationController())->validateRequiredParameter(array('device_udid'), $device_info)) != '') {
                return $response;
            }

            //Mandatory field
            $otp_token = $request->otp_token;

            $user_registration_temp_id = $request->user_registration_temp_id;
            $create_time = date("Y-m-d H:i:s");

            $active_user = DB::select('select request_json from user_registration_temp where id=?', [$user_registration_temp_id]);
            // log::info('activeUser', ['activeUser' => $active_user]);

            if (count($active_user) == 1) {
                $request = json_decode($active_user[0]->request_json);
                $is_active = 1;
                $email_id = $request->email_id;
                $signup_type = $request->signup_type;

                if (($response = (new VerificationController())->verifyOTP($email_id, $otp_token)) != '')
                    return $response;

                $response = (new VerificationController())->checkIfUserExist($email_id);
                if ($response == 1) {
                    return Response::json(array('code' => 201, 'message' => 'Email already exist.', 'cause' => '', 'data' => json_decode("{}")));

                } else {

                    $first_name = $request->first_name;
                    $last_name = $request->last_name;
                    $gender = isset($request->gender) ? $request->gender : 1;
                    $coins = isset($request->coins) ? $request->coins : 0;
                    $contact_no = isset($request->phone_no) ? $request->phone_no : '';

                    $hashids = new Hashids($email_id);
                    $hashid = $hashids->encode($contact_no);
                    Log::info(['hashid'=>$hashid]);


                    DB::begintransaction();
                    $login_data = array(
                        'email_id' => $email_id,
                        'password' => Hash::make($request->password),
                        'hash_id' => $hashid,
                        'is_active' => $is_active,
                        'signup_type' => $signup_type,
                        'create_time' => $create_time
                    );
                    //log::info('Login Data', ['Login Data=' => $login_data]);

                    $user_id = DB::table('user_master')->insertGetId($login_data);
                    $user_role_name = 'user';

                    DB::insert('insert into user_detail(user_id,first_name,last_name,email_id,gender,phone_no,coins,create_time) values(?,?,?,?,?,?,?,?)',
                        [$user_id, $first_name, $last_name, $email_id, $gender, $contact_no, $coins, $create_time]);
                    // log::info('user_details_id = ' . $user_details_insert_query);

                    $user_role_data = array(
                        'role_id' => Config::get('constant.ROLE_ID_FOR_USER'),
                        'user_id' => $user_id,
                    );
                    DB::table('role_user')->insert($user_role_data);

                    $device_udid = $device_info->device_udid;
                    $device_reg_id = isset($device_info->device_reg_id) ? $device_info->device_reg_id : '';
                    $device_platform = isset($device_info->device_platform) ? $device_info->device_platform : '';
                    $device_model_name = isset($device_info->device_model_name) ? $device_info->device_model_name : '';
                    $device_vendor_name = isset($device_info->device_vendor_name) ? $device_info->device_vendor_name : '';
                    $device_os_version = isset($device_info->device_os_version) ? $device_info->device_os_version : '';
                    $device_resolution = isset($device_info->device_resolution) ? $device_info->device_resolution : '';
                    $device_carrier = isset($device_info->device_carrier) ? $device_info->device_carrier : '';
                    $device_country_code = isset($device_info->device_country_code) ? $device_info->device_country_code : '';
                    $device_language = isset($device_info->device_language) ? $device_info->device_language : '';
                    $device_local_code = isset($device_info->device_local_code) ? $device_info->device_local_code : '';
                    $device_default_time_zone = isset($device_info->device_default_time_zone) ? $device_info->device_default_time_zone : '';
                    $device_library_version = isset($request->device_library_version) ? $request->device_library_version : '';
                    $device_application_version = isset($device_info->device_application_version) ? $device_info->device_application_version : '';
                    $device_type = isset($device_info->device_type) ? $device_info->device_type : '';
                    $device_registration_date = isset($device_info->device_registration_date) ? $device_info->device_registration_date : '';


                    $credential = ['email_id' => $email_id, 'password' => $request->password];
                    if (!$token = JWTAuth::attempt($credential))
                        return Response::json(array('code' => 201, 'message' => 'Invalid Email Id or Password.', 'cause' => '', 'data' => json_decode("{}")));

                    $user_id = JWTAuth::toUser($token)->id;
                    (new LoginController())->addNewDeviceToUser($user_id, $device_reg_id, $device_platform, $device_model_name, $device_vendor_name, $device_os_version, $device_udid, $device_resolution, $device_carrier, $device_country_code, $device_language, $device_local_code, $device_default_time_zone, $device_library_version, $device_application_version, $device_type, $device_registration_date);
                    (new LoginController())->createNewSession($user_id, $token, $device_udid);

                    $user_profile = (new LoginController())->getUserInfoByUserId($user_id);
                }
                DB::commit();

                $response = Response::json(array('code' => 200, 'message' => 'Login successfully.', 'cause' => '', 'data' => ['token' => $token, 'user_detail' => $user_profile]));
            } else {
                $response = Response::json(array('code' => 201, 'message' => 'Invalid registration id.', 'cause' => '', 'data' => json_decode("{}")));

            }

        } catch (Exception $e) {
            Log::error("verifyOTPForRegisterUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'verify OTP for register user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }

        return $response;
    }

    //==========================| Loging with social Media |==========================//

    /**
     * @api {post} doLoginForSocialUser doLoginForSocialUser
     * @apiName doLoginForSocialUser
     * @apiGroup User Login
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "signup_type":2,
     * "social_uid":123456,
     * "first_name":"test",
     * "last_name":"demo",
     * "email_id":"demo@grr.la",
     * "device_info":{
     * "device_carrier": "",
     * "device_country_code": "IN",
     * "device_reg_id": "115a1a110",
     * "device_default_time_zone": "Asia/Calcutta",
     * "device_language": "en",
     * "device_library_version": "1",
     * "device_application_version":"",
     * "device_local_code": "NA",
     * "device_model_name": "Micromax AQ4501",
     * "device_os_version": "6.0.1",
     * "device_platform": "android",
     * "device_registration_date": "2016-05-06T15:58:11 +0530",
     * "device_resolution": "480x782",
     * "device_type": "phone",
     * "device_udid": "109111aa1121",
     * "device_vendor_name": "Micromax"
     * }
     * }
     * * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Login successfully.",
     * "cause": "",
     * "data": {
     * "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjI2LCJpc3MiOiJodHRwOi8vbG9jYWxob3N0L3F1ZXN0aW9uX2Fuc3dlci9hcGkvcHVibGljL2FwaS9kb0xvZ2luRm9yU29jaWFsVXNlciIsImlhdCI6MTU1NTA5NTkxMCwiZXhwIjoxNTU2MzA1NTEwLCJuYmYiOjE1NTUwOTU5MTAsImp0aSI6ImtEYWNyYktpdndiTERTTW0ifQ.BAzXOq0-fGhYhEOCNG6K48_y4to_wiSvSwh87Gwq74w",
     * "user_detail": {
     * "user_id": 26,
     * "first_name": "test",
     * "last_name": "demo",
     * "email_id": "demo@grr.la",
     * "gender": 0,
     * "coins": 0,
     * "phone_no": "",
     * "is_active": 1,
     * "create_time": "2019-04-12 18:56:52",
     * "update_time": "2019-04-13 00:26:52"
     * }
     * }
     * }
     */
    public function doLoginForSocialUser(Request $request_body)
    {
        try {
            $request = json_decode($request_body->input('request_data'));
            //Log::info('doLoginForSocialUser_Request',['Request'=> $request]);
            if (!$request_body->has('request_data')) {
                return Response::json(array('code' => 201, 'message' => 'Required field request_data is missing or empty', 'cause' => '', 'data' => json_decode("{}")));

            }
            $response = (new VerificationController())->validateRequiredParameter(array('signup_type'), $request);
            if ($response != '') {
                return $response;
            }

            $device_info = $request->device_info;
            if (($response = (new VerificationController())->validateRequiredParameter(array('device_udid'), $device_info)) != '') {
                return $response;
            }
            $response = (new VerificationController())->validateRequiredParameter(array('device_info'), $request);
            if ($response != '') {
                return $response;
            }

            $social_uid = $request->social_uid;
            $signup_type = $request->signup_type;
            $first_name = isset($request->first_name) ? $request->first_name : NULL;
            $last_name = isset($request->last_name) ? $request->last_name : NULL;
            $email_id = isset($request->email_id) ? $request->email_id : NULL;
            $gender = isset($request->gender) ? $request->gender : NULL;
            $phone_no = isset($request->phone_no) ? $request->phone_no : NULL;
            $create_time = date('Y-m-d H:i:s');

            //log::info('doLoginForSocialUser_gender', ['gender=' => $gender]);

            if ($signup_type == 2 || $signup_type == 3 || $signup_type == 4) {
                $response = (new VerificationController())->validateRequiredParameter(array('social_uid'), $request);
                if ($response != '') {
                    return $response;
                }

                $exist_id = DB::select('select id from user_master where social_uid = ? and signup_type = ?', [$social_uid, $signup_type]);
                //return $exist_id[0]->id;
                // Log::info('Exist record', ['exist_record' => $exist_id]);

                if (count($exist_id) != 0) {

                    $id = $exist_id[0]->id;
                    if (($response = (new VerificationController())->checkIfUserIsActive($id)) != '') {
                        return $response;
                    }
                    // Log::info('Exist record user_id', ['user_id' => $id]);

                    $password = '$' . $social_uid . '#';
                    //Log::info('Exist record social credential', ['social_uid' => $social_uid, 'password' => $password]);

                    $credential = ['social_uid' => $social_uid, 'password' => $password];
                    if (!$token = JWTAuth::attempt($credential)) {
                        return Response::json(array('code' => '201', 'message' => 'Invalid credential', 'cause' => 'Email', 'data' => json_decode("{}")));
                    }
                    $user_id = JWTAuth::toUser($token)->id;
                    $device_udid = $device_info->device_udid;
                    $device_reg_id = isset($device_info->device_reg_id) ? $device_info->device_reg_id : '';
                    $device_platform = isset($device_info->device_platform) ? $device_info->device_platform : '';
                    $device_model_name = isset($device_info->device_model_name) ? $device_info->device_model_name : '';
                    $device_vendor_name = isset($device_info->device_vendor_name) ? $device_info->device_vendor_name : '';
                    $device_os_version = isset($device_info->device_os_version) ? $device_info->device_os_version : '';
                    $device_resolution = isset($device_info->device_resolution) ? $device_info->device_resolution : '';
                    $device_carrier = isset($device_info->device_carrier) ? $device_info->device_carrier : '';
                    $device_country_code = isset($device_info->device_country_code) ? $device_info->device_country_code : '';
                    $device_language = isset($device_info->device_language) ? $device_info->device_language : '';
                    $device_local_code = isset($device_info->device_local_code) ? $device_info->device_local_code : '';
                    $device_default_time_zone = isset($device_info->device_default_time_zone) ? $device_info->device_default_time_zone : '';
                    $device_library_version = isset($request->device_library_version) ? $request->device_library_version : '';
                    $device_application_version = isset($device_info->device_application_version) ? $device_info->device_application_version : '';
                    $device_type = isset($device_info->device_type) ? $device_info->device_type : '';
                    $device_registration_date = isset($device_info->device_registration_date) ? $device_info->device_registration_date : '';

                    (new LoginController())->addNewDeviceToUser($user_id, $device_reg_id, $device_platform, $device_model_name, $device_vendor_name, $device_os_version, $device_udid, $device_resolution, $device_carrier, $device_country_code, $device_language, $device_local_code, $device_default_time_zone, $device_application_version, $device_type, $device_registration_date);
                    (new LoginController())->createNewSession($user_id, $token, $device_udid);

                    DB::commit();

                    $user_profile = (new LoginController())->getUserInfoByUserId($user_id);

                    $response = Response::json(array('code' => 200, 'message' => 'Login successfully.', 'cause' => '', 'data' => ['token' => $token, 'user_detail' => $user_profile]));
                    //$response = Response::json(array('code' => 200, 'message' => 'Thank you for signing up!', 'cause' => '', 'data' => ['token' => $token, 'user_detail' => $user_profile]));

                } else {

                    $response = (new VerificationController())->validateRequiredParameter(array('first_name', 'last_name', 'email_id'), $request);

                    /* if ($response != '') {

                         $result  =  json_decode(json_encode($response),true);
                         $msg = $result['original']['message'];

                         $response = Response::json(array('code' => 420, 'message' => $msg, 'cause' => '', 'data' => json_decode("{}")));

                         return $response;
                     }*/

                    $device_udid = $device_info->device_udid;
                    $device_reg_id = isset($device_info->device_reg_id) ? $device_info->device_reg_id : '';
                    $device_platform = isset($device_info->device_platform) ? $device_info->device_platform : '';
                    $device_model_name = isset($device_info->device_model_name) ? $device_info->device_model_name : '';
                    $device_vendor_name = isset($device_info->device_vendor_name) ? $device_info->device_vendor_name : '';
                    $device_os_version = isset($device_info->device_os_version) ? $device_info->device_os_version : '';
                    $device_resolution = isset($device_info->device_resolution) ? $device_info->device_resolution : '';
                    $device_carrier = isset($device_info->device_carrier) ? $device_info->device_carrier : '';
                    $device_country_code = isset($device_info->device_country_code) ? $device_info->device_country_code : '';
                    $device_language = isset($device_info->device_language) ? $device_info->device_language : '';
                    $device_local_code = isset($device_info->device_local_code) ? $device_info->device_local_code : '';
                    $device_default_time_zone = isset($device_info->device_default_time_zone) ? $device_info->device_default_time_zone : '';
                    $device_library_version = isset($request->device_library_version) ? $request->device_library_version : '';
                    $device_application_version = isset($device_info->device_application_version) ? $device_info->device_application_version : '';
                    $device_type = isset($device_info->device_type) ? $device_info->device_type : '';
                    $device_registration_date = isset($device_info->device_registration_date) ? $device_info->device_registration_date : '';

                    // Check if social_uid is exist or not for social user
                    $response = (new VerificationController())->checkIfEmailExist($social_uid);
                    if ($response == 1) {
                        return $response = Response::json(array('code' => 201, 'message' => 'User already exists.', 'cause' => '', 'data' => json_decode("{}")));
                    }
                    // Check if user is exist
                    if ($email_id != NULL or $email_id != '') {
                        $response = (new VerificationController())->checkIfUserExist($email_id);
                        if ($response == 1) {
                            return Response::json(array('code' => 201, 'message' => 'Email already exists.', 'cause' => '', 'data' => json_decode("{}")));
                        }
                    }

                    $password = '$' . $social_uid . '#';
                    $db_password = Hash::make($password);

                    DB::beginTransaction();
                    $user_master_data = array(
                        'email_id' => $email_id,
                        'password' => $db_password,
                        'social_uid' => $social_uid,
                        'signup_type' => $signup_type,
                        'is_active' => 1,
                        'create_time' => $create_time
                    );

                    // Log::info('Social sign up user_id', ['user_master_data' => $user_master_data]);
                    $id = DB::table('user_master')->insertGetId($user_master_data);

                    // Log::info('Social sign up user_id', ['user_id' => $id]);
                    $user_details_data = array(
                        'user_id' => $id,
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'email_id' => $email_id,
                        'gender' => $gender,
                        'create_time' => $create_time
                    );

                    DB::table('user_detail')->insert($user_details_data);

                    $user_role_data = array(
                        'user_id' => $id,
                        'role_id' => Config::get('constant.ROLE_ID_FOR_USER')
                    );
                    DB::table('role_user')->insert($user_role_data);

                    // Log::info('Social sign up credential', ['social_uid' => $social_uid, 'password' => $password]);
                    $credential = ['social_uid' => $social_uid, 'password' => $password];
                    if (!$token = JWTAuth::attempt($credential)) {
                        // Log::info('Social sign up token', ['token' => $token]);
                        return Response::json(array('code' => 201, 'message' => 'Invalid credential', 'cause' => 'Email', 'data' => json_decode("{}")));
                    }
                    $user_id = JWTAuth::toUser($token)->id;
                    (new LoginController())->addNewDeviceToUser($user_id, $device_reg_id, $device_platform, $device_model_name, $device_vendor_name, $device_os_version, $device_udid, $device_resolution, $device_carrier, $device_country_code, $device_language, $device_local_code, $device_default_time_zone, $device_application_version, $device_type, $device_registration_date);
                    (new LoginController())->createNewSession($user_id, $token, $device_udid);

                    DB::commit();

                    $user_profile = (new LoginController())->getUserInfoByUserId($user_id);

                    //$response = Response::json(array('code' => 200, 'message' => 'Login successfully.', 'cause' => '', 'data' => ['token' => $token, 'user_detail' => $user_profile]));
                    $response = Response::json(array('code' => 200, 'message' => 'Thank you for signing up!', 'cause' => '', 'data' => ['token' => $token, 'user_detail' => $user_profile]));
                }
            }

        } catch (Exception $e) {
            //$response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'login for social user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'register,' . Config::get('constant.EXCEPTION_ACTION_ERROR'), 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("doLoginForSocialUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollback();
        }
        return $response;

    }

}

