<?php


namespace App\Http\Controllers;

use App\Http\Requests;
use Response;
use DB;
use Exception;
use Log;
use Config;

class VerificationController extends Controller
{
    // validate required and empty field
    public function validateRequiredParameter($required_fields, $request_params)
    {
        $error = false;
        $error_fields = '';

        foreach ($required_fields as $key => $value) {
            if (isset($request_params->$value)) {
                if (!is_object($request_params->$value)) {
                    if (strlen($request_params->$value) == 0) {
                        $error = true;
                        $error_fields .= ' ' . $value . ',';
                    }
                }
            } else {
                $error = true;
                $error_fields .= ' ' . $value . ',';
            }
        }

        if ($error) {
            // Required field(s) are missing or empty
            $error_fields = substr($error_fields, 0, -1);
            $message = 'Required field(s)' . $error_fields . ' is missing or empty.';
            $response = Response::json(array('code' => 201, 'message' => $message, 'cause' => '', 'data' => json_decode("{}")));
        } else
            $response = '';

        return $response;
    }

    public function validateRequiredParameterToArray($required_fields, $request_params)
    {
        $error = false;
        $error_fields = '';

        foreach ($required_fields as $key => $value) {
            if (isset($request_params[$value])) {
                if (!is_object($request_params[$value])) {
                    if (strlen($request_params[$value]) == 0) {
                        $error = true;
                        $error_fields .= ' ' . $value . ',';
                    }
                }
            } else {
                $error = true;
                $error_fields .= ' ' . $value . ',';
            }
        }

        if ($error) {
            // Required field(s) are missing or empty
            $error_fields = substr($error_fields, 0, -1);
            $message = 'Required field(s)' . $error_fields . ' is missing or empty.';
            $response = Response::json(array('code' => 201, 'message' => $message, 'cause' => '', 'data' => json_decode("{}")));
        } else
            $response = '';

        return $response;
    }

    public function validateRequiredArrayParameter($required_fields, $request_params)
    {
        $error = false;
        $error_fields = '';

        foreach ($required_fields as $key => $value) {
            if (isset($request_params->$value)) {
                if (!is_array($request_params->$value)) {
                    $error = true;
                    $error_fields .= ' ' . $value . ',';
                } else {
                    if (count($request_params->$value) == 0) {
                        $error = true;
                        $error_fields .= ' ' . $value . ',';
                    }
                }
            } else {
                $error = true;
                $error_fields .= ' ' . $value . ',';
            }
        }

        if ($error) {
            // Required field(s) are missing or empty
            $error_fields = substr($error_fields, 0, -1);
            $message = 'Required field(s)' . $error_fields . ' is missing or empty.';
            $response = Response::json(array('code' => 201, 'message' => $message, 'cause' => '', 'data' => json_decode("{}")));
        } else
            $response = '';

        return $response;
    }

    // validate required field
    public function validateRequiredParam($required_fields, $request_params)
    {
        $error = false;
        $error_fields = '';

        foreach ($required_fields as $key => $value) {
            if (!(isset($request_params->$value))) {
                $error = true;
                $error_fields .= ' ' . $value . ',';
            }
        }

        if ($error) {
            // Required field(s) are missing or empty
            $error_fields = substr($error_fields, 0, -1);
            $message = 'Required field(s)' . $error_fields . ' is missing.';
            $response = Response::json(array('code' => 201, 'message' => $message, 'cause' => '', 'data' => json_decode("{}")));
        } else
            $response = '';
        return $response;
    }

