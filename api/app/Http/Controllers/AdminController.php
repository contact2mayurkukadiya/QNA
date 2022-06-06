<?php

namespace App\Http\Controllers;

use Response;
use Config;
use DB;
use Log;
use File;
use Cache;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Redis;
use Mail;
use App\Jobs\EmailJob;
use Hash;


class AdminController extends Controller
{
    public $item_count;

    public function __construct()
    {
        $this->item_count = Config::get('constant.PAGINATION_ITEM_LIMIT');
    }

    /**
     * @api {post} statusCode statusCode
     * @apiName statusCode
     * @apiGroup Status Code
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Body:
     * {
     * 400 : Bad Request,
     * 401 : Token Expired,
     * 404 : Not Found,
     * 201 : Error Message,
     * 200 : Success,
     * 425 : Unassigned
     * }
     */

    /* =====================================| Redis Cache Operation |==============================================*/

    /**
     * @api {post} getRedisKeys   getRedisKeys
     * @apiName getRedisKeys
     * @apiGroup Admin
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
     * "message": "Redis keys fetched successfully.",
     * "cause": "",
     * "data": {
     * "keys_list": [
     * "qa:424f74a6a7ed4d4ed4761507ebcd209a6ef0937b:timer",
     * "qa:getQuestionAnswerByAdmin:1:50:qm.id:DESC",
     * "qa:getAllUserForAdmin1:10:update_time:DESC",
     * "qa:424f74a6a7ed4d4ed4761507ebcd209a6ef0937b"
     * ]
     * }
     * }
     */
    public function getRedisKeys()
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $redis_keys = Redis::keys('qa:*');
            //$result = isset($redis_keys)?$redis_keys:'{}';
            $result = ['keys_list' => $redis_keys];
            //Log::info("Total Keys :", [count($redis_keys)]);
            $response = Response::json(array('code' => 200, 'message' => 'Redis keys fetched successfully.', 'cause' => '', 'data' => $result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("getRedisKeys : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Get Redis-Cache Keys.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} deleteRedisKeys   deleteRedisKeys
     * @apiName deleteRedisKeys
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "keys_list": [
     * {
     * "key": "sr:getAllFriendByUser2" //compulsory
     * },
     * {
     * "key": "sr:getAllProfileImgForUser2"
     * },
     * {
     * "key":"sr:getAllTraitNameForUser2"
     * }
     *
     * ]
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Redis Keys Deleted Successfully.",
     * "cause": "",
     * "data": "{}"
     * }
     */
    public function deleteRedisKeys(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);

            if (($response = (new VerificationController())->validateRequiredParam(array('keys_list'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);


            $keys = $request->keys_list;

            foreach ($keys as $rw) {
                if (($response = (new VerificationController())->validateRequiredParameter(array('key'), $rw)) != '')
                    return $response;
            }

            foreach ($keys as $key) {
                Redis::del($key->key);
            }
            $response = Response::json(array('code' => 200, 'message' => 'Redis keys deleted successfully.', 'cause' => '', 'data' => '{}'));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("deleteRedisKeys : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Delete Redis Keys.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} getRedisKeyDetail   getRedisKeyDetail
     * @apiName getRedisKeyDetail
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "key": "sr:getAllTraitNameForUser2" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Redis Key Detail Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "keys_detail": [
     * {
     * "trait_id": 1,
     * "trait_name": "Unchanging",
     * "is_selected": 0
     * },
     * {
     * "trait_id": 2,
     * "trait_name": "Cute",
     * "is_selected": 1
     * },
     * {
     * "trait_id": 3,
     * "trait_name": "Challenging",
     * "is_selected": 0
     * },
     * {
     * "trait_id": 4,
     * "trait_name": "Humorous",
     * "is_selected": 0
     * },
     * {
     * "trait_id": 5,
     * "trait_name": "Good-natured",
     * "is_selected": 1
     * },
     * {
     * "trait_id": 6,
     * "trait_name": "Relaxed",
     * "is_selected": 1
     * },
     * {
     * "trait_id": 7,
     * "trait_name": "Crude",
     * "is_selected": 1
     * },
     * {
     * "trait_id": 8,
     * "trait_name": "Dishonest",
     * "is_selected": 1
     * }
     * ]
     * }
     * }
     */
    public function getRedisKeyDetail(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            //Log::info("getRedisKeyDetails Request:", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('key'), $request)) != '')
                return $response;

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $key = $request->key;
            $key_detail = \Illuminate\Support\Facades\Redis::get($key);
            //return $key_detail;
            $result = ['keys_detail' => unserialize($key_detail)];
            $response = Response::json(array('code' => 200, 'message' => 'Redis Key Detail Fetched Successfully.', 'cause' => '', 'data' => $result));
        } catch (Exception $e) {
            Log::error("getRedisKeyDetail : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Get Redis-Cache Key Detail.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} clearRedisCache   clearRedisCache
     * @apiName clearRedisCache
     * @apiGroup Admin
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
     * "message": "Redis Keys Deleted Successfully.",
     * "cause": "",
     * "data": "{}"
     * }
     */
    public function clearRedisCache()
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            Redis::flushAll();
            $response = Response::json(array('code' => 200, 'message' => 'Redis Keys Deleted Successfully.', 'cause' => '', 'data' => '{}'));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error("clearRedisCache : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'clear Redis-Cache Key.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /* =====================================| User Detail |==============================================*/

    /**
     * @api {post} getAllUserForAdmin getAllUserForAdmin
     * @apiName getAllUserForAdmin
     * @apiGroup Admin-user
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "page":1, //compulsory
     * "item_count":10, //compulsory
     * "order_by":"update_time"
     * "order_type":"desc"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "All user fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 1,
     * "is_next_page": false,
     * "user_detail": [
     * {
     * "user_id": 2,
     * "first_name": "user",
     * "last_name": "user",
     * "email_id": "user@gmail.com",
     * "gender": "1",
     * "phone_no": "7896541230",
     * "coins": 0,
     * "signup_type": 0,
     * "is_active": 1,
     * "is_contact": 1,
     * "create_time": "2019-04-11 00:37:18",
     * "update_time": "2019-04-11 00:37:18"
     * }
     * ]
     * }
     * }
     */
    public function getAllUserForAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count'), $request)) != '')
                return $response;

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'update_time';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->offset = ($this->page - 1) * $this->item_count;


            $total_row_result = DB::select('SELECT COUNT(*) as total FROM user_master as um,
                                              user_detail as ud
                                               WHERE um.id=ud.user_id AND NOT um.is_admin = 1 ');
            $total_row = $total_row_result[0]->total;

            if ($this->order_by == 'signup_type' or $this->order_by == 'is_active' or $this->order_by == 'create_time' or $this->order_by == 'update_time') {
                $this->table_prefix = 'um';
            } else {
                $this->table_prefix = 'ud';
            }

            $result = DB::select('SELECT
                                      um.id as user_id,
                                      COALESCE(ud.first_name,"") AS first_name,
                                      COALESCE(ud.last_name,"") AS last_name,
                                      COALESCE(ud.email_id,"")AS email_id,
                                      COALESCE(ud.gender,"") AS gender,
                                      COALESCE(ud.phone_no,"") AS phone_no,
                                      COALESCE(ud.coins,0) AS coins,
                                      COALESCE(um.signup_type,0) AS signup_type,
                                      COALESCE(um.is_active,0) AS is_active,
                                      ud.is_contact,
                                      um.create_time,
                                      um.update_time
                                      FROM
                                      user_master um, user_detail ud
                                      WHERE um.id=ud.user_id AND NOT um.is_admin = 1
                                      ORDER BY ' . $this->table_prefix . '.' . $this->order_by . ' ' . $this->order_type . ' LIMIT ?,?', [$this->offset, $this->item_count]);


            $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

            $response = Response::json(array('code' => 200, 'message' => 'All user fetched successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'user_detail' => $result]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));
            (new VerificationController())->removeResponseHeadersDetail();

        } catch (Exception $e) {
            Log::error("getAllUserForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fetch all user,' . Config::get('constant.EXCEPTION_ACTION_ERROR'), 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /**
     * @api {post} searchUserForAdmin searchUserForAdmin
     * @apiName searchUserForAdmin
     * @apiGroup Admin-user
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "search_type":"phone_no", //compulsory field : first_name,last_name,email_id,phone_no
     * "search_query":"54321" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Users fetched successfully.",
     * "cause": "",
     * "data": {
     * "user_detail": [
     * {
     * "user_id": 3,
     * "first_name": "jesal",
     * "last_name": "Petel",
     * "email_id": "jesal@grr.la",
     * "gender": "1",
     * "phone_no": "786543210",
     * "coins": 0,
     * "signup_type": 1,
     * "is_active": 1,
     * "is_contact": 0,
     * "create_time": "2019-05-05 16:48:46",
     * "update_time": "2019-05-05 22:18:46"
     * }
     * ]
     * }
     * }
     */
    public function searchUserForAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('search_type', 'search_query'), $request)) != '') {
                return $response;
            }

            $search_type = $request->search_type;
            $search_query = '%' . $request->search_query . '%';

            if ($search_type == 'first_name' or $search_type == 'last_name' or $search_type == 'email_id' or $search_type == 'gender' or $search_type == 'phone_no') {
                $table_prefix = 'ud';
            } else {
                $table_prefix = 'um';

            }
            $result = DB::select('SELECT
                                      um.id as user_id,
                                      COALESCE(ud.first_name,"") AS first_name,
                                      COALESCE(ud.last_name,"") AS last_name,
                                      COALESCE(ud.email_id,"")AS email_id,
                                      COALESCE(ud.gender,"") AS gender,
                                      COALESCE(ud.phone_no,"") AS phone_no,
                                      COALESCE(ud.coins,0) AS coins,
                                      COALESCE(um.signup_type,0) AS signup_type,
                                      COALESCE(um.is_active,0) AS is_active,
                                      ud.is_contact,
                                      um.create_time,
                                      um.update_time
                                      FROM
                                      user_master um, user_detail ud
                                      WHERE um.id=ud.user_id AND NOT um.is_admin = 1 AND 
                                      ' . $table_prefix . '.' . $search_type . ' LIKE ?', [$search_query]);

            $response = Response::json(array('code' => 200, 'message' => 'Users fetched successfully.', 'cause' => '', 'data' => ['user_detail' => $result]));

            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));
            (new VerificationController())->removeResponseHeadersDetail();

        } catch (Exception $e) {
            Log::error("getAllUserForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fetch all user,' . Config::get('constant.EXCEPTION_ACTION_ERROR'), 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /* =========================================| Round Detail  |=========================================*/

    /**
     * @api {post} addRoundDetailByAdmin addRoundDetailByAdmin
     * @apiName addRoundDetailByAdmin
     * @apiGroup Admin-round
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "round_name":"round 3",//compulsory
     * "entry_coins":150,//compulsory
     * "coin_per_answer":20,//compulsory
     * "sec_to_answer":20,//compulsory
     * "coins_minus":200//compulsory
     * "total_question_for_user":20, //compulsory
     * "time_break":2 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Round added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addRoundDetailByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('round_name', 'entry_coins', 'coin_per_answer', 'sec_to_answer', 'coins_minus', 'total_question_for_user', 'time_break'), $request)) != '')
                return $response;

            $round_name = trim($request->round_name);
            $total_question_for_user = $request->total_question_for_user;
            $time_break = $request->time_break;
            $entry_coins = $request->entry_coins;
            $coin_per_answer = $request->coin_per_answer;
            $sec_to_answer = $request->sec_to_answer;
            $coins_minus = $request->coins_minus; //coins minus per second

            if (($response = (new VerificationController())->checkIsExistInDB('', 'round_master', 'round_name', $round_name)) != '')
                return $response;

            $create_at = date('Y-m-d H:i:s');
            DB::beginTransaction();

            DB::insert('INSERT INTO round_master (user_id,round_name,entry_coins,coin_per_answer,sec_to_answer,coins_minus,time_break,total_question_for_user,create_time) 
                        VALUES(?,?,?,?,?,?,?,?,?)',
                [$user_id, $round_name, $entry_coins, $coin_per_answer, $sec_to_answer, $coins_minus, $time_break, $total_question_for_user, $create_at]);

            DB::commit();
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Round added successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("addRoundDetailByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add round detail by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} updateRoundDetailByAdmin updateRoundDetailByAdmin
     * @apiName updateRoundDetailByAdmin
     * @apiGroup Admin-round
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "round_id":1,//compulsory
     * "round_name":"round 3",//compulsory
     * "entry_coins":150,//compulsory
     * "coin_per_answer":20,//compulsory
     * "sec_to_answer":20,//compulsory
     * "coins_minus":200//compulsory
     * "total_question_for_user":20, //compulsory
     * "time_break":2 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Round updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateRoundDetailByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('round_id', 'round_name', 'entry_coins', 'coin_per_answer', 'sec_to_answer', 'coins_minus', 'total_question_for_user', 'time_break'), $request)) != '')
                return $response;

            $round_id = $request->round_id;
            $round_name = trim($request->round_name);
            $entry_coins = $request->entry_coins;
            $coin_per_answer = $request->coin_per_answer;
            $sec_to_answer = $request->sec_to_answer;
            $coins_minus = $request->coins_minus; //coins minus per second
            $total_question_for_user = $request->total_question_for_user;
            $time_break = $request->time_break;


            if (($response = (new VerificationController())->checkIsExistInDB($round_id, 'round_master', 'round_name', $round_name)) != '')
                return $response;

            DB::beginTransaction();
            DB::update('UPDATE
                              round_master
                            SET
                              user_id = ?,
                              round_name=?,
                              entry_coins=?,
                              coin_per_answer=?,
                              sec_to_answer=?,
                              coins_minus=?,
                              total_question_for_user=?,
                              time_break=?
                            WHERE
                              id = ? ',
                [$user_id, $round_name, $entry_coins, $coin_per_answer, $sec_to_answer, $coins_minus, $total_question_for_user, $time_break, $round_id]);


            DB::commit();
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Round updated successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("updateRoundDetailByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update round detail by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} deleteRoundDetailByAdmin deleteRoundDetailByAdmin
     * @apiName deleteRoundDetailByAdmin
     * @apiGroup Admin-round
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "round_id":1//compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Round deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteRoundDetailByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('round_id'), $request)) != '')
                return $response;

            $round_id = $request->round_id;

            DB::beginTransaction();
            DB::delete('DELETE
                        FROM
                          round_master
                        WHERE
                          id = ?', [$round_id]);
            DB::commit();
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Round deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("deleteRoundDetailByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'deleted round detail by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getRoundDetailByAdmin getRoundDetailByAdmin
     * @apiName getRoundDetailByAdmin
     * @apiGroup Admin-round
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "page":1, //compulsory
     * "item_count":10, //compulsory
     * "order_by":"update_time"
     * "order_type":"asc"
     *
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Round fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "round_id": 1,
     * "round_name": "round 3",
     * "entry_coins": 200,
     * "coin_per_answer": 20,
     * "sec_to_answer": 20,
     * "coins_minus": 200,
     * "time_break": 2,
     * "total_question_for_user": 20,
     * "is_active": 1,
     * "create_time": "2019-04-03 17:20:46",
     * "update_time": "2019-04-03 23:00:17"
     * }
     * ]
     * }
     * }
     */
    public function getRoundDetailByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count'), $request)) != '')
                return $response;

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'update_time';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->offset = ($this->page - 1) * $this->item_count;

            if ($this->order_by == "round_id") {
                $this->order_by = "id";
            }

            $total_round = DB::select('SELECT COUNT(*) AS total FROM round_master WHERE is_active=1');
            $total_round = $total_round[0]->total;

            if (!Cache::has("qa:getRoundDetailByAdmin:$this->page:$this->item_count:$this->order_by:$this->order_type")) {
                $result = Cache::rememberforever("getRoundDetailByAdmin:$this->page:$this->item_count:$this->order_by:$this->order_type", function () {

                    return DB::select('SELECT 
                            id AS round_id,
                            user_id,
                            round_name,
                            entry_coins,
                            coin_per_answer,
                            sec_to_answer,
                            coins_minus,
                            time_break,
                            total_question_for_user,
                            is_active,
                            create_time,
                            update_time
                            FROM round_master ORDER BY ' . $this->order_by . ' ' . $this->order_type . '
                            LIMIT ?,?', [$this->offset, $this->item_count]);

                });
            }
            $redis_result = Cache::get("getRoundDetailByAdmin:$this->page:$this->item_count:$this->order_by:$this->order_type");
            $is_next_page = ($total_round > ($this->offset + $this->item_count)) ? true : false;
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Round fetched successfully.', 'cause' => '', 'data' => ['total_round' => $total_round, 'is_next_page' => $is_next_page, 'result' => $redis_result]));
        } catch (Exception $e) {
            Log::error("getRoundDetailByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get round detail by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /* =========================================| Question Answer Detail  |=========================================*/

    /**
     * @api {post} addQuestionAnswerByAdmin addQuestionAnswerByAdmin
     * @apiName addQuestionAnswerByAdmin
     * @apiGroup Admin-Quesion Answer
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{
     * "round_id":1,//compulsory
     * "question":"What is this?",//compulsory
     * "answer_a":"Answer a",//compulsory
     * "answer_b":"Answer b",//compulsory
     * "answer_c":"Answer c",//compulsory
     * "answer_d":"Answer d",//compulsory
     * "real_answer":"answer_a"//compulsory
     * }
     * file:i.jpg //if question with image
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Question added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addQuestionAnswerByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->input('request_data'));
            if (!$request_body->has('request_data'))
                return Response::json(array('code' => 201, 'message' => 'Required field request_data is missing or empty', 'cause' => '', 'data' => json_decode("{}")));

            if (($response = (new VerificationController())->validateRequiredParameter(array('round_id', 'question', 'answer_a', 'answer_b', 'answer_c', 'answer_d', 'real_answer'), $request)) != '')
                return $response;

            $round_id = $request->round_id;
            $question = $request->question;
            $answer_a = $request->answer_a;
            $answer_b = $request->answer_b;
            $answer_c = $request->answer_c;
            $answer_d = $request->answer_d;
            $real_answer = $request->real_answer;
            $is_question_image = NULL;
            $create_at = date('Y-m-d H:i:s');

            if ($request_body->hasFile('file')) {
                $image_array = Input::file('file');
                if (($response = (new ImageController())->verifyImage($image_array)) != '')
                    return $response;

                $is_question_image = (new ImageController())->generateNewFileName('question_image', $image_array);
                (new ImageController())->saveOriginalImage($is_question_image);
                (new ImageController())->saveCompressedImage($is_question_image);
                (new ImageController())->saveThumbnailImage($is_question_image);
            }

            DB::beginTransaction();
            DB::insert('INSERT INTO question_master (user_id,round_id,question,is_question_image,answer_a,answer_b,answer_c,answer_d,real_answer,is_active,create_time) 
                            VALUES(?,?,?,?,?,?,?,?,?,?,?)',
                [$user_id, $round_id, $question, $is_question_image, $answer_a, $answer_b, $answer_c, $answer_d, $real_answer, 1, $create_at]);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Question added successfully.', 'cause' => '', 'data' => json_decode('{}')));
            (new VerificationController())->removeResponseHeadersDetail();

        } catch (Exception $e) {
            Log::error("addQuestionAnswerByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add question by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} updateQuestionAnswerByAdmin updateQuestionAnswerByAdmin
     * @apiName updateQuestionAnswerByAdmin
     * @apiGroup Admin-Quesion Answer
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     *{
     * "question_id":1,//compulsory
     * "round_id":1,//compulsory
     * "question":"What is this?",//compulsory
     * "answer_a":"Answer a",//compulsory
     * "answer_b":"Answer b",//compulsory
     * "answer_c":"Answer c update",//compulsory
     * "answer_d":"Answer d",//compulsory
     * "real_answer":"answer_b"//compulsory
     * }
     * file:1.jpg //if question with image
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Question updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateQuestionAnswerByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $user = Auth::user();
            $user_id = $user->id;

            if (!$request_body->has('request_data'))
                return Response::json(array('code' => 201, 'message' => 'Required field request_data is missing or empty', 'cause' => '', 'data' => json_decode("{}")));

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(array('question_id', 'round_id', 'question', 'answer_a', 'answer_b', 'answer_c', 'answer_d', 'real_answer'), $request)) != '')
                return $response;

            $question_id = $request->question_id;
            $round_id = $request->round_id;
            $question = $request->question;
            $answer_a = $request->answer_a;
            $answer_b = $request->answer_b;
            $answer_c = $request->answer_c;
            $answer_d = $request->answer_d;
            $real_answer = $request->real_answer;
            $is_question_image = "";

            if ($request_body->hasFile('file')) {
                $image_array = Input::file('file');
                if (($response = (new ImageController())->verifyImage($image_array)) != '')
                    return $response;

                $is_question_image = (new ImageController())->generateNewFileName('question_image', $image_array);
                (new ImageController())->saveOriginalImage($is_question_image);
                (new ImageController())->saveCompressedImage($is_question_image);
                (new ImageController())->saveThumbnailImage($is_question_image);

                $exit_data = DB::select('SELECT * FROM question_master WHERE id = ?', [$question_id]);
                $question_image = $exit_data[0]->is_question_image;

                if ($question_image) {
                    //Image Delete in image_bucket
                    (new ImageController())->deleteImage($question_image);
                }
            }

            DB::beginTransaction();
            DB::update('UPDATE question_master 
                        SET round_id = ?, 
                            user_id = ?,
                            question = ?,
                            is_question_image = IF(? != "",?,is_question_image),
                            answer_a=?,
                            answer_b=?,
                            answer_c=?,
                            answer_d=?,
                            real_answer=?
                        WHERE id=?',
                [$round_id, $user_id, $question, $is_question_image, $is_question_image, $answer_a, $answer_b, $answer_c, $answer_d, $real_answer, $question_id]);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Question updated successfully.', 'cause' => '', 'data' => json_decode('{}')));
            (new VerificationController())->removeResponseHeadersDetail();

        } catch (Exception $e) {
            Log::error("updateQuestionAnswerByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update question by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} deleteQuestionAnswerByAdmin deleteQuestionAnswerByAdmin
     * @apiName deleteQuestionAnswerByAdmin
     * @apiGroup Admin-Quesion Answer
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "question_id":1//compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Question deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteQuestionAnswerByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('question_id'), $request)) != '')
                return $response;

            $question_id = $request->question_id;

            $exit_data = DB::select('SELECT * FROM question_master WHERE id = ?', [$question_id]);
            if (count($exit_data) > 0) {
                $question_image = $exit_data[0]->is_question_image;

                DB::beginTransaction();
                DB::delete('DELETE
                        FROM
                          question_master
                        WHERE
                          id = ?', [$question_id]);
                DB::commit();

                if ($question_image) {
                    //Image Delete in image_bucket
                    (new ImageController())->deleteImage($question_image);
                }
            }

            $response = Response::json(array('code' => 200, 'message' => 'Question deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));
            (new VerificationController())->removeResponseHeadersDetail();

        } catch (Exception $e) {
            Log::error("deleteQuestionAnswerByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'deleted question detail by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getQuestionAnswerByAdmin getQuestionAnswerByAdmin
     * @apiName getQuestionAnswerByAdmin
     * @apiGroup Admin-Quesion Answer
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "page":1,
     * "item_count":10,
     * "order_by":"round_name",
     * "order_type":"asc"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Question fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_question": 7,
     * "is_next_page": false,
     * "result": [
     * {
     * "question_id": 6,
     * "round_name": "round 4",
     * "question_thumbnail_image": "",
     * "question_compressed_image": "",
     * "question_original_image": "",
     * "question": "What is this?",
     * "answer_a": "answer round 1",
     * "answer_b": "answer round 1",
     * "answer_c": "answer round 1",
     * "answer_d": "answer round 1",
     * "real_answer": "answer_a",
     * "update_time": "2019-04-05 23:22:56"
     * },
     * {
     * "question_id": 7,
     * "round_name": "round 4",
     * "question_thumbnail_image": "http://localhost/question_answer/image_bucket/thumbnail/5ca7967d8a0d0_question_image_1554486909.jpg",
     * "question_compressed_image": "http://localhost/question_answer/image_bucket/compressed/5ca7967d8a0d0_question_image_1554486909.jpg",
     * "question_original_image": "http://localhost/question_answer/image_bucket/original/5ca7967d8a0d0_question_image_1554486909.jpg",
     * "question": "What is this?",
     * "answer_a": "answer round 1",
     * "answer_b": "answer round 1",
     * "answer_c": "answer round 1",
     * "answer_d": "answer round 1",
     * "real_answer": "answer_c",
     * "update_time": "2019-04-05 23:25:10"
     * }
     * ]
     * }
     * }
     */
    public function getQuestionAnswerByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count'), $request)) != '')
                return $response;

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'update_time';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->offset = ($this->page - 1) * $this->item_count;

            $result = DB::select('SELECT 
                            COUNT(*) AS total
                            FROM question_master qm LEFT JOIN round_master rm ON rm.id=qm.round_id');
            $total_question = $result[0]->total;

            if ($this->order_by == "round_name") {
                $this->order_by = "rm.round_name";
            } elseif ($this->order_by == "question_id") {
                $this->order_by = "qm.id";
            } else {
                $this->order_by = 'qm.' . $this->order_by;
            }

            if (!Cache::has("qa:getQuestionAnswerByAdmin:$this->page:$this->item_count:$this->order_by:$this->order_type")) {
                $result = Cache::rememberforever("getQuestionAnswerByAdmin:$this->page:$this->item_count:$this->order_by:$this->order_type", function () {

                    return DB::select('SELECT 
                                          qm.id AS question_id,
                                          rm.round_name,
                                          IF(qm.is_question_image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_QA') . '",qm.is_question_image),"") as question_thumbnail_image,
                                          IF(qm.is_question_image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_QA') . '",qm.is_question_image),"") as question_compressed_image,
                                          IF(qm.is_question_image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_QA') . '",qm.is_question_image),"") as question_original_image,
                                          qm.question,
                                          qm.answer_a,
                                          qm.answer_b,
                                          qm.answer_c,
                                          qm.answer_d,
                                          qm.real_answer,
                                          qm.update_time
                                          FROM question_master qm LEFT JOIN round_master rm ON rm.id=qm.round_id
                                          ORDER BY ' . $this->order_by . ' ' . $this->order_type . '
                                          LIMIT ?,?', [$this->offset, $this->item_count]);

                });
            }
            $redis_result = Cache::get("getQuestionAnswerByAdmin:$this->page:$this->item_count:$this->order_by:$this->order_type");
            $is_next_page = ($total_question > ($this->offset + $this->item_count)) ? true : false;
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Question fetched successfully.', 'cause' => '', 'data' => ['total_question' => $total_question, 'is_next_page' => $is_next_page, 'result' => $redis_result]));
        } catch (Exception $e) {
            Log::error("getQuestionAnswerByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'question detail by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getQuestionAnswerFromRoundByAdmin getQuestionAnswerFromRoundByAdmin
     * @apiName getQuestionAnswerFromRoundByAdmin
     * @apiGroup Admin-Quesion Answer
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "round_id":3,
     * "page":1,
     * "item_count":2,
     * "order_by":"question",
     * "order_type":"asc"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Question fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_question": 3,
     * "is_next_page": true,
     * "result": [
     * {
     * "round_id": 1,
     * "round_name": "round 3",
     * "entry_coins": 200,
     * "coin_per_answer": 20,
     * "sec_to_answer": 20,
     * "coins_minus": 200,
     * "is_active": 1,
     * "total_question": 2,
     * "questions_detail": [
     * {
     * "question_id": 1,
     * "question": "What is this?",
     * "question_thumbnail_image": "http://localhost/question_answer/image_bucket/thumbnail/5ca6304872446_question_image_1554395208.jpg",
     * "question_compressed_image": "http://localhost/question_answer/image_bucket/compressed/5ca6304872446_question_image_1554395208.jpg",
     * "question_original_image": "http://localhost/question_answer/image_bucket/original/5ca6304872446_question_image_1554395208.jpg",
     * "answer_a": "Answer a",
     * "answer_b": "Answer b",
     * "answer_c": "Answer c update",
     * "answer_d": "Answer d",
     * "real_answer": "b"
     * },
     * {
     * "question_id": 2,
     * "question": "What is this?",
     * "question_thumbnail_image": "",
     * "question_compressed_image": "",
     * "question_original_image": "",
     * "answer_a": "Answer a cat 1 jwfh @#$%^",
     * "answer_b": "Answer a cat 1 jwfh @#$%^",
     * "answer_c": "Answer a cat 1 jwfh @#$%^",
     * "answer_d": "Answer a cat 1 jwfh @#$%^",
     * "real_answer": "a"
     * }
     * ]
     * }
     * ]
     * }
     * }
     */
    public function getQuestionAnswerFromRoundByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('round_id', 'page', 'item_count'), $request)) != '')
                return $response;

            $this->round_id = $request->round_id;
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'update_time';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->offset = ($this->page - 1) * $this->item_count;

            $result = DB::select('SELECT 
                            COUNT(*) AS total
                            FROM question_master qm LEFT JOIN round_master rm ON rm.id=qm.round_id WHERE rm.id = ?', [$this->round_id]);
            $total_question = $result[0]->total;

            if (!Cache::has("qa:getQuestionAnswerFromRoundByAdmin:$request->round_id:$this->page:$this->item_count:$this->order_by:$this->order_type")) {
                $result = Cache::rememberforever("getQuestionAnswerFromRoundByAdmin:$request->round_id:$this->page:$this->item_count:$this->order_by:$this->order_type", function () {

                    $rount_detail = DB::select('SELECT 
                            rm.id AS round_id,
                            rm.round_name,
                            rm.entry_coins,
                            rm.coin_per_answer,
                            rm.sec_to_answer,
                            rm.coins_minus,
                            rm.is_active,
                            COUNT(rm.id) AS total_question
                            FROM question_master qm LEFT JOIN round_master rm ON rm.id=qm.round_id
                            WHERE qm.round_id=?
                            GROUP BY rm.id', [$this->round_id]);

                    foreach ($rount_detail AS $round) {
                        $round_id = $round->round_id;
                        $question_by_round = DB::select('SELECT 
                                          qm.id AS question_id,
                                          qm.question,
                                          IF(qm.is_question_image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_QA') . '",qm.is_question_image),"") as question_thumbnail_image,
                                          IF(qm.is_question_image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_QA') . '",qm.is_question_image),"") as question_compressed_image,
                                          IF(qm.is_question_image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_QA') . '",qm.is_question_image),"") as question_original_image,
                                          qm.answer_a,
                                          qm.answer_b,
                                          qm.answer_c,
                                          qm.answer_d,
                                          qm.real_answer
                                          FROM question_master qm
                                          WHERE qm.round_id=?
                                           ORDER BY qm.' . $this->order_by . ' ' . $this->order_type . '
                                          LIMIT ?,?', [$round_id, $this->offset, $this->item_count]);

                        $round->questions_detail = $question_by_round;
                    }
                    return $rount_detail;
                });
            }

            $redis_result = Cache::get("getQuestionAnswerFromRoundByAdmin:$request->round_id:$this->page:$this->item_count:$this->order_by:$this->order_type");

            $is_next_page = ($total_question > ($this->offset + $this->item_count)) ? true : false;

            $response = Response::json(array('code' => 200, 'message' => 'Question fetched successfully.', 'cause' => '', 'data' => ['total_question' => $total_question, 'is_next_page' => $is_next_page, 'result' => $redis_result]));
            (new VerificationController())->removeResponseHeadersDetail();
        } catch (Exception $e) {
            Log::error("getQuestionAnswerFromRoundByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'question detail from round by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} addQuestionAnswerFromExcelByAdmin addQuestionAnswerFromExcelByAdmin
     * @apiName addQuestionAnswerFromExcelByAdmin
     * @apiGroup Admin-Quesion Answer
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * file:i.jpg //Currently use csv file
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Question added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addQuestionAnswerFromExcelByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $user = Auth::user();
            $user_id = $user->id;

            if (!$request_body->hasFile('file'))
                return Response::json(array('code' => 201, 'message' => 'Required field file is missing or empty', 'cause' => '', 'data' => json_decode("{}")));

            $file_array = Input::file('file');
            $file_name = $file_array->getClientOriginalName();

            //Log::info('file_type : ', ['file_type' => $_FILES['file']['tmp_name'], 'file_ext' => File::extension($file_name)]);

            if (File::extension($file_name) == 'csv') {
                // Good to go

                $csv_file_type = array('text/csv');
                $csvMimes = array('application/vnd.ms-excel');
                $filename = $_FILES["file"]["tmp_name"];
                $file_type = $_FILES['file']['type'];
                $csvFile = fopen($filename, 'r');
                //skip first line
                fgetcsv($csvFile);
                DB::beginTransaction();
                $i = 2;
                //parse data from csv file line by line
                //DB::delete('DELETE FROM question_master');
                while (($line = fgetcsv($csvFile)) !== FALSE) {

                    $round_id = isset($line[0]) ? $line[0] : '';
                    $question = isset($line[1]) ? $line[1] : '';
                    $answer_a = isset($line[2]) ? $line[2] : '';
                    $answer_b = isset($line[3]) ? $line[3] : '';
                    $answer_c = isset($line[4]) ? $line[4] : '';
                    $answer_d = isset($line[5]) ? $line[5] : '';
                    $real_answer = isset($line[6]) ? $line[6] : '';
                    //$is_question_image = isset($line[7]) ? $line[7] : '';
                    $create_at = date('Y-m-d H:i:s');
                    Log::info('$question', ['$question' => $question]);
                    $validation_data = array(
                        'user_id' => $user_id,
                        'round_id' => $round_id,
                        'answer_a' => $answer_a,
                        'answer_b' => $answer_b,
                        'answer_c' => $answer_c,
                        'answer_d' => $answer_d,
                        'real_answer' => $real_answer,
                        'create_time' => $create_at,
                        'question' => $question
                    );

                    Log::info('validation', ['validation' => $validation_data]);
                    // (new VerificationController())->required_validation($validation_data, $i);
                    $count = 0;
                    foreach ($validation_data as $key => $value) {
                        if (empty($value)) {
                            //return Response::json(array('code' => '201', 'message' => 'Please enter ' . $key . ' at line ' . $i . '.', 'cause' => '', 'response' => json_decode("{}")));
                            $count = $count + 1;
                        }
                    }

                    if ($count == 0) {

                        $data = array(
                            'round_id' => $round_id,
                            'question' => $question,
                            'answer_a' => $answer_a,
                            'answer_b' => $answer_b,
                            'answer_c' => $answer_c,
                            'answer_d' => $answer_d,
                            'real_answer' => $real_answer,
                            'create_time' => $create_at,
                        );
                        Log::info('$data', ['$data' => $data]);
                        DB::beginTransaction();

                        $question_id = DB::table('question_master')->insertGetId($validation_data);

                        DB::commit();
                    }
                    $i++;
                }
                DB::commit();
                //close opened csv file
                fclose($csvFile);
                $response = Response::json(array('code' => 200, 'message' => 'Question added successfully.', 'cause' => '', 'data' => json_decode('{}')));

            } else {
                //Log::info('file_type', ['file_type' => $_FILES['file']['type']]);
                $response = Response::json(array('code' => '201', 'message' => 'You can upload only CSV file.', 'cause' => '', 'data' => json_decode("{}")));
            }

            (new VerificationController())->removeResponseHeadersDetail();

        } catch (Exception $e) {
            Log::error("addQuestionAnswerFromExcelByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add question answer from by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /* =========================================| FAQ Detail  |=========================================*/

    /**
     * @api {post} addFAQByAdmin addFAQByAdmin
     * @apiName addFAQByAdmin
     * @apiGroup Admin-FAQ
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "faq_question":"Question ?",//compulsory
     * "faq_answer":"Answer,//compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "FAQ added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addFAQByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('faq_question', 'faq_answer'), $request)) != '')
                return $response;

            $faq_question = $request->faq_question;
            $faq_answer = $request->faq_answer;
            $is_active = 1;
            $create_at = date('Y-m-d H:i:s');

            DB::beginTransaction();

            DB::insert('INSERT INTO faq_master (user_id,faq_question,faq_answer,is_active,create_time) 
                        VALUES(?,?,?,?,?)',
                [$user_id, $faq_question, $faq_answer, $is_active, $create_at]);

            DB::commit();
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'FAQ added successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("addFAQByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add faq detail by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} updateFAQByAdmin updateFAQByAdmin
     * @apiName updateFAQByAdmin
     * @apiGroup Admin-FAQ
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "faq_id":2,//compulsory
     * "faq_question":"Question ?",//compulsory
     * "faq_answer":"Answer"//compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "FAQ updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateFAQByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('faq_id', 'faq_question', 'faq_answer'), $request)) != '')
                return $response;

            $faq_id = $request->faq_id;
            $faq_question = $request->faq_question;
            $faq_answer = $request->faq_answer;

            DB::beginTransaction();
            DB::update('UPDATE faq_master 
                        SET faq_question = ?, faq_answer = ?, user_id = ?
                        WHERE id = ?',
                [$faq_question, $faq_answer, $user_id, $faq_id]);
            DB::commit();

            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'FAQ updated successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("updateFAQByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update faq by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} deleteFAQByAdmin deleteFAQByAdmin
     * @apiName deleteFAQByAdmin
     * @apiGroup Admin-FAQ
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "faq_id":1//compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "FAQ deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteFAQByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('faq_id'), $request)) != '')
                return $response;

            $faq_id = $request->faq_id;

            DB::beginTransaction();
            DB::delete('DELETE
                        FROM
                          faq_master
                        WHERE
                          id = ?', [$faq_id]);
            DB::commit();
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'FAQ deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("deleteFAQByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'deleted faq detail by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} setStatusOfFAQByAdmin setStatusOfFAQByAdmin
     * @apiName setStatusOfFAQByAdmin
     * @apiGroup Admin-FAQ
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "is_active":1//compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Set status successfully of FAQ.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function setStatusOfFAQByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('is_active', 'faq_id'), $request)) != '')
                return $response;

            $faq_id = $request->faq_id;
            $is_active = $request->is_active;

            DB::beginTransaction();
            DB::update('UPDATE faq_master 
                        SET is_active = ?, user_id = ?
                        WHERE id = ?',
                [$is_active, $user_id, $faq_id]);
            DB::commit();
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Set status successfully of FAQ.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("is_active : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'set status of faq by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getFAQByAdmin getFAQByAdmin
     * @apiName getFAQByAdmin
     * @apiGroup Admin-FAQ
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "page":1, //compulsory
     * "item_count":10, //compulsory
     * "order_by":"update_time",
     * "order_type":"DESC"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "FAQ fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_round": 2,
     * "is_next_page": false,
     * "result": [
     * {
     * "faq_id": 2,
     * "faq_question": "Can I use?",
     * "faq_answer": "You can copy, modify, distribute, and use the images, even for commercial purposes, all without asking for permission or giving credits to the artist. However, depicted content may still be protected by trademarks, publicity or privacy rights",
     * "is_active": 1,
     * "create_time": "2019-04-26 16:12:12",
     * "update_time": "2019-04-26 21:44:17"
     * },
     * {
     * "faq_id": 1,
     * "faq_question": "Can I use your images?",
     * "faq_answer": "You can copy, modify, distribute, and use the images, even for commercial purposes, all without asking for permission or giving credits to the artist. However, depicted content may still be protected by trademarks, publicity or privacy rights",
     * "is_active": 1,
     * "create_time": "2019-04-26 16:12:02",
     * "update_time": "2019-04-26 21:42:02"
     * }
     * ]
     * }
     * }
     */
    public function getFAQByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count'), $request)) != '')
                return $response;

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'update_time';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->offset = ($this->page - 1) * $this->item_count;

            if ($this->order_by == "faq_id") {
                $this->order_by = "id";
            }

            $total_round = DB::select('SELECT COUNT(*) AS total FROM faq_master');
            $total_round = $total_round[0]->total;

            if (!Cache::has("qa:getFAQByAdmin:$this->page:$this->item_count:$this->order_by:$this->order_type")) {
                $result = Cache::rememberforever("getFAQByAdmin:$this->page:$this->item_count:$this->order_by:$this->order_type", function () {

                    return DB::select('SELECT 
                            id AS faq_id,
                            user_id,
                            faq_question,
                            faq_answer,
                            is_active,
                            create_time,
                            update_time
                            FROM faq_master 
                            ORDER BY ' . $this->order_by . ' ' . $this->order_type . '
                             LIMIT ?,?', [$this->offset, $this->item_count]);

                });
            }
            $redis_result = Cache::get("getFAQByAdmin:$this->page:$this->item_count:$this->order_by:$this->order_type");
            $is_next_page = ($total_round > ($this->offset + $this->item_count)) ? true : false;
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'FAQ fetched successfully.', 'cause' => '', 'data' => ['total_round' => $total_round, 'is_next_page' => $is_next_page, 'result' => $redis_result]));
        } catch (Exception $e) {
            Log::error("getFAQByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get faq by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /* =========================================| Terms and Conditions  |=========================================*/

    /**
     * @api {post} addTermsNConditionsByAdmin addTermsNConditionsByAdmin
     * @apiName addTermsNConditionsByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "subject":"Use of the Service", //compulsory
     * "description":"In connection with your use of the Service you will not engage in or use any data mining, robots, scraping or similar data gathering or extraction methods. The technology and software underlying the Service or distributed in connection therewith is the property of Pixabay and our licensors, affiliates and partners and you are granted no license in respect of that Software." //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Term and condition added successfully.",
     * "cause": "",
     * "data": {}
     * }
     *
     */
    public function addTermsNConditionsByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('subject', 'description'), $request)) != '')
                return $response;

            $subject = $request->subject;
            $description = $request->description;
            $is_active = 1;
            $create_at = date('Y-m-d H:i:s');

            DB::beginTransaction();
            DB::insert('INSERT INTO terms_n_conditions (user_id, subject,description,is_active,create_time) 
                        VALUES(?,?,?,?,?)',
                [$user_id, $subject, $description, $is_active, $create_at]);
            DB::commit();

            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Term and condition added successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("addTermsNConditionsByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add terms and conditions by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} updateTermsNConditionsByAdmin updateTermsNConditionsByAdmin
     * @apiName updateTermsNConditionsByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "term_n_condition_id":2, //compulsory
     * "subject":"License for Images and Videos",  //compulsory
     * "description":"sale or distribution of Images or Videos as digital stock photos or as digital wallpapers;sale or distribution of Images or Videos"  //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Term and condition updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateTermsNConditionsByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('term_n_condition_id', 'subject', 'description'), $request)) != '')
                return $response;

            $term_n_condition_id = $request->term_n_condition_id;
            $subject = $request->subject;
            $description = $request->description;

            DB::beginTransaction();
            DB::update('UPDATE terms_n_conditions 
                        SET subject = ?, description = ?, user_id = ? 
                        WHERE id = ?',
                [$subject, $description, $user_id, $term_n_condition_id]);
            DB::commit();

            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Term and condition updated successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("updateTermsNConditionsByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update term and condition by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} deleteTermsNConditionsByAdmin deleteTermsNConditionsByAdmin
     * @apiName deleteTermsNConditionsByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "term_n_condition_id":1//compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Term and condition deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteTermsNConditionsByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('term_n_condition_id'), $request)) != '')
                return $response;

            $term_n_condition_id = $request->term_n_condition_id;

            DB::beginTransaction();
            DB::delete('DELETE
                        FROM
                          terms_n_conditions
                        WHERE
                          id = ?', [$term_n_condition_id]);
            DB::commit();
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Term and condition deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("deleteTermsNConditionsByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'deleted term and condition detail by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} setStatusOfTermsNConditionsByAdmin setStatusOfTermsNConditionsByAdmin
     * @apiName setStatusOfTermsNConditionsByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "is_active":1,//compulsory
     * "term_n_condition_id":1//compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Set status successfully of term and condition.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function setStatusOfTermsNConditionsByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('is_active', 'term_n_condition_id'), $request)) != '')
                return $response;

            $term_n_condition_id = $request->term_n_condition_id;
            $is_active = $request->is_active;

            DB::beginTransaction();
            DB::update('UPDATE terms_n_conditions 
                        SET is_active = ?,
                            user_id = ?
                        WHERE id = ?',
                [$is_active, $user_id, $term_n_condition_id]);
            DB::commit();
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Set status successfully of term and condition.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("is_active : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'set status of term and condition by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getTermsNConditionsByAdmin getTermsNConditionsByAdmin
     * @apiName getTermsNConditionsByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "page":1, //compulsory
     * "item_count":10, //compulsory
     * "order_by":"update_time",
     * "order_type":"DESC"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Terms and conditions fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_items": 2,
     * "is_next_page": false,
     * "result": [
     * {
     * "term_n_condition_id": 3,
     * "subject": "License for Images and Videos – Pixabay License",
     * "description": "sale or distribution of Images or Videos as digital stock photos or as digital wallpapers;sale or distribution of Images or Videos e.g. as a posters, digital prints or physical products, without adding any additional elements or otherwise adding value",
     * "is_active": 0,
     * "create_time": "2019-04-28 04:08:35",
     * "update_time": "2019-04-28 09:45:28"
     * }
     * ]
     * }
     * }
     */
    public function getTermsNConditionsByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count'), $request)) != '')
                return $response;

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'update_time';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->offset = ($this->page - 1) * $this->item_count;

            if ($this->order_by == "term_n_condition_id") {
                $this->order_by = "id";
            }

            $total_item = DB::select('SELECT COUNT(*) AS total FROM terms_n_conditions');
            $total_item = $total_item[0]->total;

            if (!Cache::has("qa:getTermsNConditionsByAdmin:$this->page:$this->item_count:$this->order_by:$this->order_type")) {
                $result = Cache::rememberforever("getTermsNConditionsByAdmin:$this->page:$this->item_count:$this->order_by:$this->order_type", function () {

                    return DB::select('SELECT 
                            id AS term_n_condition_id,
                            user_id,
                            subject,
                            description,
                            is_active,
                            create_time,
                            update_time
                            FROM terms_n_conditions 
                            ORDER BY ' . $this->order_by . ' ' . $this->order_type . '
                             LIMIT ?,?', [$this->offset, $this->item_count]);
                });
            }
            $redis_result = Cache::get("getTermsNConditionsByAdmin:$this->page:$this->item_count:$this->order_by:$this->order_type");
            $is_next_page = ($total_item > ($this->offset + $this->item_count)) ? true : false;
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Terms and conditions fetched successfully.', 'cause' => '', 'data' => ['total_items' => $total_item, 'is_next_page' => $is_next_page, 'result' => $redis_result]));
        } catch (Exception $e) {
            Log::error("getTermsNConditionsByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get terms and conditions by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /* =========================================| Contact for admin  |=========================================*/

    public function replayToContactByAdmin_old(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('subject', 'description', 'receiver_user_id'), $request)) != '')
                return $response;

            $subject = $request->subject;
            $description = $request->description;
            $receiver_user_id = $request->receiver_user_id;
            $is_active = 1;
            $is_contact = 0;
            $create_at = date('Y-m-d H:i:s');

            $user_detail = DB::select('SELECT email_id FROM user_master WHERE id = ? ', [$receiver_user_id]);
            DB::beginTransaction();
            DB::insert('INSERT INTO contact_master (sender_user_id,receiver_user_id,subject,description,is_active,create_time) 
                        VALUES(?,?,?,?,?,?)',
                [$user_id, $receiver_user_id, $subject, $description, $is_active, $create_at]);
            DB::update('UPDATE user_detail SET is_contact = ? WHERE user_id = ? ', [$is_contact, $receiver_user_id]);
            DB::commit();

            if (count($user_detail) > 0) {
                $email_id = $user_detail[0]->email_id;
                if ($email_id) {
                    $template = 'replay2contact';
                    $mail_subject = $subject;
                    $message_body = $description;
                    $api_name = 'replayToContactByAdmin';
                    $api_description = 'send mail here for replay in user query.';
                    $this->dispatch(new EmailJob(1, $email_id, $mail_subject, $message_body, $template, $api_name, $api_description));
                }
            }
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Replay has been sent successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("replayToContactByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'replay to contact by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function getContactDetailByAdmin_old(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user = Auth::user();
            $this->replay_id = $user->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count', 'user_id'), $request)) != '')
                return $response;

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->sender_user_id = $request->user_id;
            $this->offset = ($this->page - 1) * $this->item_count;

            $total_contacts = DB::select('SELECT COUNT(*) AS total 
                                          FROM contact_master 
                                          WHERE is_active = 1 AND 
                                                (sender_user_id = ? OR receiver_user_id = ?) AND 
                                                (sender_user_id = ? OR receiver_user_id = ?)',
                [$this->replay_id, $this->replay_id, $this->sender_user_id, $this->sender_user_id]);
            $total_contacts = $total_contacts[0]->total;

            if (!Cache::has("qa:getContactDetailByUser:$this->page:$this->item_count:$this->replay_id:$this->sender_user_id")) {
                $result = Cache::rememberforever("getContactDetailByUser:$this->page:$this->item_count:$this->replay_id:$this->sender_user_id", function () {

                    return DB::select('SELECT 
                            id AS contact_id,
                            sender_user_id,
                            receiver_user_id,
                            subject,
                            description,
                            create_time,
                            update_time
                            FROM contact_master 
                            WHERE is_active = 1 AND (sender_user_id = ? OR receiver_user_id = ?) AND (sender_user_id = ? OR receiver_user_id = ?)
                            ORDER BY update_time desc
                            LIMIT ?,?', [$this->replay_id, $this->replay_id, $this->sender_user_id, $this->sender_user_id, $this->offset, $this->item_count]);

                });
            }
            $redis_result = Cache::get("getContactDetailByUser:$this->page:$this->item_count:$this->replay_id:$this->sender_user_id");
            $is_next_page = ($total_contacts > ($this->offset + $this->item_count)) ? true : false;
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Contact detail fetched successfully.', 'cause' => '', 'data' => ['total_items' => $total_contacts, 'is_next_page' => $is_next_page, 'result' => $redis_result]));
        } catch (Exception $e) {
            Log::error("getContactDetailByUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get contact detail by user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} replayToContactByAdmin replayToContactByAdmin
     * @apiName replayToContactByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "contact_id":2, //compulsory
     * "answer":"I'm using the single activity multi fragments with navigation component.how do i hide the bottom navigation bar for some of the fragments?", //compulsory
     * "sender_user_id":3 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Replay has been sent successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function replayToContactByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('answer', 'contact_id', 'sender_user_id'), $request)) != '')
                return $response;

            $subject = isset($request->subject) ? $request->subject : 'Replay by admin';
            $answer = $request->answer;
            $contact_id = $request->contact_id;
            $sender_user_id = $request->sender_user_id;
            $is_active = 1;
            $is_contact = 0;

            $user_detail = DB::select('SELECT email_id FROM user_master WHERE id = ? ', [$sender_user_id]);

            DB::beginTransaction();
            DB::update('UPDATE contact_master SET answer_user_id = ?,answer = ? WHERE id = ? ', [$user_id, $answer, $contact_id]);
            DB::commit();

            if (count($user_detail) > 0) {
                $email_id = $user_detail[0]->email_id;
                if ($email_id) {
                    $template = 'replay2contact';
                    $mail_subject = $subject;
                    $message_body = $answer;
                    $api_name = 'replayToContactByAdmin';
                    $api_description = 'send mail here for replay in user query.';
                    $this->dispatch(new EmailJob(1, $email_id, $mail_subject, $message_body, $template, $api_name, $api_description));
                }
            }
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Replay has been sent successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("replayToContactByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'replay to contact by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getContactDetailByAdmin getContactDetailByAdmin
     * @apiName getContactDetailByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "page":1, //compulsory
     * "item_count":10 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Contact detail fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_items": 3,
     * "is_next_page": false,
     * "result": [
     * {
     * "contact_id": 1,
     * "answer_user_id": "1",
     * "sender_user_id": 2,
     * "email_id": "elsa@grr.la",
     * "phone_no": "7896541230",
     * "subject": "subject",
     * "description": "What is app? Please, send description of app.",
     * "answer": "I'm using the single activity multi fragments with navigation component.how do i hide the bottom navigation bar for some of the fragments?",
     * "create_time": "2019-05-04 14:22:58",
     * "update_time": "2019-05-04 20:56:57"
     * },
     * {
     * "contact_id": 3,
     * "answer_user_id": "",
     * "sender_user_id": 2,
     * "email_id": "elsa@grr.la",
     * "phone_no": "7896541230",
     * "subject": "subject",
     * "description": "What is app? Please, send description of app.",
     * "answer": "",
     * "create_time": "2019-05-04 14:58:47",
     * "update_time": "2019-05-04 20:28:47"
     * },
     * {
     * "contact_id": 2,
     * "answer_user_id": "",
     * "sender_user_id": 2,
     * "email_id": "elsa@grr.la",
     * "phone_no": "7896541230",
     * "subject": "subject",
     * "description": "What is app? Please, send description of app.",
     * "answer": "",
     * "create_time": "2019-05-04 14:58:45",
     * "update_time": "2019-05-04 20:28:45"
     * }
     * ]
     * }
     * }
     */
    public function getContactDetailByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user = Auth::user();
            $this->replay_id = $user->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count'), $request)) != '')
                return $response;

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;

            $total_contacts = DB::select('SELECT COUNT(*) AS total 
                                          FROM contact_master 
                                          WHERE is_active = 1');
            $total_contacts = $total_contacts[0]->total;

            if (!Cache::has("qa:getContactDetailByAdmin:$this->page:$this->item_count")) {
                $result = Cache::rememberforever("getContactDetailByAdmin:$this->page:$this->item_count", function () {

                    return DB::select('SELECT 
                            cm.id AS contact_id,
                            if(cm.answer_user_id!="",cm.answer_user_id,"") AS answer_user_id,
                            cm.sender_user_id,
                            ud.email_id,
                            ud.phone_no,
                            COALESCE(cm.subject,"") AS subject,
                            cm.description,
                            COALESCE(cm.answer,"") AS answer,
                            cm.create_time,
                            cm.update_time
                            FROM contact_master AS cm LEFT JOIN user_detail AS ud ON cm.sender_user_id = ud.user_id
                            WHERE cm.is_active = 1
                            ORDER BY cm.update_time DESC 
                            LIMIT ?,?', [$this->offset, $this->item_count]);

                });
            }
            $redis_result = Cache::get("getContactDetailByAdmin:$this->page:$this->item_count");
            $is_next_page = ($total_contacts > ($this->offset + $this->item_count)) ? true : false;
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Contact detail fetched successfully.', 'cause' => '', 'data' => ['total_items' => $total_contacts, 'is_next_page' => $is_next_page, 'result' => $redis_result]));
        } catch (Exception $e) {
            Log::error("getContactDetailByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get contact detail by user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} deleteContactByAdmin deleteContactByAdmin
     * @apiName deleteContactByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "contact_id":1//compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Contact deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteContactByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('contact_id'), $request)) != '')
                return $response;

            $contact_id = $request->contact_id;

            DB::beginTransaction();
            DB::delete('DELETE
                        FROM
                          contact_master
                        WHERE
                          id = ?', [$contact_id]);
            DB::commit();
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Contact deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("deleteContactByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'deleted contact by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /* =========================================| Multiple admin  |=========================================*/

    /**
     * @api {post}adminRegisterByAdmin adminRegisterByAdmin
     * @apiName adminRegisterByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "first_name":"Herry", //compulsory
     * "last_name":"portal", //compulsory
     * "email_id":"admin@grr.la", //compulsory
     * "password":"12345" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Admin registered successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function adminRegisterByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('email_id', 'first_name', 'last_name', 'password'), $request)) != '')
                return $response;

            $email_id = $request->email_id;
            $first_name = $request->first_name;
            $last_name = $request->last_name;
            $password = Hash::make($request->password);
            $is_active = 0;
            $create_at = date('Y-m-d H:i:s');

            //--------email verification-----
            if (!filter_var($email_id, FILTER_VALIDATE_EMAIL)) {
                $response = Response::json(array('code' => 201, 'message' => 'E-mail is in the wrong format.', 'cause' => '', 'data' => ''));
                return $response;
            }

            $result = DB::select('select um.email_id from user_master as um,user_detail as ud WHERE um.email_id=? and um.id=ud.user_id', [$email_id]);
            if (count($result) == 1) {
                return Response::json(array('code' => 201, 'message' => 'User already exists.', 'cause' => '', 'data' => json_decode("{}")));

            }

            $data = array('email_id' => $email_id,
                'password' => $password,
                'social_uid' => 'admin',
                'is_admin' => 1,
                'is_active' => $is_active,
                'signup_type' => 1,
                'create_time' => $create_at);
            DB::beginTransaction();

            $user_id = DB::table('user_master')->insertGetId($data);
            DB::insert('INSERT INTO user_detail ( user_id, first_name, last_name, email_id,is_active,create_time) 
                    VALUES(?,?,?,?,?,?)',
                [
                    $user_id,
                    $first_name,
                    $last_name,
                    $email_id,
                    $is_active,
                    $create_at
                ]);
            $user_role_data = array(
                'role_id' => Config::get('constant.ROLE_ID_FOR_ADMIN'),
                'user_id' => $user_id,
            );
            DB::table('role_user')->insert($user_role_data);


            DB::commit();
            $response = Response::json(array('code' => 200, 'message' => 'Admin registered successfully.', 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            Log::error("adminRegister : ", ["error" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'admin register.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post}setAdminStatus setAdminStatus
     * @apiName setAdminStatus
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "is_active":0, //compulsory
     * "user_id":3 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Admin deactivated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function setAdminStatus(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('is_active', 'user_id'), $request)) != '')
                return $response;

            $is_active = $request->is_active;
            $user_id = $request->user_id;

            DB::beginTransaction();

            DB::update('UPDATE user_master SET is_active = ? WHERE id = ?', [$is_active, $user_id]);
            DB::update('UPDATE user_detail SET is_active = ? WHERE user_id = ?', [$is_active, $user_id]);
            DB::delete('DELETE FROM user_session WHERE user_id = ?', [$user_id]);

            DB::commit();

            $msg = 'deactivated';
            if ($is_active) {
                $msg = 'actived';
            }
            $response = Response::json(array('code' => 200, 'message' => "Admin $msg successfully.", 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            Log::error("setAdminStatus : ", ["error" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'set status of admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post}updateAdminData updateAdminData
     * @apiName updateAdminData
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "email_id":"anna@gmail.com", //compulsory
     * "first_name":"Anna", //compulsory
     * "last_name":"Lisa", //compulsory
     * "user_id":5 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Admin's data updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateAdminData(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('email_id', 'first_name', 'last_name', 'user_id'), $request)) != '')
                return $response;

            $email_id = $request->email_id;
            $first_name = $request->first_name;
            $last_name = $request->last_name;
            $user_id = $request->user_id;

            $result = DB::select('select um.email_id from user_master as um,user_detail as ud WHERE um.is_active=1 and um.email_id=? and um.id=ud.user_id and um.id != ?', [$email_id, $user_id]);
            if (count($result) == 1) {
                return Response::json(array('code' => 201, 'message' => 'User already exists.', 'cause' => '', 'data' => json_decode("{}")));
            }

            DB::beginTransaction();

            DB::update('UPDATE user_master SET email_id = ? WHERE id = ?', [$email_id, $user_id]);
            DB::update('UPDATE user_detail SET first_name = ?,last_name = ?,email_id = ? WHERE user_id = ?',
                [$first_name, $last_name, $email_id, $user_id]);
            DB::delete('DELETE FROM user_session WHERE user_id = ?', [$user_id]);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => "Admin's data updated successfully.", 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            Log::error("updateAdminData : ", ["error" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update admin data.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post}getAdminData getAdminData
     * @apiName getAdminData
     * @apiGroup Admin
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
     * "message": "Admin's data fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "user_id": 6,
     * "first_name": "Herry",
     * "last_name": "portal",
     * "email_id": "herry@grr.la",
     * "is_active": 1,
     * "create_time": "2019-05-05 06:16:05",
     * "update_time": "2019-05-05 11:46:05"
     * }
     * ]
     * }
     * }
     */
    public function getAdminData()
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (!Cache::has("qa:getAdminData")) {
                $result = Cache::rememberforever("getAdminData", function () {

                    return DB::select('SELECT
                                      um.id as user_id,
                                      COALESCE(ud.first_name,"") AS first_name,
                                      COALESCE(ud.last_name,"") AS last_name,
                                      COALESCE(ud.email_id,"")AS email_id,
                                      COALESCE(um.is_active,0) AS is_active,
                                      um.create_time,
                                      um.update_time
                                      FROM
                                      user_master um, user_detail ud
                                    WHERE
                                      um.id=ud.user_id AND um.is_admin = ? 
                                      ORDER BY ud.update_time DESC ', [1]);


                });
            }
            $redis_result = Cache::get("getAdminData");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => "Admin's data fetched successfully.", 'cause' => '', 'data' => ['result' => $redis_result]));

        } catch (Exception $e) {
            Log::error("getAdminData : ", ["error" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get admin data.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /* =========================================| Debit  |=========================================*/

    /**
     * @api {post}addDebitByAdmin addDebitByAdmin
     * @apiName addDebitByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "expenses_no":1, //compulsory
     * "expenses_name":"expenses_price", //compulsory
     * "expenses_price":200, //compulsory
     * "trans_per":0.10, //compulsory
     * "coins":200, //compulsory
     * "amount":10, //compulsory
     * "invite_amt":10 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Debit added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addDebitByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('expenses_no', 'expenses_name', 'expenses_price', 'trans_per', 'coins', 'amount', 'invite_amt'), $request)) != '')
                return $response;

            $expenses_no = $request->expenses_no;
            $expenses_name = $request->expenses_name;
            $expenses_price = $request->expenses_price;
            $trans_per = $request->trans_per;
            $coins = $request->coins;
            $amount = $request->amount;
            $invite_amt = $request->invite_amt;
            $is_active = 1;
            $create_at = date('Y-m-d H:i:s');

            $is_expenses_no_exist = DB::select('SELECT expenses_no FROM debit_master WHERE expenses_no = ?', [$expenses_no]);

            if (count($is_expenses_no_exist) > 0) {
                return Response::json(array('code' => 201, 'message' => 'This expenses no. already exists.', 'cause' => '', 'data' => json_decode("{}")));
            }
            DB::beginTransaction();

            DB::insert('INSERT INTO debit_master (user_id, expenses_no, expenses_name, expenses_price, trans_per,coins,amount,invite_amt,is_active,create_time) 
                    VALUES(?,?,?,?,?,?,?,?,?,?)',
                [
                    $user_id,
                    $expenses_no,
                    $expenses_name,
                    $expenses_price,
                    $trans_per,
                    $coins,
                    $amount,
                    $invite_amt,
                    $is_active,
                    $create_at
                ]);
            DB::commit();
            $response = Response::json(array('code' => 200, 'message' => 'Debit added successfully.', 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            Log::error("addDebitByAdmin : ", ["error" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add debit.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post}updateDebitByAdmin updateDebitByAdmin
     * @apiName updateDebitByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:}
     * {
     * "debit_id":1, //compulsory
     * "expenses_no":1, //compulsory
     * "expenses_name":"expenses_price", //compulsory
     * "expenses_price":200, //compulsory
     * "trans_per":0.10, //compulsory
     * "coins":200, //compulsory
     * "amount":10, //compulsory
     * "invite_amt":10 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Debit updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateDebitByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('debit_id', 'expenses_no', 'expenses_name', 'expenses_price', 'trans_per', 'coins', 'amount', 'invite_amt'), $request)) != '')
                return $response;

            $debit_id = $request->debit_id;
            $expenses_no = $request->expenses_no;
            $expenses_name = $request->expenses_name;
            $expenses_price = $request->expenses_price;
            $trans_per = $request->trans_per;
            $coins = $request->coins;
            $amount = $request->amount;
            $invite_amt = $request->invite_amt;

            $is_expenses_no_exist = DB::select('SELECT expenses_no FROM debit_master WHERE expenses_no = ? AND id != ?', [$expenses_no, $debit_id]);

            if (count($is_expenses_no_exist) > 0) {
                return Response::json(array('code' => 200, 'message' => 'This expenses no. already exists.', 'cause' => '', 'data' => json_decode("{}")));
            }

            DB::beginTransaction();

            DB::update('UPDATE debit_master 
                        SET user_id = ?, 
                            expenses_no = ?, 
                            expenses_name = ?, 
                            expenses_price = ?, 
                            trans_per = ?,
                            coins = ?,
                            amount = ?,
                            invite_amt = ?
                            WHERE id = ?',
                [
                    $user_id,
                    $expenses_no,
                    $expenses_name,
                    $expenses_price,
                    $trans_per,
                    $coins,
                    $amount,
                    $invite_amt,
                    $debit_id
                ]);
            DB::commit();
            $response = Response::json(array('code' => 200, 'message' => 'Debit updated successfully.', 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            Log::error("updateDebitByAdmin : ", ["error" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update debit.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post}deleteDebitByAdmin deleteDebitByAdmin
     * @apiName deleteDebitByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:}
     * {
     * "debit_id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Debit deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteDebitByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('debit_id'), $request)) != '')
                return $response;

            $debit_id = $request->debit_id;

            DB::beginTransaction();
            DB::delete('DELETE
                        FROM
                          debit_master
                        WHERE
                          id = ?', [$debit_id]);
            DB::commit();
            $response = Response::json(array('code' => 200, 'message' => 'Debit deleted successfully.', 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            Log::error("deleteDebitByAdmin : ", ["error" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete debit.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post}getDebitByAdmin getDebitByAdmin
     * @apiName getDebitByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:}
     * // Give all data in one page by set page = 0 & item_count = 0
     * {
     * "page":0, //compulsory
     * "item_count":0, //compulsory
     * "order_by":"update_time", //optional
     * "order_type":"DESC, //optional
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Debit fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_items": 3,
     * "is_next_page": false,
     * "result": [
     * {
     * "debit_id": 2,
     * "expenses_no": 2,
     * "expenses_name": "expenses price",
     * "expenses_price": 678,
     * "trans_per": 0.1,
     * "coins": 200,
     * "amount": 10,
     * "invite_amt": 10,
     * "create_time": "2019-05-05 07:49:40",
     * "update_time": "2019-05-05 15:18:28"
     * }
     * ]
     * }
     * }
     */
    public function getDebitByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count'), $request)) != '')
                return $response;

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'update_time';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->offset = ($this->page - 1) * $this->item_count;

            $total_items = DB::select('SELECT COUNT(*) AS total FROM debit_master  WHERE is_active = 1');
            $total_item = $total_items[0]->total;

            if ($this->order_by == "debit_id") {
                $this->order_by = "id";
            }

            if (!Cache::has("qa:getDebitByAdmin:$this->page:$this->item_count:$this->order_by:$this->order_type")) {
                $result = Cache::rememberforever("getDebitByAdmin:$this->page:$this->item_count:$this->order_by:$this->order_type", function () {

                    if ($this->page == 0 && $this->item_count == 0) {
                        return DB::select('SELECT 
                            id AS debit_id,
                            expenses_no,
                            expenses_name,
                            expenses_price,
                            trans_per,
                            coins,
                            amount,
                            invite_amt,
                            create_time,
                            update_time
                            FROM debit_master 
                            WHERE is_active = 1
                            ORDER BY ' . $this->order_by . ' ' . $this->order_type);
                    } else {
                        return DB::select('SELECT 
                            id AS debit_id,
                            expenses_no,
                            expenses_name,
                            expenses_price,
                            trans_per,
                            coins,
                            amount,
                            invite_amt,
                            create_time,
                            update_time
                            FROM debit_master 
                            WHERE is_active = 1
                            ORDER BY ' . $this->order_by . ' ' . $this->order_type . '
                             LIMIT ?,?', [$this->offset, $this->item_count]);
                    }
                });
            }
            $redis_result = Cache::get("getDebitByAdmin:$this->page:$this->item_count:$this->order_by:$this->order_type");
            $is_next_page = ($total_item > ($this->offset + $this->item_count)) ? true : false;
            if ($this->page == 0 && $this->item_count == 0) {
                $is_next_page = false;
            }
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Debit fetched successfully.', 'cause' => '', 'data' => ['total_items' => $total_item, 'is_next_page' => $is_next_page, 'result' => $redis_result]));

        } catch (Exception $e) {
            Log::error("getDebitByAdmin : ", ["error" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fetched debit.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /* =========================================| Notify  |=========================================*/

    /**
     * @api {post}addNotifyByAdmin addNotifyByAdmin
     * @apiName addNotifyByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "alert_data":"test", //compulsory
     * "skuname":"CASH_CREDIT" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Notify added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addNotifyByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('alert_data', 'skuname'), $request)) != '')
                return $response;

            $alert_data = $request->alert_data;
            $skuname = $request->skuname;
            $is_active = 1;
            $create_at = date('Y-m-d H:i:s');

            DB::beginTransaction();

            DB::insert('INSERT INTO notify_master (user_id, alert_data, skuname, is_active,create_time) 
                    VALUES(?,?,?,?,?)',
                [
                    $user_id,
                    $alert_data,
                    $skuname,
                    $is_active,
                    $create_at
                ]);
            DB::commit();
            $response = Response::json(array('code' => 200, 'message' => 'Notify added successfully.', 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            Log::error("addNotifyByAdmin : ", ["error" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add notify.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post}updateNotifyByAdmin updateNotifyByAdmin
     * @apiName updateNotifyByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:}
     * {
     * "notify_id":1, //compulsory
     * "alert_data":"test" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Notify updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateNotifyByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('notify_id', 'alert_data'), $request)) != '')
                return $response;

            $notify_id = $request->notify_id;
            $alert_data = $request->alert_data;

            DB::beginTransaction();

            DB::update('UPDATE notify_master 
                        SET user_id = ?, 
                            alert_data = ?
                            WHERE id = ?',
                [
                    $user_id,
                    $alert_data,
                    $notify_id
                ]);
            DB::commit();
            $response = Response::json(array('code' => 200, 'message' => 'Notify updated successfully.', 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            Log::error("updateNotifyByAdmin : ", ["error" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update notify.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post}deleteNotifyByAdmin deleteNotifyByAdmin
     * @apiName deleteNotifyByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:}
     * {
     * "notify_id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Notify deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteNotifyByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('notify_id'), $request)) != '')
                return $response;

            $notify_id = $request->notify_id;

            DB::beginTransaction();
            DB::delete('DELETE
                        FROM
                          notify_master
                        WHERE
                          id = ?', [$notify_id]);
            DB::commit();
            $response = Response::json(array('code' => 200, 'message' => 'Notify deleted successfully.', 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            Log::error("deleteNotifyByAdmin : ", ["error" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete notify.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post}getNotifyByAdmin getNotifyByAdmin
     * @apiName getNotifyByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * // Give all data in one page by set page = 0 & item_count = 0
     * {
     * "page":0, //compulsory
     * "item_count":0, //compulsory
     * "order_by":"update_time", //optional
     * "order_type":"DESC, //optional
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Notify fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_items": 3,
     * "is_next_page": false,
     * "result": [
     * {
     * "notify_id": 5,
     * "user_id": 1,
     * "alert_data": "Push",
     * "skuname": "CASH_CREDIT",
     * "is_active": 1,
     * "create_time": "2019-05-06 18:32:37",
     * "update_time": "2019-05-07 00:02:37"
     * }
     * ]
     * }
     * }
     */
    public function getNotifyByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count'), $request)) != '')
                return $response;

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'update_time';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->offset = ($this->page - 1) * $this->item_count;

            $total_items = DB::select('SELECT COUNT(*) AS total FROM notify_master  WHERE is_active = 1');
            $total_item = $total_items[0]->total;

            if ($this->order_by == "notify_id") {
                $this->order_by = "id";
            }

            if (!Cache::has("qa:getNotifyByAdmin:$this->page:$this->item_count:$this->order_by:$this->order_type")) {
                $result = Cache::rememberforever("getNotifyByAdmin:$this->page:$this->item_count:$this->order_by:$this->order_type", function () {

                    if ($this->page == 0 && $this->item_count == 0) {
                        return DB::select('SELECT 
                            id AS notify_id,
                            user_id,
                            alert_data,
                            skuname,
                            is_active,
                            create_time,
                            update_time
                            FROM notify_master 
                            WHERE is_active = 1
                            ORDER BY ' . $this->order_by . ' ' . $this->order_type);
                    } else {
                        return DB::select('SELECT 
                            id AS notify_id,
                            user_id,
                            alert_data,
                            skuname,
                            is_active,
                            create_time,
                            update_time
                            FROM notify_master 
                            WHERE is_active = 1
                            ORDER BY ' . $this->order_by . ' ' . $this->order_type . '
                             LIMIT ?,?', [$this->offset, $this->item_count]);
                    }
                });
            }
            $redis_result = Cache::get("getNotifyByAdmin:$this->page:$this->item_count:$this->order_by:$this->order_type");
            $is_next_page = ($total_item > ($this->offset + $this->item_count)) ? true : false;
            if ($this->page == 0 && $this->item_count == 0) {
                $is_next_page = false;
            }
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Notify fetched successfully.', 'cause' => '', 'data' => ['total_items' => $total_item, 'is_next_page' => $is_next_page, 'result' => $redis_result]));

        } catch (Exception $e) {
            Log::error("getNotifyByAdmin : ", ["error" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fetched notification.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /* =========================================| CASH_CREDIT  |=========================================*/

    /**
     * @api {post}addKeywordByAdmin addKeywordByAdmin
     * @apiName addKeywordByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "keyword":"KAdsGoogleBottomBannerApp", //compulsory
     * "value":"KAdsGoogleBottomBannerApp",
     * "description":"the test description",
     * "skuname":"CASH_CREDIT"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Keyword added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addKeywordByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('keyword'), $request)) != '')
                return $response;

            $keyword = $request->keyword;
            $value = isset($request->value) ? $request->value : NULL;
            $description = isset($request->description) ? $request->description : NULL;
            $skuname = isset($request->skuname) ? $request->skuname : NULL;
            $is_active = 1;
            $create_at = date('Y-m-d H:i:s');

            DB::beginTransaction();

            DB::insert('INSERT INTO cash_credit_master (user_id, keyword, value, description, skuname, is_active, create_time) 
                    VALUES(?,?,?,?,?,?,?)',
                [
                    $user_id,
                    $keyword,
                    $value,
                    $description,
                    $skuname,
                    $is_active,
                    $create_at
                ]);
            DB::commit();
            $response = Response::json(array('code' => 200, 'message' => 'Keyword added successfully.', 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            Log::error("addKeywordByAdmin : ", ["error" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add keyword.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post}updateKeywordByAdmin updateKeywordByAdmin
     * @apiName updateKeywordByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:}
     * {
     * "keyword_id":1,, //compulsory
     * "keyword":"KAdsGoogleBottomBannerApp", //compulsory
     * "value":"KAdsGoogleBottomBannerApp",
     * "description":"the test description",
     * "skuname":"CASH_CREDIT"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Keyword updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateKeywordByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('keyword_id', 'keyword'), $request)) != '')
                return $response;

            $keyword_id = $request->keyword_id;
            $keyword = isset($request->keyword) ? $request->keyword : NULL;
            $value = isset($request->value) ? $request->value : NULL;
            $description = isset($request->description) ? $request->description : NULL;
            $skuname = isset($request->skuname) ? $request->skuname : NULL;

            DB::beginTransaction();

            DB::update('UPDATE cash_credit_master
                        SET user_id = ?, 
                            keyword = IF(? != "",?,keyword),
                            value = IF(? != "",?,value),
                            description = IF(? != "",?,description),
                            skuname = IF(? != "",?,skuname)
                            WHERE id = ?',
                [
                    $user_id,
                    $keyword,
                    $keyword,
                    $value,
                    $value,
                    $description,
                    $description,
                    $skuname,
                    $skuname,
                    $keyword_id
                ]);
            /* DB::update("UPDATE cash_credit_master
                         SET user_id = $user_id,
                             keyword = $keyword,
                             value = $value,
                             description = $description,
                             skuname = $skuname
                             WHERE id = $keyword_id",
                 [
                     $user_id,
                     $keyword,
                     $value,
                     $description,
                     $skuname,
                     $keyword_id
                 ]);*/

            DB::commit();
            $response = Response::json(array('code' => 200, 'message' => 'Keyword updated successfully.', 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            Log::error("updateKeywordByAdmin : ", ["error" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update keyword.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post}deleteKeywordByAdmin deleteKeywordByAdmin
     * @apiName deleteKeywordByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:}
     * {
     * "keyword_id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Keyword deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteKeywordByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('keyword_id'), $request)) != '')
                return $response;

            $keyword_id = $request->keyword_id;

            DB::beginTransaction();
            DB::delete('DELETE
                        FROM
                          cash_credit_master
                        WHERE
                          id = ?', [$keyword_id]);
            DB::commit();
            $response = Response::json(array('code' => 200, 'message' => 'Keyword deleted successfully.', 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            Log::error("deleteKeywordByAdmin : ", ["error" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete keyword.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post}getKeywordByAdmin getKeywordByAdmin
     * @apiName getKeywordByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:}
     * // Give all data in one page by set page = 0 & item_count = 0
     * {
     * "page":0, //compulsory
     * "item_count":0, //compulsory
     * "order_by":"update_time", //optional
     * "order_type":"DESC, //optional
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Keywords fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_items": 5,
     * "is_next_page": false,
     * "result": [
     * {
     * "keyword_id": 3,
     * "keyword": "KAdsGoogleBottomBannerApp",
     * "value": "KAdsGoogleBottomBannerApp",
     * "description": "the test description",
     * "skuname": "CASH_CREDIT",
     * "is_active": 1,
     * "create_time": "2019-05-06 19:17:15",
     * "update_time": "2019-05-07 00:47:15"
     * },
     * {
     * "keyword_id": 1,
     * "keyword": "KAdsGoogleBottomBannerApp",
     * "value": "",
     * "description": "",
     * "skuname": "",
     * "is_active": 1,
     * "create_time": "2019-05-06 19:10:28",
     * "update_time": "2019-05-07 00:40:28"
     * }
     * ]
     * }
     * }
     */
    public function getKeywordByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count'), $request)) != '')
                return $response;

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'update_time';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->offset = ($this->page - 1) * $this->item_count;

            $total_items = DB::select('SELECT COUNT(*) AS total FROM cash_credit_master  WHERE is_active = 1');
            $total_item = $total_items[0]->total;

            if ($this->order_by == "keyword_id") {
                $this->order_by = "id";
            }

            if (!Cache::has("qa:getKeywordByAdmin:$this->page:$this->item_count:$this->order_by:$this->order_type")) {
                $result = Cache::rememberforever("getKeywordByAdmin:$this->page:$this->item_count:$this->order_by:$this->order_type", function () {

                    if ($this->page == 0 && $this->item_count == 0) {
                        return DB::select('SELECT 
                            id AS keyword_id,
                            COALESCE(keyword,"") AS keyword,
                            COALESCE(value,"") AS value,
                            COALESCE(description,"") AS description,
                            COALESCE(skuname,"") AS skuname,
                            is_active,
                            create_time,
                            update_time
                            FROM cash_credit_master 
                            WHERE is_active = 1
                            ORDER BY ' . $this->order_by . ' ' . $this->order_type);
                    } else {
                        return DB::select('SELECT 
                            id AS keyword_id,
                            COALESCE(keyword,"") AS keyword,
                            COALESCE(value,"") AS value,
                            COALESCE(description,"") AS description,
                            COALESCE(skuname,"") AS skuname,
                            is_active,
                            create_time,
                            update_time
                            FROM cash_credit_master 
                            WHERE is_active = 1
                            ORDER BY ' . $this->order_by . ' ' . $this->order_type . '
                             LIMIT ?,?', [$this->offset, $this->item_count]);
                    }
                });
            }
            $redis_result = Cache::get("getKeywordByAdmin:$this->page:$this->item_count:$this->order_by:$this->order_type");
            $is_next_page = ($total_item > ($this->offset + $this->item_count)) ? true : false;
            if ($this->page == 0 && $this->item_count == 0) {
                $is_next_page = false;
            }
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Keywords fetched successfully.', 'cause' => '', 'data' => ['total_items' => $total_item, 'is_next_page' => $is_next_page, 'result' => $redis_result]));

        } catch (Exception $e) {
            Log::error("getKeywordByAdmin : ", ["error" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fetched keyword.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /* =========================================| Expense  |=========================================*/

    /**
     * @api {post}getExpenseDetailByAdmin getExpenseDetailByAdmin
     * @apiName getExpenseDetailByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "page":1, //compulsory
     * "item_count":5, //compulsory
     * "min_amt":15, //optional
     * "max_amt":20, //optional
     * "skuname":"CASH_CREDIT", //compulsory
     * "status":1 //compulsory 0=Pending,1=Success,2=Return,3=Cancel
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Expense fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_items": 1,
     * "is_next_page": false,
     * "result": [
     * {
     * "expense_id": 2,
     * "req_phone_no": "786543210",
     * "user_id":2,
     * "is_phone_no_verify": 1,
     * "request_coin": "200",
     * "approve_coin": "180",
     * "payment": "10",
     * "pay": "17.82",
     * "status": 1
     * }
     * ]
     * }
     * }
     */
    public function getExpenseDetailByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count', 'skuname', 'status'), $request)) != '')
                return $response;

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'update_time';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->max_amt = isset($request->max_amt) ? $request->max_amt : '';
            $this->min_amt = isset($request->min_amt) ? $request->min_amt : '';
            $this->skuname = isset($request->skuname) ? $request->skuname : '';
            $this->status = isset($request->status) ? $request->status : 0;
            $this->offset = ($this->page - 1) * $this->item_count;

            if ($this->max_amt) {
                $this->max_amt_filter = ' AND pay <= ' . $this->max_amt;
            } else {
                $this->max_amt_filter = '';
            }

            if ($this->min_amt) {
                $this->min_amt_filter = ' AND pay >= ' . $this->min_amt;
            } else {
                $this->min_amt_filter = '';
            }

            if ($this->order_by == "expense_id") {
                $this->order_by = "id";
            }

            $total_items = DB::select('SELECT COUNT(*) AS total FROM expense_master  WHERE is_active = 1 AND skuname = ? ' . $this->max_amt_filter . $this->min_amt_filter . ' AND status = ?',
                [$this->skuname, $this->status]);
            $total_item = $total_items[0]->total;

            $redis_result = DB::select('SELECT id AS expense_id,
                                      COALESCE(req_phone_no,"") AS req_phone_no,
                                      user_id,
                                      is_phone_no_verify,
                                      COALESCE(request_coin,"") AS request_coin,
                                      COALESCE(approve_coin,"") AS approve_coin,
                                      COALESCE(payment,"") AS payment,
                                      COALESCE(pay,"") AS pay,
                                      status
                                      FROM expense_master
                                      WHERE is_active = 1 AND skuname = ? ' . $this->max_amt_filter . $this->min_amt_filter . ' AND status = ?
                                      ORDER BY ' . $this->order_by . ' ' . $this->order_type . '
                                      LIMIT ?,?',
                [$this->skuname, $this->status, $this->offset, $this->item_count]);
            $is_next_page = ($total_item > ($this->offset + $this->item_count)) ? true : false;
            if ($this->page == 0 && $this->item_count == 0) {
                $is_next_page = false;
            }
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Expense fetched successfully.', 'cause' => '', 'data' => ['total_items' => $total_item, 'is_next_page' => $is_next_page, 'result' => $redis_result]));

        } catch (Exception $e) {
            Log::error("getExpenseDetailByAdmin : ", ["error" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fetched expense.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post}exportExpenseDetailByAdmin exportExpenseDetailByAdmin
     * @apiName exportExpenseDetailByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "page":1, //compulsory
     * "item_count":5, //compulsory
     * "min_amt":15, //optional
     * "max_amt":20, //optional
     * "skuname":"CASH_CREDIT", //compulsory
     * "status":1 //compulsory 0=Pending,1=Success,2=Return,3=Cancel
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Expense file successfully.",
     * "cause": "",
     * "data": {
     * "result": "http://localhost/question_answer/image_bucket/exported/expense_master_2019_05_21_18_19_19.csv"
     * }
     * }
     */
    public function exportExpenseDetailByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count', 'skuname', 'status'), $request)) != '')
                return $response;

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'update_time';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->max_amt = isset($request->max_amt) ? $request->max_amt : '';
            $this->min_amt = isset($request->min_amt) ? $request->min_amt : '';
            $this->skuname = isset($request->skuname) ? $request->skuname : '';
            $this->status = isset($request->status) ? $request->status : 0;
            $this->offset = ($this->page - 1) * $this->item_count;

            if ($this->max_amt) {
                $this->max_amt_filter = ' AND pay <= ' . $this->max_amt;
            } else {
                $this->max_amt_filter = '';
            }

            if ($this->min_amt) {
                $this->min_amt_filter = ' AND pay >= ' . $this->min_amt;
            } else {
                $this->min_amt_filter = '';
            }

            if ($this->order_by == "expense_id") {
                $this->order_by = "id";
            }

            if ($this->status == 1) {
                $status = 'Success';
            } elseif ($this->status == 2) {
                $status = 'Return';
            } elseif ($this->status == 3) {
                $status = 'Cancel';
            } else {
                $status = 'Pending';
            }

            $table = DB::select('SELECT em.id AS expense_id,
                                      COALESCE(em.req_phone_no,"") AS req_phone_no,
                                      ud.email_id,
                                      em.is_phone_no_verify,
                                      COALESCE(em.request_coin,"") AS request_coin,
                                      COALESCE(em.approve_coin,"") AS approve_coin,
                                      COALESCE(em.payment,"") AS payment,
                                      COALESCE(em.pay,"") AS pay,
                                      em.status,
                                      em.create_time,
                                      em.update_time
                                      FROM expense_master AS em 
                                      LEFT JOIN user_detail AS ud ON ud.user_id = em.user_id
                                      WHERE em.is_active = 1 AND em.skuname = ? ' . $this->max_amt_filter . $this->min_amt_filter . ' AND em.status = ?
                                      ORDER BY ' . $this->order_by . ' ' . $this->order_type . '
                                      LIMIT ?,?',
                [$this->skuname, $this->status, $this->offset, $this->item_count]);

            $date = date('Y_m_d_H_i_s');

            $filename = "expense_master_$date.csv";
            $dir = '../..' . Config::get('constant.EXPENSE_FILES_DIRECTORY') . $filename;

            $handle = fopen($dir, 'w+');
            fputcsv($handle, array('expense_id', 'req_phone_no', 'email_id', 'is_phone_no_verify', 'request_coin', 'approve_coin', 'payment', 'pay', 'status', 'request_date', 'response_date'));

            foreach ($table as $row) {
                fputcsv($handle, array($row->expense_id, $row->req_phone_no, $row->email_id, $row->is_phone_no_verify, $row->request_coin, $row->approve_coin, $row->payment, $row->pay, $status, $row->create_time, $row->update_time));
            }

            fclose($handle);

            $headers = array(
                'Content-Type' => 'text/csv',
            );

            $file_path = Config::get('constant.EXPENSE_FILES_DIRECTORY_OF_DIGITAL_OCEAN_QA') . $filename;
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Expense file successfully.', 'cause' => '', 'data' => ['result' => $file_path]));

        } catch (Exception $e) {
            Log::error("exportExpenseDetailByAdmin : ", ["error" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'export expense detail by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post}payRSFromByAdmin payRSFromByAdmin
     * @apiName payRSFromByAdmin
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "expense_id":1,//compulsory
     * "request_coin":200,//compulsory
     * "approve_coin":150,//compulsory
     * "status":1//compulsory 0=Pending,1=Success,2=Return,3=Cancel
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Payment paid successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function payRSFromByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user = Auth::user();
            $admin_user_id = $user->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('expense_id', 'status', 'request_coin', 'user_id'), $request)) != '')
                return $response;

            $expense_id = $request->expense_id;
            $status = $request->status;
            $request_coin = $request->request_coin;
            $user_id = $request->user_id;

            $paid_d = DB::select('SELECT * FROM expense_master WHERE id = ?', [$expense_id]);
            $is_paid = $paid_d[0]->is_paid;

            if ($is_paid == 1) {
                return Response::json(array('code' => 201, 'message' => 'This is already paid.', 'cause' => '', 'data' => json_decode("{}")));
            }
            $is_paid = 1;

            if ($status == 1) {
                if (($response = (new VerificationController())->validateRequiredParameter(array('approve_coin'), $request)) != '')
                    return $response;
                $approve_coin = $request->approve_coin;

                $debit_detail = DB::select('SELECT * FROM debit_master WHERE coins <= ? ORDER BY coins DESC LIMIT 1', [$approve_coin]);
                $req_pay = DB::select('SELECT * FROM debit_master WHERE coins <= ? ORDER BY coins DESC LIMIT 1', [$request_coin]);
                if (count($debit_detail) > 0) {
                    $trans_per = $debit_detail[0]->trans_per;
                    $amount = $debit_detail[0]->amount;
                    $coins = $debit_detail[0]->coins;

                    $trans_per_amt = $amount * $trans_per / 100;
                    $pay_amt_by_d = $amount - $trans_per_amt;

                    $pay_amt = $approve_coin * $pay_amt_by_d / $coins;
                }

                if (count($req_pay) > 0) {
                    $req_amount = $req_pay[0]->amount;
                    $req_coins = $req_pay[0]->coins;
                    $amount = $request_coin * $req_amount / $req_coins;
                }
                DB::beginTransaction();
                DB::update('UPDATE expense_master SET status = ?, admin_user_id = ?, approve_coin = ?, payment = ?, pay = ?,is_paid = ? WHERE id = ?',
                    [$status, $admin_user_id, $approve_coin, $amount, $pay_amt, $is_paid, $expense_id]);
                DB::commit();
            }

            if ($status == 2) {

                DB::beginTransaction();
                DB::update('UPDATE expense_master SET status = ?, admin_user_id = ?,is_paid = ? WHERE id = ?',
                    [$status, $user_id, $is_paid, $expense_id]);
                DB::update('UPDATE user_detail SET coins = coins + ? WHERE user_id = ? ', [$request_coin, $user_id]);
                DB::commit();
            }

            if ($status == 3) {
                DB::beginTransaction();
                DB::update('UPDATE expense_master SET status = ?, admin_user_id = ?, is_paid = ? WHERE id = ?',
                    [$status, $user_id, $is_paid, $expense_id]);
                DB::commit();
            }

            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Payment paid successfully.', 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            Log::error("giveCoinsToPayByAdmin : ", ["error" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'give coin to pay.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /*=========================================| PHP Info |=========================================*/

    public function getPhpInfo()
    {
        try {

            return $php_info = phpinfo();

        } catch (Exception $e) {
            Log::error("getPhpInfo : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get php_info.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    public function dbManageForAdmin(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('query'), $request)) != '')
                return $response;

            $query = $request->query;
            return DB::select("$query");

        } catch (Exception $e) {
            Log::error("dbManageForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' db manage for admin', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /*=========================================| testMail |=========================================*/

    public function testMail()
    {
        try {

            $email_id = 'contact2pooja36@gmail.com';
            $template = 'simple';
            $subject = 'Test Mail Job';
            $message_body = 'Welcome to Test Mail Job';
            $api_name = 'verifyProfessional';
            $api_description = 'Send mail after professional verification.';

            $message_body = 'You are ready to start using the ' . Config::get('constant.PROJECT_NAME_FOR_MSG') . ' app! Please enter your verification code in the app. Your verification code is ';
            $api_name = 'signupUser';
            $api_description = 'Send mail for OTP verification.';

            //$this->dispatch(new SMSJob(1, $email_id, 7600394730, $message_body, $api_name, $api_description));

            //$data = array('template' => $template, 'email' => $email_id, 'subject' => $subject, 'message_body' => $message_body);
            /* echo Mail::send($data['template'], $data, function ($message) use ($data) {
                 $message->to($data['email'])->subject($data['subject']);
                 $message->bcc('contact2pooja36@gmail.com')->subject($data['subject']);
             });*/
            Log::info('testMail()');
            $this->dispatch(new EmailJob('john.k@grr.la', $email_id, $subject, $message_body, $template, $api_name, $api_description));

            return "sended";

        } catch (Swift_TransportException $e) {
            echo "error : " . $e->getMessage();
        } catch (Exception $e) {
            echo "error : " . $e->getMessage();
        }
    }

}
