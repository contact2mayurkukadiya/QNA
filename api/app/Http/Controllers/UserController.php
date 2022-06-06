<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Input;
use App\Permission;
use App\Role;
use Response;
use Config;
use DB;
use Log;
use File;
use Cache;
use Auth;
use Exception;
use Mail;


class UserController extends Controller
{

    public $item_count;

    public function __construct()
    {
        $this->item_count = Config::get('constant.PAGINATION_ITEM_LIMIT');
    }

    /*===============================================| User Detail |==================================================*/

    /**
     * @api {post} getUserProfileByUser getUserProfileByUser
     * @apiName getUserProfileByUser
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * }
     * @apiSuccessExample Request-Body:
     * //must be bearer token
     * {
     * "token":"bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjYsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3QvcXVlc3Rpb25fYW5zd2VyL2FwaS9wdWJsaWMvYXBpL2RvTG9naW5Gb3JVc2VyIiwiaWF0IjoxNTYwNTI5NTAxLCJleHAiOjE1NjE3MzkxMDEsIm5iZiI6MTU2MDUyOTUwMSwianRpIjoiRERVR2ptUFpTUXA5blRJeSJ9.d7vXAsg8oJbbsIsp49Rv3mIgh8kgXn-V2RaXZYTZwYs"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Profile fetched successfully.",
     * "cause": "",
     * "data": {
     * "user_detail": {
     * "user_id": 28,
     * "first_name": "elsa",
     * "last_name": "Pater",
     * "email_id": "elsa@grr.la",
     * "gender": 1,
     * "coins": 0,
     * "phone_no": "8160891945",
     * "is_active": 1,
     * "create_time": "2019-04-13 21:47:41",
     * "update_time": "2019-04-14 03:19:24"
     * }
     * }
     * }
     */
    public function getUserProfileByUser()
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user_id = Auth::user()->id;

            $user_profile = (new LoginController())->getUserInfoByUserId($user_id);