    // verify otp
    public function verifyOTP($email_id, $otp_token)
    {
        try {
            $result = DB::select('SELECT otp_token_expire
                                  FROM otp_codes
                                  WHERE email_id = ? AND
                                        otp_token = ?',[$email_id,$otp_token]);
            if (count($result) == 0) {
                $response = Response::json(array('code' => 201, 'message' => 'OTP is invalid.', 'cause' => '', 'data' => json_decode("{}")));
            } elseif (strtotime(date(Config::get('constant.DATE_FORMAT'))) > strtotime($result[0]->otp_token_expire)) {
                $response = Response::json(array('code' => 201, 'message' => 'OTP token expired.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("verifyOTP  : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    // check if user is active
    public function checkIfUserIsActive($user_id)
    {
        try {

            $result = DB::select('SELECT
                                        um.is_active
                                        FROM user_master um
                                        WHERE um.id = ?', [$user_id]);
            $response = ($result[0]->is_active == '1') ? '' : Response::json(array('code' => '201', 'message' => 'You are inactive user. Please contact administrator.', 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
        }
        return $response;
    }

    // check if  user is active
    public function checkIfUserExist($user_id)
    {
        try {
            $result = DB::select('SELECT 1 FROM user_master WHERE email_id = ?', [$user_id]);
            $response = (sizeof($result) != 0) ? 1 : 0;

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("checkIfUserExist  : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    // verify user
    public function verifyUser($user_id, $role_name)
    {
        try {
            $result = DB::select('SELECT r.name
                                  FROM role_user ru, roles r, user_master um
                                  WHERE r.id = ru.role_id AND
                                        um.id = ru.user_id AND
                                        um.email_id = ?', [$user_id]);
            $response = (sizeof($result) > 0 && $result[0]->name == $role_name) ? '' : Response::json(array('code' => 201, 'message' => 'Unauthorized user.', 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("verifyUser  : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    // get user role
    public function getUserRole($user_id)
    {
        try {
            $result = DB::select('SELECT
                                        r.name
                                        FROM role_user ru, user_master um, roles r
                                        WHERE
                                          um.id = ru.user_id AND
                                          ru.role_id = r.id AND
                                          um.user_id = ?', [$user_id]);

            $response = (count($result) > 0) ? $result[0]->name : '';

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
        }
        return $response;
    }

    // verify setup
    public function verifySetup($column, $user_id)
    {
        try {
            $result = DB::select('SELECT
                                    ' . $column . '
                                    FROM user_master um
                                    WHERE
                                      user_id = ?', [$user_id]);

            $response = $result[0]->$column;

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
        }
        return $response;
    }

    public function verifyforgotOTP($email_id, $otp_token)
    {
        try {
            $result = DB::select('SELECT otp_token_expire
                                  FROM otp_codes
                                  WHERE email_id = ? AND
                                        otp_token = ? ORDER BY otp_token_expire DESC LIMIT 1', [$email_id, $otp_token]);
            if (count($result) == 0) {
                $response = Response::json(array('code' => 201, 'message' => 'OTP is invalid.', 'cause' => '', 'data' => json_decode("{}")));
            } elseif (strtotime(date(Config::get('constant.DATE_FORMAT'))) > strtotime($result[0]->otp_token_expire)) {
                $response = Response::json(array('code' => 201, 'message' => 'OTP token expired.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("verifyforgotOTP : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }
        return $response;
    }

    // check if email is exist
    public function checkIfEmailExist($email_id)
    {
        try {
            $result = DB::select('SELECT 1 FROM user_master WHERE email_id = ?', [$email_id]);
//            $response = (sizeof($result) != 0) ? 1 : 0;

            $response = (count($result) == 0) ? '' : Response::json(array('code' => 201, 'message' => 'Email already existed.', 'cause' => '', 'data' => json_decode("{}")));


        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("checkIfEmailExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }
        return $response;
    }

    // verifyOTPForForgotPassword
    public function verifyOTPForForgotPassword($phone_number, $otp_token)
    {
        try {
            $result = DB::select('SELECT otp_token_expire
                                  FROM otp_codes
                                  WHERE phone_number = ? AND
                                        otp_token = ?', [$phone_number, $otp_token]);
            if (count($result) == 0) {
                $response = Response::json(array('code' => 201, 'message' => 'OTP is invalid.', 'cause' => '', 'data' => json_decode("{}")));
            } elseif (strtotime(date(Config::get('constant.DATE_FORMAT'))) > strtotime($result[0]->otp_token_expire)) {
                $response = Response::json(array('code' => 201, 'message' => 'OTP token expired.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("verifyOTPForForgotPassword : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }
        return $response;
    }

    //validateItemCount
    public function validateItemCount($item_count)
    {
        try {

            if ($item_count < 3 or $item_count > 200) {
                $response = Response::json(array('code' => 201, 'message' => 'Item count must be >= 3 and <= 200.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            Log::error("validateItemCount : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
        }

        return $response;
    }

    public function checkIsExistInDB($id,$table_name, $field_Name, $value_name)
    {
        try {
            if($id){
                $result = DB::select('SELECT *
                                  FROM ' . $table_name . '
                                  WHERE ' . $field_Name . ' = ? AND is_active = 1 AND id!=?', [$value_name,$id]);
            }else{
                $result = DB::select('SELECT *
                                  FROM ' . $table_name . '
                                  WHERE ' . $field_Name . ' = ? AND is_active = 1', [$value_name]);
            }

            if (count($result) > 0) {
                $response = Response::json(array('code' => 201, 'message' => ucfirst($field_Name) . ' already exist.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("checkIsExistInDB : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    public function removeResponseHeadersDetail(){
        try{
            header_remove("X-Powered-By");
            header_remove("Server");
        } catch (Exception $e) {
            Log::error("removeResponseHeadersDetail : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }
}
