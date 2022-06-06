<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Config;
use DB;
use Exception;
use Hash;
use Illuminate\Support\Facades\Response;
use JWTAuth;
use Log;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Auth;
use Cache;
use JWTException;
use Mail;
use Image;
use App\Jobs\SMSJob;


class LoginController extends Controller
{
    /*===============================================| Admin |==================================================*/

    /**
     * @api {post} doLoginForAdmin doLoginForAdmin
     * @apiName doLoginForAdmin
     * @apiGroup Admin Login
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "email_id":"admin@gmail.com",
     * "password":"demo@123"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Login successfully.",
     * "cause": "",
     * "data": {
     * "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3QvcXVlc3Rpb25fYW5zd2VyL2FwaS9wdWJsaWMvYXBpL2RvTG9naW5Gb3JBZG1pbiIsImlhdCI6MTU1NDgzMDgwMSwiZXhwIjoxNTU2MDQwNDAxLCJuYmYiOjE1NTQ4MzA4MDEsImp0aSI6IjkyTzdGSkNINjFveUtNZkkifQ.MXxcV3tN2w-yLj-g70KVjF5xls_bIIEpYAAtZe37eM8",
     * "user_detail": {
     * "user_id": 1,
     * "first_name": "admin",
     * "last_name": "admin",
     * "email_id": "admin@gmail.com",
     * "gender": 0,
     * "coins": 0,
     * "is_active": 1,
     * "create_time": "2019-04-03 17:05:58",
     * "update_time": "2019-04-03 22:35:58"
     * }
     * }
     * }
     */
    public function doLoginForAdmin(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            if ($response = (new VerificationController())->validateRequiredParameter(array('email_id', 'password'), $request) != '')
                return $response;

            $email_id = $request->email_id;
            $password = $request->password;
            $credentials = ['email_id' => $email_id, 'password' => $password];
            if (!$token = JWTAuth::attempt($credentials)) {
                return Response::json(array('code' => 201, 'message' => 'Invalid email or password', 'cause' => '', 'data' => json_decode("{}")));
            }
            $is_admin = JWTAuth::toUser($token)->is_admin;
            $user_id = JWTAuth::toUser($token)->id;
            $is_active = JWTAuth::toUser($token)->is_active;

            if ($is_active == 0) {
                return Response::json(array('code' => 201, 'message' => 'Your Account Was Deactivated By Administrator..!!!', 'cause' => '', 'data' => json_decode("{}")));
            }

            if ($is_admin == 1) {
                DB::begintransaction();
                DB::insert('INSERT INTO user_session
                                    (user_id, token)
                                    VALUES (?,?)',
                    [$user_id, $token]);
                DB::commit();

                $result = $this->getUserInfoByUserId($user_id);
                $response = Response::json(array('code' => 200, 'message' => 'Login successfully.', 'cause' => '', 'data' => ['token' => $token, 'user_detail' => $result]));
            } else {
                $response = Response::json(array('code' => 201, 'message' => 'Invalid email or password.', 'cause' => '', 'data' => json_decode("{}")));
            }
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'login for admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("doLoginForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            DB::rollback();
        }
        return $response;
    }

    public function forgotPasswordToSendOTP(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('email_id'), $request)) != '') {
                return $response;
            }
            //Mandatory field
            $email_id = $request->email_id;
            $user_id = DB::select('SELECT id FROM user_master WHERE email_id = ? and id = ?', [$email_id, Config::get('constant.ROLE_ID_FOR_ADMIN')]);

            if (count($user_id) > 0) {


                DB::beginTransaction();
                $otp_token = (new Utils())->generateOTP();
                $otp_token_expire = date(Config::get('constant.DATE_FORMAT'), strtotime('+' . Config::get('constant.OTP_EXPIRATION_TIME') . ' minutes'));

                //log::info(strtotime($otp_token_expire));
                DB::insert('INSERT INTO otp_codes
                            (email_id,otp_token,otp_token_expire)
                            values (? ,? ,?)',
                    [$email_id, $otp_token, $otp_token_expire]);

                DB::commit();
                $response = Response::json(array('code' => 200, 'message' => 'Your verification code has been sent to your email.', 'cause' => '', 'data' => json_decode("{}")));

                $template = 'simple';
                $subject = 'OB ADS : Verification code.';
                $message_body = 'Your verification code is : ' . $otp_token . '.';
                $api_name = 'forgotPasswordToSendOTP';
                $api_description = 'before user login forgot his password then send mail here.';
                $this->dispatch(new EmailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));


            } else {
                $response = Response::json(array('code' => 201, 'message' => 'Invalid email address.', 'cause' => '', 'data' => json_decode("{}")));
            }


        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'forgot Password To Send OTP', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("forgotPasswordToSendOTP : ", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            DB::rollBack();
        }
        return $response;
    }

    public function resendOTPForAdmin(Request $request_body)
    {

        try {
            $request = json_decode($request_body->getContent());

            $email_id = isset($request->email_id) ? $request->email_id : '';


            if ($email_id != '') {
                if (($response = (new VerificationController())->validateRequiredParameter(array('email_id'), $request)) != '') {
                    return $response;
                }


                //Mandatory field
                $email_id = $request->email_id;


                $temp_user_result = DB::select('SELECT
                                                   id,email_id
                                                    FROM user_master
                                                    WHERE email_id = ? AND id=?', [$email_id, Config::get('constant.ROLE_ID_FOR_ADMIN')]);
                $email_id = $temp_user_result[0]->email_id;
                //log::info('sizeof(temp_user_result', ['sizeof(temp_user_result' => sizeof($temp_user_result)]);
                if (count($temp_user_result) > 0) {

                    //  $request_json = json_decode($temp_user_result[0]->request_json);


                    $result = DB::select('SELECT otp_token,
                                          otp_token_expire
                                          FROM otp_codes
                                          WHERE email_id = ? order by create_time desc limit 1', [$email_id]);
                    $otp_token_expire = $result[0]->otp_token_expire;
                    $create_time = date(Config::get('constant.DATE_FORMAT'));


                    //log::info(strtotime($create_time) . '  ' . strtotime($otp_token_expire));
                    if (strtotime(date('Y-m-d H:i:s')) > strtotime($otp_token_expire)) {

                        DB::beginTransaction();
                        $otp_token = (new Utils())->generateOTP();
                        $otp_token_expire = date(Config::get('constant.DATE_FORMAT'), strtotime('+' . Config::get('constant.OTP_EXPIRATION_TIME') . ' minutes'));

                        DB::update('UPDATE otp_codes
                                    SET otp_token = ?,
                                    otp_token_expire = ?
                                    WHERE email_id = ?', [$otp_token, $otp_token_expire, $email_id]);
                        $response = Response::json(array('code' => 200, 'message' => 'Your verification code has been sent to your phone number.', 'cause' => '', 'data' => json_decode("{}")));

                    } else {
                        $otp_token = $result[0]->otp_token;
                        $response = Response::json(array('code' => 200, 'message' => 'Your OTP sent to your email address please check your email.', 'cause' => '', 'data' => json_decode("{}")));

                    }

                    DB::commit();

                    $template = 'simple';
                    $subject = 'MayStr: Verification code';
                    $message_body = 'Your new verification code is ' . $otp_token . '.';
                    $api_name = 'resendOTPForAdmin';
                    $api_description = 'resend forgot password.';
                    $this->dispatch(new EmailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                } else {
                    $response = Response::json(array('code' => 201, 'message' => 'Invalid email address.', 'cause' => '', 'data' => json_decode("{}")));

                }

            }

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'resend OTP for admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("resendOTPForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollback();
        }
        return $response;
    }

    public function verifyOTPForAdmin(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('email_id', 'otp_token'), $request)) != '') {
                return $response;
            }

            //Mandatory field
            $email_id = $request->email_id;
            $otp_token = $request->otp_token;
            $create_time = date(Config::get('constant.DATE_FORMAT'));
            // $user = DB::table('user_master')->select('email_id')->where('email_id', $email_id)->first();
            //$email_id = $user->email_id;
            $user = DB::select('SELECT * FROM user_master WHERE email_id=? AND id=?', [$email_id, Config::get('constant.ROLE_ID_FOR_ADMIN')]);
            //if ($user[0]->id == 1)
            if (count($user) > 0) {
                if (($response = (new VerificationController())->verifyforgotOTP($email_id, $otp_token)) != '') {
                    return $response;
                }

                $result = DB::select('SELECT reset_token,reset_token_expire  FROM user_pwd_reset_token_master WHERE email_id = ? LIMIT 1', [$email_id]);
                DB::beginTransaction();

                if (count($result) > 0) {
                    $db_reset_token = $result[0]->reset_token;
                    $db_reset_token_expire = $result[0]->reset_token_expire;

                    if (strtotime($create_time) > strtotime($db_reset_token_expire)) {
                        $reset_token = bin2hex(openssl_random_pseudo_bytes(50)); //generate a random token
                        $reset_token_expire = date(Config::get('constant.DATE_FORMAT'), strtotime('+1 hour'));//the expiration date will be in one hour from the current moment

                        DB::update('UPDATE user_pwd_reset_token_master
                                SET reset_token = ?,
                                    reset_token_expire = ?
                                WHERE email_id = ?', [$reset_token, $reset_token_expire, $email_id]);

                    } else
                        $reset_token = $db_reset_token;
                } else {
                    $reset_token_expire = date(Config::get('constant.DATE_FORMAT'), strtotime('+1 hour'));//the expiration date will be in one hour from the current moment
                    $reset_token = bin2hex(openssl_random_pseudo_bytes(50)); //generate a random token

                    DB::insert('INSERT INTO user_pwd_reset_token_master
                                        (email_id, reset_token, reset_token_expire)
                                        VALUES (? ,?, ?)',
                        [$email_id, $reset_token, $reset_token_expire]);
                }
                DB::commit();
                $response = Response::json(array('code' => 200, 'message' => 'OTP verified successfully.', 'cause' => '', 'data' => ['token' => $reset_token]));
            } else {
                $response = Response::json(array('code' => 200, 'message' => 'Invalid email address .', 'cause' => '', 'data' => json_decode("{}")));

            }


        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'forgot password verify otp.', 'cause' => '', 'data' => json_decode("{}")));
            Log::error("verifyOTPForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();
        }
        return $response;
    }

    public function newPasswordForAdmin(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            $response = (new VerificationController())->validateRequiredParameter(array('email_id', 'token', 'new_password'), $request);
            if ($response != '') {
                return $response;
            }


            //Mandatory field
            $email_id = $request->email_id;
            $result = DB::select('SELECT id, email_id FROM user_master WHERE email_id = ? AND id=?', [$email_id, Config::get('constant.ROLE_ID_FOR_ADMIN')]);
            //return $result;
            if (count($result) > 0) {

                $user_id = $result[0]->id;
                $email_id = $result[0]->email_id;
                $token = $request->token;
                $new_password = Hash::make($request->new_password);
                $create_time = date(Config::get('constant.DATE_FORMAT'));

                $result = DB::select('SELECT
                                    email_id,
                                    reset_token,
                                    reset_token_expire
                                  FROM
                                    user_pwd_reset_token_master
                                  WHERE
                                    email_id = ? AND
                                    reset_token = ? AND
                                    reset_token_expire > ?', [$email_id, $token, $create_time]);
                DB::beginTransaction();
                if (count($result) > 0) {

                    DB::update('UPDATE user_master SET password = ? WHERE id = ?', [$new_password, $user_id]);
                    DB::delete('DELETE FROM user_pwd_reset_token_master WHERE email_id = ? AND reset_token = ?', [$email_id, $token]);
                    $response = Response::json(array('code' => 200, 'message' => 'Password updated successfully.', 'cause' => '', 'data' => json_decode("{}")));

                    //send email
                    $template = 'simple';
                    $subject = 'MayStr: Reset Password';
                    $message_body = 'Your password has been updated successfully.';
                    $api_name = 'newPasswordForAdmin';
                    $api_description = 'new password request.';
                    $this->dispatch(new EmailJob($user_id, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                } else {
                    $response = Response::json(array('code' => 201, 'message' => 'Please enter valid details.', 'cause' => '', 'data' => json_decode("{}")));
                }
            } else {
                $response = Response::json(array('code' => 201, 'message' => 'Invalid email address.', 'cause' => '', 'data' => json_decode("{}")));
            }

            DB::commit();

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'new password.', 'cause' => '', 'data' => json_decode("{}")));
            Log::error("newPasswordForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();
        }
        return $response;
    }

    /*===============================================| User |==================================================*/

    /**
     * @api {post} doLoginForUser doLoginForUser
     * @apiName doLoginForUser
     * @apiGroup User Login
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Body:
     * {
     * "email_id":"test1@grr.la",  //compulsory
     * "password":"demo@123",      //compulsory
     * "device_info": {
     * "device_carrier": "",
     * "device_country_code": "IN",
     * "device_reg_id": "asdvasghhasfhdgasfdffd",  //compulsory
     * "device_default_time_zone": "Asia/Calcutta",
     * "device_language": "en",
     * "device_latitude": "",
     * "device_library_version": "1",
     * "device_local_code": "NA",
     * "device_longitude": "",
     * "device_model_name": "Micromax AQ4501",
     * "device_os_version": "6.0.1",
     * "device_platform": "android",      //compulsory
     * "device_registration_date": "2016-05-06T15:58:11 +0530",
     * "device_resolution": "480x782",
     * "device_type": "phone",
     * "device_udid": "1a7b0b368a12d370",   //compulsory
     * "device_vendor_name": "Micromax",
     * "project_package_name": "com.test.projectsetup"
     * }
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Login successfully.",
     * "cause": "",
     * "data": {
     * "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEyLCJpc3MiOiJodHRwOi8vbG9jYWxob3N0L3F1ZXN0aW9uX2Fuc3dlci9hcGkvcHVibGljL2FwaS9kb0xvZ2luRm9yVXNlciIsImlhdCI6MTU1NTE4NjUzOCwiZXhwIjoxNTU2Mzk2MTM4LCJuYmYiOjE1NTUxODY1MzgsImp0aSI6IlJINjB0NENtRHdRSmQ4dU8ifQ.LqNA1oTURa8rZC5gxb9TtEtsrXYZC4xcWVn89vY59ok",
     * "user_detail": {
     * "user_id": 12,
     * "first_name": "john",
     * "last_name": "peter",
     * "email_id": "test1@grr.la",
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
    public function doLoginForUser(Request $request_body)
    {
        try {

            $request = json_decode($request_body->getContent());

            //Mandatory Field
            if (($response = (new VerificationController())->validateRequiredParameter(array(
                    'email_id',
                    'password',
                    'device_info'), $request)) != ''
            )
                return $response;

            //Log::info('doLoginForUser_Request',['request'=>$request]);
            $device_info = $request->device_info;
            if (($response = (new VerificationController())->validateRequiredParameter(array('device_udid'), $device_info)) != '') {
                return $response;
            }

            $email_id = $request->email_id;
            $password = $request->password;
            $device_udid = $device_info->device_udid;
            $current_date = date("Y-m-d H:i:s");

            $credential = ['email_id' => $email_id, 'password' => $password];
            if (!$token = JWTAuth::attempt($credential)) {
                return Response::json(array('code' => 201, 'message' => 'Invalid email or password.', 'cause' => '', 'data' => json_decode("{}")));
            }

            //JWTAuth::toUser($token)->id;
            $user_id = JWTAuth::toUser($token)->id;
            //log::info('user_id', ['user_id' => $user_id]);

            $active_user = DB::select('select * from user_master where id=? and is_active=1', [$user_id]);

            if (count($active_user) == 1) {

                $user_profile = $this->getUserInfoByUserId($user_id);

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

                // add new device
                $this->addNewDeviceToUser($user_id, $device_reg_id, $device_platform, $device_model_name, $device_vendor_name, $device_os_version, $device_udid, $device_resolution, $device_carrier, $device_country_code, $device_language, $device_local_code, $device_default_time_zone, $device_application_version, $device_type, $device_registration_date);

                // create user session
                $this->createNewSession($user_id, $token, $device_udid);

                $response = Response::json(array('code' => 200, 'message' => 'Login successfully.', 'cause' => '', 'data' => ['token' => $token, 'user_detail' => $user_profile]));

                //Log::info("Login token", ["token :" => $token, "time" => date('H:m:s')]);
            } else {
                $response = Response::json(array('code' => 201, 'message' => 'Your account has been deactivated.', 'cause' => '', 'data' => json_decode("{}")));
            }
            DB::commit();
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'login for user,' . Config::get('constant.EXCEPTION_ACTION_ERROR'), 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("doLoginForUser : ", ["error" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollback();
        }
        return $response;
    }

    /**
     * @api {post} forgotPasswordForSendOTP forgotPasswordForSendOTP
     * @apiName forgotPasswordForSendOTP
     * @apiGroup User Login
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {}
     * @apiSuccessExample Request-Body:
     * {
     * "email_id":"demo12@grr.la"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "The OTP will be sent via massage on verified phone number",
     * "cause": "",
     * "data": {
     * "phone_no": "8160891945"
     * }
     * }
     */
    public function forgotPasswordForSendOTP(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('email_id'), $request)) != '')
                return $response;

            //Mandatory field
            $email_id = $request->email_id;
            $user_detail = DB::select('SELECT um.id,ud.phone_no FROM user_master um LEFT JOIN user_detail ud ON  ud.user_id = um.id WHERE um.email_id = ?', [$email_id]);

            if (sizeof($user_detail) > 0) {

                $phone_no = $user_detail[0]->phone_no;
                DB::beginTransaction();
                $otp_token = (new Utils())->generateOTP();
                $otp_token_expire = date(Config::get('constant.DATE_FORMAT'), strtotime('+' . Config::get('constant.OTP_EXPIRATION_TIME') . ' minutes'));

                //log::info(strtotime($otp_token_expire));
                DB::insert('INSERT INTO otp_codes
                            (email_id,otp_token,otp_token_expire)
                            values (? ,? ,?)',
                    [$email_id, $otp_token, $otp_token_expire]);

                DB::commit();
                $response = Response::json(array('code' => 200, 'message' => 'The OTP will be sent via massage on verified phone number', 'cause' => '', 'data' => ['phone_no' => $phone_no]));

                $subject = 'Ask Question Poll: Your verification code is : ' . $otp_token;
                $message_body = $subject . ' Thank you.';
                $api_name = 'forgotPasswordforSendOTP';
                $api_description = 'before user login forgot his password then send massage here.';
                $this->dispatch(new SMSJob(1, $email_id, $phone_no, $message_body, $api_name, $api_description));

            } else {
                return Response::json(array('code' => 201, 'message' => 'Email does not exist.', 'cause' => '', 'data' => json_decode("{}")));
            }

        } catch (Swift_TransportException $e) {
            $response = Response::json(array('code' => 201, 'message' => 'Failed to send email.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("forgotPasswordForSendOTP : ", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            DB::rollBack();
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'reset password,' . Config::get('constant.EXCEPTION_ACTION_ERROR'), 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("forgotPasswordForSendOTP : ", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);

            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} resendOTPForUser resendOTPForUser
     * @apiName resendOTPForUser
     * @apiGroup User Login
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {}
     * @apiSuccessExample Request-Body:
     * {
     * "email_id":"demo12@grr.la" OR "user_registration_temp_id":56
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "The OTP will be sent via email on verified email ID.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function resendOTPForUser(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());

            $email_id = isset($request->email_id) ? $request->email_id : '';
            $user_registration_temp_id = isset($request->user_registration_temp_id) ? $request->user_registration_temp_id : '';

            if ($email_id != '') {
                if (($response = (new VerificationController())->validateRequiredParameter(array('email_id'), $request)) != '')
                    return $response;

                //Mandatory field
                $email_id = $request->email_id;

                $user_detail = DB::select('SELECT um.id,ud.phone_no FROM user_master um LEFT JOIN user_detail ud ON  ud.user_id = um.id WHERE um.email_id = ?', [$email_id]);
                // log::info('sizeof(temp_user_result', ['sizeof(temp_user_result' => sizeof($temp_user_result)]);
                if (sizeof($user_detail) > 0) {

                    $phone_no = $user_detail[0]->phone_no;
                    $result = DB::select('SELECT otp_token,
                                          otp_token_expire
                                          FROM otp_codes
                                          WHERE email_id = ? order by create_time desc limit 1', [$email_id]);
                    $otp_token_expire = $result[0]->otp_token_expire;
                    $create_time = date(Config::get('constant.DATE_FORMAT'));

                    DB::beginTransaction();
                    if (strtotime(date('Y-m-d H:i:s')) > strtotime($otp_token_expire)) {
                        $otp_token = (new Utils())->generateOTP();
                        $otp_token_expire = date(Config::get('constant.DATE_FORMAT'), strtotime('+' . Config::get('constant.OTP_EXPIRATION_TIME') . ' minutes'));

                        DB::update('UPDATE otp_codes
                                    SET otp_token = ?,
                                    otp_token_expire = ?
                                    WHERE email_id = ?', [$otp_token, $otp_token_expire, $email_id]);
                        $response = Response::json(array('code' => 200, 'message' => 'The OTP will be sent via massage on verified phone number', 'cause' => '', 'data' => ['phone_no' => $phone_no]));

                    } else {
                        $otp_token = $result[0]->otp_token;
                        $response = Response::json(array('code' => 200, 'message' => 'The OTP will be sent via massage on verified phone number', 'cause' => '', 'data' => ['phone_no' => $phone_no]));

                    }

                    DB::commit();

                    $subject = 'Ask Question Poll: Your verification code is : ' . $otp_token;
                    $message_body = $subject . ' Thank you.';
                    $api_name = 'resendForgotPasswordOtp';
                    $api_description = 'resend forgot password.';
                    $this->dispatch(new SMSJob(1, $email_id, $phone_no, $message_body, $api_name, $api_description));

                } else {
                    $response = Response::json(array('code' => 201, 'message' => 'Email does not exist.', 'cause' => '', 'data' => json_decode("{}")));
                }
            }

            if ($user_registration_temp_id != '') {
                if (($response = (new VerificationController())->validateRequiredParameter(array('user_registration_temp_id'), $request)) != '')
                    return $response;

                $user_registration_temp_id = $request->user_registration_temp_id;

                $active_user = DB::select('select request_json from user_registration_temp where id=?', [$user_registration_temp_id]);
                //log::info($active_user);

                if (sizeof($active_user) == 1) {
                    $request = json_decode($active_user[0]->request_json);
                    $is_active = 1;
                    $email_id = $request->email_id;
                    $phone_no = isset($request->phone_no) ? $request->phone_no : '';

                    $result = DB::select('SELECT otp_token,
                                          otp_token_expire
                                          FROM otp_codes
                                          WHERE email_id = ? order by create_time desc limit 1', [$email_id]);
                    $otp_token_expire = $result[0]->otp_token_expire;
                    $create_time = date(Config::get('constant.DATE_FORMAT'));

                    // log::info(strtotime($create_time) . '  ' . strtotime($otp_token_expire));
                    DB::beginTransaction();
                    if (strtotime(date('Y-m-d H:i:s')) > strtotime($otp_token_expire)) {
                        $otp_token = (new Utils())->generateOTP();
                        $otp_token_expire = date(Config::get('constant.DATE_FORMAT'), strtotime('+' . Config::get('constant.OTP_EXPIRATION_TIME') . ' minutes'));

                        DB::update('UPDATE otp_codes
                                    SET otp_token = ?,
                                    otp_token_expire = ?
                                    WHERE email_id = ?', [$otp_token, $otp_token_expire, $email_id]);
                        $response = Response::json(array('code' => 200, 'message' => 'Your verification code has been sent to your phone number', 'cause' => '', 'data' => ['phone_no' => $phone_no]));

                    } else {
                        $otp_token = $result[0]->otp_token;
                        $response = Response::json(array('code' => 200, 'message' => 'The OTP will be sent via massage on verified phone number', 'cause' => '', 'data' => ['phone_no' => $phone_no]));
                    }
                    DB::commit();

                    $template = 'simple';
                    $subject = 'Ask Question Poll: Resend Verification Code';
                    $message_body = $subject . ' Your new verification code is: ' . $otp_token;
                    $api_name = 'resendOtp';
                    $api_description = 'resend Otp.';
                    $this->dispatch(new SMSJob(1, $email_id, $phone_no, $message_body, $api_name, $api_description));

                } else {
                    $response = Response::json(array('code' => 201, 'message' => 'Invalid registration id.', 'cause' => '', 'data' => json_decode("{}")));
                }
            }

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'send OTP,' . Config::get('constant.EXCEPTION_ACTION_ERROR'), 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("resendOTPForUser :", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} verifyOTP verifyOTP
     * @apiName verifyOTP
     * @apiGroup User Login
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "email_id":"roy12@grr.la",
     * "otp_token":2780
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "The OTP verified successfully.",
     * "cause": "",
     * "data": {
     * "token": "9d797c648a1fa17aa3356c8f4ec6b2c93ad80b08b80ab1e861be5c9d610c24b4b6c55bd0e3583d133f3af964e03209d81c81"
     * }
     * }
     */
    public function verifyOTP(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('email_id', 'otp_token'), $request)) != '') {
                return $response;
            }

            //Mandatory field
            $email_id = $request->email_id;
            $otp_token = $request->otp_token;
            $create_time = date(Config::get('constant.DATE_FORMAT'));
            //$user = DB::table('user_master')->select('email_id')->where('email_id', $email_id)->first();

            $user = DB::select('SELECT id,email_id FROM user_master WHERE email_id = ? and id !=1', [$email_id]);

            if (count($user) > 0) {
                $email_id = $user[0]->email_id;
                if (($response = (new VerificationController())->verifyforgotOTP($email_id, $otp_token)) != '') {
                    return $response;
                }

                $result = DB::select('SELECT reset_token,reset_token_expire  FROM user_pwd_reset_token_master WHERE email_id = ? LIMIT 1', [$email_id]);
                if (count($result) > 0) {
                    $db_reset_token = $result[0]->reset_token;
                    $db_reset_token_expire = $result[0]->reset_token_expire;

                    if (strtotime($create_time) > strtotime($db_reset_token_expire)) {
                        $reset_token = bin2hex(openssl_random_pseudo_bytes(50)); //generate a random token
                        $reset_token_expire = date(Config::get('constant.DATE_FORMAT'), strtotime('+1 hour'));//the expiration date will be in one hour from the current moment
                        DB::beginTransaction();
                        DB::update('UPDATE user_pwd_reset_token_master
                                SET reset_token = ?,
                                    reset_token_expire = ?
                                WHERE email_id = ?', [$reset_token, $reset_token_expire, $email_id]);

                    } else
                        $reset_token = $db_reset_token;
                } else {
                    $reset_token_expire = date(Config::get('constant.DATE_FORMAT'), strtotime('+1 hour'));//the expiration date will be in one hour from the current moment
                    $reset_token = bin2hex(openssl_random_pseudo_bytes(50)); //generate a random token

                    DB::insert('INSERT INTO user_pwd_reset_token_master
                                        (email_id, reset_token, reset_token_expire)
                                        VALUES (? ,?, ?)',
                        [$email_id, $reset_token, $reset_token_expire]);
                }
                DB::commit();
                $response = Response::json(array('code' => 200, 'message' => 'The OTP verified successfully.', 'cause' => '', 'data' => ['token' => $reset_token]));

            } else {
                $response = Response::json(array('code' => 201, 'message' => 'Email does not exist.', 'cause' => '', 'data' => json_decode("{}")));
            }

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'verify OTP,' . Config::get('constant.EXCEPTION_ACTION_ERROR'), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("verifyOTP :", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} newPasswordForUser newPasswordForUser
     * @apiName newPasswordForUser
     * @apiGroup User Login
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "email_id":"roy12@grr.la",
     * "token":"9d797c648a1fa17aa3356c8f4ec6b2c93ad80b08b80ab1e861be5c9d610c24b4b6c55bd0e3583d133f3af964e03209d81c81",
     * "new_password":"demo@123"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Password updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function newPasswordForUser(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            $response = (new VerificationController())->validateRequiredParameter(array('email_id', 'token', 'new_password'), $request);
            if ($response != '') {
                return $response;
            }

            //Mandatory field
            $email_id = $request->email_id;
            $result = DB::select('SELECT id, email_id FROM user_master WHERE email_id = ? and id!=1', [$email_id]);
            //return $result;
            if (count($result) > 0) {

                $user_id = $result[0]->id;
                $email_id = $result[0]->email_id;
                $token = $request->token;
                $new_password = Hash::make($request->new_password);
                $create_time = date(Config::get('constant.DATE_FORMAT'));

                DB::beginTransaction();

                $result = DB::select('SELECT
                                    email_id,
                                    reset_token,
                                    reset_token_expire
                                  FROM
                                    user_pwd_reset_token_master
                                  WHERE
                                    email_id = ? AND
                                    reset_token = ? AND
                                    reset_token_expire > ?', [$email_id, $token, $create_time]);

                if (count($result) > 0) {

                    DB::update('UPDATE user_master SET password = ? WHERE id = ?', [$new_password, $user_id]);
                    DB::delete('DELETE FROM user_pwd_reset_token_master WHERE email_id = ? AND reset_token = ?', [$email_id, $token]);
                    $response = Response::json(array('code' => 200, 'message' => 'Password updated successfully.', 'cause' => '', 'data' => json_decode("{}")));

                } else {
                    $response = Response::json(array('code' => 201, 'message' => 'Please enter valid details.', 'cause' => '', 'data' => json_decode("{}")));
                }
            } else {
                return Response::json(array('code' => 201, 'message' => 'Email does not exist.', 'cause' => '', 'data' => json_decode("{}")));
            }

            DB::commit();

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'change password,' . Config::get('constant.EXCEPTION_ACTION_ERROR'), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("newPasswordForUser :", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            DB::rollBack();
        }
        return $response;
    }

    /*===============================================| Common for all |==================================================*/

    /**
     * @api {post} doLogout   doLogout
     * @apiName doLogout
     * @apiGroup Common For All
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "User have successfully logged out.",
     * "cause": "",
     * "data": {
     *
     * }
     * }
     */
    public function doLogout()
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $user_data = JWTAuth::parseToken()->authenticate();
            //$user_id = $user_data->id;

            //Log::info('logout');
            DB::beginTransaction();
            DB::delete('DELETE FROM user_session WHERE token = ?', [$token]);
            DB::commit();
            $response = Response::json(array('code' => 200, 'message' => 'User have successfully logged out.', 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'logout user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("doLogout : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post}changePassword changePassword
     * @apiName changePassword
     * @apiGroup Common For All
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "current_password":"demo@1234",
     * "new_password":"demo@123"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Password Changed successfully.",
     * "cause": "",
     * "data": {
     * "token": ""
     * }
     * }
     */
    public function changePassword(Request $request)
    {
        try {
            //get token & match the token
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('current_password', 'new_password'), $request)) != '') {
                return $response;
            }
            //Mandatory field
            $user_data = JWTAuth::parseToken()->authenticate();
            $user_id = $user_data->id;
            $email_id = $user_data->email_id;
            $current_password = $request->current_password;
            $new_password = Hash::make($request->new_password);

            $credential = ['email_id' => $email_id, 'password' => $current_password];
            if (!$old_token = JWTAuth::attempt($credential)) {

                return Response::json(array('code' => 201, 'message' => 'Current password is incorrect.', 'cause' => '', 'data' => json_decode("{}")));
            }

            DB::beginTransaction();
            DB::update('UPDATE user_master
                          SET
                          password = ?
                          WHERE email_id = ?', [$new_password, $email_id]);
            DB::commit();

            $credential = ['email_id' => $email_id, 'password' => $request->new_password];

            if ($new_token = JWTAuth::attempt($credential)) {
                //Log::info('change pass  :', ['old_token' => $token, 'new_token' => $new_token]);
                DB::beginTransaction();
                DB::update('UPDATE user_session
                          SET
                          token = ?
                          WHERE token = ?', [$new_token, $token]);
                DB::delete('DELETE FROM user_session WHERE user_id = ? AND token != ?', [$user_id, $new_token]);
                DB::commit();
                //Log::info('result of change password :', ['result' => $result]);
            }

            $response = Response::json(array('code' => 200, 'message' => 'Password Changed successfully.', 'cause' => '', 'data' => ['token' => $new_token]));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'change password.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            Log::error("changePassword : ", ["Exception" => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            DB::rollBack();
        }
        return $response;
    }

    /*========================================================function=================================================*/

    // create new user session
    public function createNewSession($user_id, $token, $device_udid)
    {
        try {

            $create_time = date('Y-m-d H:i:s');
            DB::beginTransaction();

            DB::insert('INSERT INTO user_session
                                    (user_id, token, device_udid,create_time)
                                    VALUES (?,?,?,?)',
                [$user_id, $token, $device_udid, $create_time]);
            DB::commit();
            $response = '';

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'create session,' . Config::get('constant.EXCEPTION_ACTION_ERROR'), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("createNewSession : ", ["Exception" => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            DB::rollBack();
        }
        return $response;
    }

    // get user info by user id
    public function getUserInfoByUserId($user_id)
    {
        try {
            $result = DB::select('SELECT
                                      um.id as user_id,
                                      if(ud.first_name!="",ud.first_name,"") AS first_name,
                                      if(ud.last_name!="",ud.last_name,"") AS last_name,
                                      COALESCE(um.email_id,"") AS email_id,
                                      COALESCE(cast(ud.gender AS UNSIGNED INTEGER),0) as gender,
                                      if(ud.coins!="",ud.coins,0) AS coins,
                                      if(ud.phone_no!="",ud.phone_no,"") AS phone_no,
                                      COALESCE(um.is_active,0)as is_active,
                                      um.create_time,
                                      um.update_time
                                    FROM
                                      user_master um, user_detail ud 
                                    WHERE
                                      um.id=ud.user_id and um.id=? and um.is_active=1', [$user_id]);

            $response = (count($result) != 0) ? $result[0] : json_decode("{}");

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fetch user detail.', 'cause' => '', 'data' => json_decode("{}")));
            Log::error("getUserInfoByUserId : ", ["Exception" => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
        }
        return $response;
    }

    public function getUserDeviceInfo($device_udid, $app_id)
    {
        try {

            $result = DB::select('SELECT * FROM device_master WHERE device_udid = ? AND app_id = ?', [$device_udid, $app_id]);

            $response = (sizeof($result) != 0) ? $result[0] : json_decode("{}");

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fetch user detail,' . Config::get('constant.EXCEPTION_ACTION_ERROR'), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("getUserDeviceInfo : ", ["Exception" => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
        }
        return $response;
    }

    public function addNewDeviceToUser($user_id, $device_reg_id, $device_platform, $device_model_name, $device_vendor_name, $device_os_version, $device_udid, $device_resolution, $device_carrier, $device_country_code, $device_language, $device_local_code, $device_default_time_zone, $device_application_version, $device_type, $device_registration_date)
    {
        try {
//            $created_date = date(Config::get('constant.DATE_FORMAT'));


            $device_result = DB::select('SELECT device_id FROM device_master WHERE user_id = ? AND device_udid = ?', [$user_id, $device_udid]);

            if (count($device_result) != 0) {
                DB::beginTransaction();
                DB::update('UPDATE device_master
                            SET is_active = 0
                                WHERE  user_id = ?',
                    [$user_id]);
                DB::commit();

                DB::update('UPDATE device_master SET device_reg_id = ?,is_active = 1 WHERE user_id = ? AND device_udid = ?',
                    [$device_reg_id, $user_id, $device_udid]);
                DB::commit();

                $response = $device_result[0]->device_id;
            } else {
                DB::beginTransaction();
                DB::update('UPDATE device_master
                            SET is_active = 0
                                WHERE  user_id = ?',
                    [$user_id]);
                DB::commit();
                $results = DB::table('device_master')->insertGetId(
                    array('user_id' => $user_id,
                        'device_reg_id' => $device_reg_id,
                        'device_platform' => $device_platform,
                        'device_model_name' => $device_model_name,
                        'device_vendor_name' => $device_vendor_name,
                        'device_os_version' => $device_os_version,
                        'device_udid' => $device_udid,
                        'device_resolution' => $device_resolution,
                        'device_carrier' => $device_carrier,
                        'device_country_code' => $device_country_code,
                        'device_language' => $device_language,
                        'device_local_code' => $device_local_code,
                        'device_default_time_zone' => $device_default_time_zone,
                        'device_application_version' => $device_application_version,
                        'device_type' => $device_type,
                        'device_registration_date' => $device_registration_date)
                );
                DB::commit();
                $response = $results;
                //Log::debug('device_reg_id', ['Exception' => $device_reg_id]);
            }


        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add user device,' . Config::get('constant.EXCEPTION_ACTION_ERROR'), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("addNewDeviceToUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            DB::rollBack();
        }
        return $response;
    }


}