            $response = Response::json(array('code' => 200, 'message' => 'Profile fetched successfully.', 'cause' => '', 'data' => ['user_detail' => $user_profile]));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));


        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fetch user profile,' . Config::get('constant.EXCEPTION_ACTION_ERROR'), 'cause' => '', 'data' => json_decode("{}")));
            Log::error('getUserProfileByUser', ['Exception' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
        }
        return $response;
    }

    /**
     * @api {post} addCoinBySomeTaskForUser addCoinBySomeTaskForUser
     * @apiName addCoinBySomeTaskForUser
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "token":"bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjYsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3QvcXVlc3Rpb25fYW5zd2VyL2FwaS9wdWJsaWMvYXBpL2RvTG9naW5Gb3JVc2VyIiwiaWF0IjoxNTYwNTI5NTAxLCJleHAiOjE1NjE3MzkxMDEsIm5iZiI6MTU2MDUyOTUwMSwianRpIjoiRERVR2ptUFpTUXA5blRJeSJ9.d7vXAsg8oJbbsIsp49Rv3mIgh8kgXn-V2RaXZYTZwYs",
     * "coins":20
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Coins added successfully.",
     * "cause": "",
     * "data": {}
     * }
     * }
     */
    public function addCoinBySomeTaskForUser(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user_id = Auth::user()->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('coins'), $request)) != '')
                return $response;

            $coins = $request->coins;

            DB::beginTransaction();
            DB::update('UPDATE user_detail SET coins = coins + ? WHERE user_id = ? ', [$coins, $user_id]);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Coins added successfully.', 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add coins by user' . Config::get('constant.EXCEPTION_ACTION_ERROR'), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("addCoinBySomeTaskForUser : : ", ["error" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    /**
     * @api {post} getRoundDetailByUser getRoundDetailByUser
     * @apiName getRoundDetailByUser
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "token":"bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjYsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3QvcXVlc3Rpb25fYW5zd2VyL2FwaS9wdWJsaWMvYXBpL2RvTG9naW5Gb3JVc2VyIiwiaWF0IjoxNTYwNTI5NTAxLCJleHAiOjE1NjE3MzkxMDEsIm5iZiI6MTU2MDUyOTUwMSwianRpIjoiRERVR2ptUFpTUXA5blRJeSJ9.d7vXAsg8oJbbsIsp49Rv3mIgh8kgXn-V2RaXZYTZwYs",
     * "page":1, //compulsory
     * "item_count":10, //compulsory
     * "order_by":"update_time"
     * "order_type":"asc"
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
     * "is_active": 1,
     * "create_time": "2019-04-03 17:20:46",
     * "update_time": "2019-04-03 23:00:17"
     * }
     * ]
     * }
     * }
     */
    public function getRoundDetailByUser(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count'), $request)) != '')
                return $response;

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;

            $total_round = DB::select('SELECT COUNT(*) AS total FROM round_master WHERE is_active=1');
            $total_round = $total_round[0]->total;

            if (!Cache::has("qa:getRoundDetailByUser:$this->page:$this->item_count")) {
                $result = Cache::rememberforever("getRoundDetailByUser:$this->page:$this->item_count", function () {

                    return DB::select('SELECT 
                            id AS round_id,
                            round_name,
                            entry_coins,
                            coin_per_answer,
                            sec_to_answer,
                            coins_minus,
                            total_question_for_user,
                            time_break,
                            is_active,
                            create_time,
                            update_time
                            FROM round_master
                            LIMIT ?,?', [$this->offset, $this->item_count]);

                });
            }
            $redis_result = Cache::get("getRoundDetailByUser:$this->page:$this->item_count");
            $is_next_page = ($total_round > ($this->offset + $this->item_count)) ? true : false;
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Round fetched successfully.', 'cause' => '', 'data' => ['total_round' => $total_round, 'is_next_page' => $is_next_page, 'result' => $redis_result]));
        } catch (Exception $e) {
            Log::error("getRoundDetailByUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get round detail by user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getQuestionByRoundForUser getQuestionByRoundForUser
     * @apiName getRoundDetailByUser
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "token":"bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjYsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3QvcXVlc3Rpb25fYW5zd2VyL2FwaS9wdWJsaWMvYXBpL2RvTG9naW5Gb3JVc2VyIiwiaWF0IjoxNTYwNTI5NTAxLCJleHAiOjE1NjE3MzkxMDEsIm5iZiI6MTU2MDUyOTUwMSwianRpIjoiRERVR2ptUFpTUXA5blRJeSJ9.d7vXAsg8oJbbsIsp49Rv3mIgh8kgXn-V2RaXZYTZwYs",
     * "round_id":3
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Question fetched successfully.",
     * "cause": "",
     * "data": {
     * "round_detail": [
     * {
     * "round_id": 3,
     * "round_name": "round 3",
     * "entry_coins": 160,
     * "coin_per_answer": 20,
     * "sec_to_answer": 20,
     * "coins_minus": 200,
     * "is_active": 1,
     * "create_time": "2019-04-14 12:29:28",
     * "update_time": "2019-04-14 17:59:28"
     * }
     * ],
     * "questions": [
     * {
     * "question_id": 1,
     * "question": "What is this?",
     * "question_thumbnail_image": "",
     * "question_compressed_image": "",
     * "question_original_image": "",
     * "answer_a": "answer round 1",
     * "answer_b": "answer round 1",
     * "answer_c": "answer round 1",
     * "answer_d": "answer round 1",
     * "real_answer": "answer_a"
     * },
     * {
     * "question_id": 3,
     * "question": "What is this?",
     * "question_thumbnail_image": "http://localhost/question_answer/image_bucket/thumbnail/5cb37b24b8757_question_image_1555266340.jpg",
     * "question_compressed_image": "http://localhost/question_answer/image_bucket/compressed/5cb37b24b8757_question_image_1555266340.jpg",
     * "question_original_image": "http://localhost/question_answer/image_bucket/original/5cb37b24b8757_question_image_1555266340.jpg",
     * "answer_a": "answer round 1",
     * "answer_b": "answer round 1",
     * "answer_c": "answer round 1",
     * "answer_d": "answer round 1",
     * "real_answer": "answer_a"
     * }
     * ]
     * }
     * }
     */
    public function getQuestionByRoundForUser(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user_id = Auth::user()->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('round_id'), $request)) != '')
                return $response;

            $this->round_id = $request->round_id;

            $round_detail = DB::select('SELECT ud.user_id
                                        FROM user_detail ud 
                                        WHERE ud.user_id=? AND ud.coins >= (SELECT entry_coins 
                                                                              FROM round_master 
                                                                              WHERE id = ?)', [$user_id, $this->round_id]);
            if (count($round_detail) > 0) {
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
                                          WHERE qm.round_id=?', [$this->round_id]);

                $round_detail = DB::select('SELECT 
                                                id AS round_id,
                                                round_name,
                                                entry_coins,
                                                coin_per_answer,
                                                sec_to_answer,
                                                coins_minus,
                                                is_active,
                                                create_time,
                                                update_time
                                                FROM round_master WHERE id = ?', [$this->round_id]);
                $response = Response::json(array('code' => 200, 'message' => 'Question fetched successfully.', 'cause' => '', 'data' => ['round_detail' => $round_detail, 'questions' => $question_by_round]));

            } else {
                $response = Response::json(array('code' => 201, 'message' => 'You have no more coins for this round', 'cause' => '', 'data' => json_decode("{}")));
            }

            (new VerificationController())->removeResponseHeadersDetail();
        } catch (Exception $e) {
            Log::error("getQuestionByRoundForUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get question by round for user', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /* =========================================| Contact  |=========================================*/

    public function contactByUser_old(Request $request_body)
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
            $is_contact = 1;
            $receiver_id = Config::get('constant.CONTACT_RECEIVER_ID_FOR_USER_CONTACT');
            $create_at = date('Y-m-d H:i:s');

            DB::beginTransaction();
            DB::insert('INSERT INTO contact_master (sender_user_id,receiver_user_id,subject,description,is_active,create_time) 
                        VALUES(?,?,?,?,?,?)',
                [$user_id, $receiver_id, $subject, $description, $is_active, $create_at]);
            DB::update('UPDATE user_detail SET is_contact = is_contact + ? WHERE user_id = ? ', [$is_contact, $user_id]);
            DB::commit();

            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Contact has been sent successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("contactByUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'contact by user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} contactByUser contactByUser
     * @apiName contactByUser
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "token":"bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjYsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3QvcXVlc3Rpb25fYW5zd2VyL2FwaS9wdWJsaWMvYXBpL2RvTG9naW5Gb3JVc2VyIiwiaWF0IjoxNTYwNTI5NTAxLCJleHAiOjE1NjE3MzkxMDEsIm5iZiI6MTU2MDUyOTUwMSwianRpIjoiRERVR2ptUFpTUXA5blRJeSJ9.d7vXAsg8oJbbsIsp49Rv3mIgh8kgXn-V2RaXZYTZwYs",
     * "subject":"subject",//compulsory
     * "description":"What is app? Please, send description of app."//compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Contact has been sent successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function contactByUser(Request $request_body)
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
            DB::insert('INSERT INTO contact_master (sender_user_id,subject,description,is_active,create_time) 
                        VALUES(?,?,?,?,?)',
                [$user_id, $subject, $description, $is_active, $create_at]);
            DB::commit();

            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Contact has been sent successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("contactByUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'contact by user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getContactDetailByUser getContactDetailByUser
     * @apiName getContactDetailByUser
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "token":"bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjYsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3QvcXVlc3Rpb25fYW5zd2VyL2FwaS9wdWJsaWMvYXBpL2RvTG9naW5Gb3JVc2VyIiwiaWF0IjoxNTYwNTI5NTAxLCJleHAiOjE1NjE3MzkxMDEsIm5iZiI6MTU2MDUyOTUwMSwianRpIjoiRERVR2ptUFpTUXA5blRJeSJ9.d7vXAsg8oJbbsIsp49Rv3mIgh8kgXn-V2RaXZYTZwYs",
     * "page":1, //compulsory
     * "item_count":10 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Contact detail fetched successfully.",
     * "cause": "",
     * "data": {
     * "is_next_page": true,
     * "result": [
     * {
     * "contact_id": 1,
     * "sender_user_id": 2,
     * "answer_user_id": "1",
     * "subject": "subject",
     * "description": "What is app? Please, send description of app.",
     * "answer": "I'm using the single activity multi fragments with navigation component.how do i hide the bottom navigation bar for some of the fragments?",
     * "create_time": "2019-05-04 14:22:58",
     * "update_time": "2019-05-04 20:56:57"
     * },
     * {
     * "contact_id": 3,
     * "sender_user_id": 2,
     * "answer_user_id": "",
     * "subject": "subject",
     * "description": "What is app? Please, send description of app.",
     * "answer": "",
     * "create_time": "2019-05-04 14:58:47",
     * "update_time": "2019-05-04 20:28:47"
     * }
     * ]
     * }
     * }
     */
    public function getContactDetailByUser(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user = Auth::user();
            $this->user_id = $user->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count'), $request)) != '')
                return $response;

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;

            $total_contacts = DB::select('SELECT COUNT(*) AS total FROM contact_master WHERE is_active = 1 AND sender_user_id = ? ', [$this->user_id]);
            $total_contacts = $total_contacts[0]->total;

            if (!Cache::has("qa:getContactDetailByUser:$this->page:$this->item_count:$this->user_id")) {
                $result = Cache::rememberforever("getContactDetailByUser:$this->page:$this->item_count:$this->user_id", function () {

                    return DB::select('SELECT 
                            id AS contact_id,
                            sender_user_id,
                            COALESCE(answer_user_id,"") AS answer_user_id,
                            COALESCE(subject,"") AS subject,
                            description,
                            COALESCE(answer,"") AS answer,
                            create_time,
                            update_time
                            FROM contact_master 
                            WHERE is_active = 1 AND sender_user_id = ? 
                            ORDER BY update_time desc
                             LIMIT ?,?', [$this->user_id, $this->offset, $this->item_count]);
                });
            }
            $redis_result = Cache::get("getContactDetailByUser:$this->page:$this->item_count:$this->user_id");
            $is_next_page = ($total_contacts > ($this->offset + $this->item_count)) ? true : false;
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Contact detail fetched successfully.', 'cause' => '', 'data' => ['is_next_page' => $is_next_page, 'result' => $redis_result]));
        } catch (Exception $e) {
            Log::error("getContactDetailByUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get contact detail by user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /* =========================================| Payment  |=========================================*/

    /**
     * @api {post} requestForCoinsToPay requestForCoinsToPay
     * @apiName requestForCoinsToPay
     * @apiGroup User
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "token":"bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjYsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3QvcXVlc3Rpb25fYW5zd2VyL2FwaS9wdWJsaWMvYXBpL2RvTG9naW5Gb3JVc2VyIiwiaWF0IjoxNTYwNTI5NTAxLCJleHAiOjE1NjE3MzkxMDEsIm5iZiI6MTU2MDUyOTUwMSwianRpIjoiRERVR2ptUFpTUXA5blRJeSJ9.d7vXAsg8oJbbsIsp49Rv3mIgh8kgXn-V2RaXZYTZwYs",
     * "skuname":"CASH_CREDIT", //compulsory
     * "req_phone_no":"1234567890", //compulsory
     * "request_coin":200 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Request has been sent successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function requestForCoinsToPay(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('skuname', 'req_phone_no', 'request_coin'), $request)) != '')
                return $response;

            $skuname = $request->skuname;
            $req_phone_no = $request->req_phone_no;
            $request_coin = $request->request_coin;
            $is_active = 1;
            $status = 0;
            $create_at = date('Y-m-d H:i:s');
            $is_phone_no_verify = 0;

            $phone_no_verify = DB::select('SELECT * FROM user_detail WHERE user_id = ?', [$user_id]);
            if (count($phone_no_verify) > 0) {
                $phone_no = $phone_no_verify[0]->phone_no;
                if ($phone_no == $req_phone_no) {
                    $is_phone_no_verify = 1;
                }
            }
            DB::beginTransaction();
            DB::insert('INSERT INTO expense_master (user_id,skuname,req_phone_no,is_phone_no_verify,request_coin,status,is_active,create_time) 
                        VALUES(?,?,?,?,?,?,?,?)',
                [$user_id, $skuname, $req_phone_no, $is_phone_no_verify, $request_coin, $status, $is_active, $create_at]);
            DB::commit();

            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Request has been sent successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
            Log::error("requestForCoinsToPay : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'request by user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

}
