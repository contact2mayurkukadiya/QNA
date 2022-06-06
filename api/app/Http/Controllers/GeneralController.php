<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Config;
use DB;
use Log;
use File;
use Cache;
use Auth;
use Exception;
use Mail;


class GeneralController extends Controller
{

    public $item_count;

    public function __construct()
    {
        $this->item_count = Config::get('constant.PAGINATION_ITEM_LIMIT');
    }

    /*===============================================| FAQ for user |==================================================*/

    /**
     * @api {post} getFAQByUser getFAQByUser
     * @apiName getFAQByUser
     * @apiGroup General
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * }
     * @apiSuccessExample Request-Body:
     * {
     * //Give all data in one page by set page = 0 & item_count = 0
     * "page":1, //compulsory
     * "item_count":10 //compulsory
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
    public function getFAQByUser(Request $request_body)
    {
        try {
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

            $total_round = DB::select('SELECT COUNT(*) AS total FROM faq_master WHERE is_active = 1');
            $total_round = $total_round[0]->total;

            if (!Cache::has("qa:getFAQByUser:$this->page:$this->item_count:$this->order_by:$this->order_type")) {
                $result = Cache::rememberforever("getFAQByUser:$this->page:$this->item_count:$this->order_by:$this->order_type", function () {

                    if($this->page == 0 && $this->item_count == 0){
                        return DB::select('SELECT 
                            id AS faq_id,
                            faq_question,
                            faq_answer,
                            is_active,
                            create_time,
                            update_time
                            FROM faq_master 
                            WHERE is_active = 1
                            ORDER BY update_time DESC');
                    }else{
                        return DB::select('SELECT 
                            id AS faq_id,
                            faq_question,
                            faq_answer,
                            is_active,
                            create_time,
                            update_time
                            FROM faq_master 
                            WHERE is_active = 1
                            ORDER BY ' . $this->order_by . ' ' . $this->order_type . '
                             LIMIT ?,?', [$this->offset, $this->item_count]);
                    }
                });
            }
            $redis_result = Cache::get("getFAQByUser:$this->page:$this->item_count:$this->order_by:$this->order_type");
            $is_next_page = ($total_round > ($this->offset + $this->item_count)) ? true : false;
            if($this->page == 0 && $this->item_count == 0) {
                $is_next_page =false;
            }
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'FAQ fetched successfully.', 'cause' => '', 'data' => ['total_round' => $total_round, 'is_next_page' => $is_next_page, 'result' => $redis_result]));
        } catch (Exception $e) {
            Log::error("getFAQByUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get faq by user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /**
     * @api {post} getTermsNConditionsByUser getTermsNConditionsByUser
     * @apiName getTermsNConditionsByUser
     * @apiGroup General
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * }
     * @apiSuccessExample Request-Body:
     * {
     * //Give all data in one page by set page = 0 & item_count = 0
     * "page":1, //compulsory
     * "item_count":10 //compulsory
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
    public function getTermsNConditionsByUser(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count'), $request)) != '')
                return $response;

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'update_time';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->offset = ($this->page - 1) * $this->item_count;

            $total_items = DB::select('SELECT COUNT(*) AS total FROM terms_n_conditions  WHERE is_active = 1');
            $total_item = $total_items[0]->total;

            if ($this->order_by == "term_n_condition_id") {
                $this->order_by = "id";
            }

            if (!Cache::has("qa:getTermsNConditionsByUser:$this->page:$this->item_count:$this->order_by:$this->order_type")) {
                $result = Cache::rememberforever("getTermsNConditionsByUser:$this->page:$this->item_count:$this->order_by:$this->order_type", function () {

                    if($this->page == 0 && $this->item_count == 0){
                        return DB::select('SELECT 
                            id AS term_n_condition_id,
                            subject,
                            description,
                            is_active,
                            create_time,
                            update_time
                            FROM terms_n_conditions 
                            WHERE is_active = 1
                            ORDER BY update_time DESC', [$this->offset, $this->item_count]);
                    }else{
                        return DB::select('SELECT 
                            id AS term_n_condition_id,
                            subject,
                            description,
                            is_active,
                            create_time,
                            update_time
                            FROM terms_n_conditions 
                            WHERE is_active = 1
                            ORDER BY ' . $this->order_by . ' ' . $this->order_type . '
                             LIMIT ?,?', [$this->offset, $this->item_count]);
                    }
                });
            }
            $redis_result = Cache::get("getTermsNConditionsByUser:$this->page:$this->item_count:$this->order_by:$this->order_type");
            $is_next_page = ($total_item > ($this->offset + $this->item_count)) ? true : false;
            if($this->page == 0 && $this->item_count == 0){
                $is_next_page = false;
            }
            (new VerificationController())->removeResponseHeadersDetail();
            $response = Response::json(array('code' => 200, 'message' => 'Terms and conditions fetched successfully.', 'cause' => '', 'data' => ['total_items' => $total_item, 'is_next_page' => $is_next_page, 'result' => $redis_result]));
        } catch (Exception $e) {
            Log::error("getTermsNConditionsByUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get terms and conditions by user.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

}
