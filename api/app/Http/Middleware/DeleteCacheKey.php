<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;
use Log;
use Illuminate\Http\Request;
use Mockery\CountValidator\Exception;


class DeleteCacheKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $api = $request->getPathInfo();
        IF ($api != "/api/logs/admin@gmail.com/demo@123") {
            //Log::info("apicall :",[$api]);
        }
        return $next($request);
    }

    public function terminate(Request $request)
    {
        try {
            $api = $request->getPathInfo();

            //Round
            if ($api == '/api/addRoundDetailByAdmin' or $api == '/api/updateRoundDetailByAdmin' or $api == '/api/deleteRoundDetailByAdmin') {

                //get Round API
                $keys = Redis::keys('qa:getRoundDetailByAdmin*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                //get Round API User
                $keys = Redis::keys('qa:getRoundDetailByUser*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
            }

            //Question
            if ($api == '/api/addQuestionAnswerByAdmin' or $api == '/api/updateQuestionAnswerByAdmin' or $api == '/api/deleteQuestionAnswerByAdmin' or $api == '/api/updateRoundDetailByAdmin' or $api == '/api/deleteRoundDetailByAdmin' or $api == '/api/addQuestionAnswerFromExcelByAdmin') {

                //get question API
                $keys = Redis::keys('qa:getQuestionAnswerByAdmin*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                $keys = Redis::keys('qa:getQuestionAnswerFromRoundByAdmin*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
            }

            //Multiple admin
            if ($api == '/api/adminRegisterByAdmin' or $api == '/api/setAdminStatus' or $api == '/api/updateAdminData') {

                //get question API
                $keys = Redis::keys('qa:getAdminData*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
            }

            //Debit module
            if ($api == '/api/addDebitByAdmin' OR $api == '/api/updateDebitByAdmin' OR $api == '/api/deleteDebitByAdmin') {

                $keys = Redis::keys('qa:getDebitByAdmin*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
            }

            //FAQ
            if ($api == '/api/addFAQByAdmin' or $api == '/api/updateFAQByAdmin' or $api == '/api/deleteFAQByAdmin' or $api == '/api/setStatusOfFAQByAdmin') {

                //get question API
                $keys = Redis::keys('qa:getFAQByAdmin*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                $keys = Redis::keys('qa:getFAQByUser*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
            }

            //Contact us
            if ($api == '/api/contactByUser' OR $api == '/api/deleteContactByAdmin' OR $api == '/api/replayToContactByAdmin') {

                $keys = Redis::keys('qa:getContactDetailByUser*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                $keys = Redis::keys('qa:getContactDetailByAdmin*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
            }

            //Terms and Conditions
            if ($api == '/api/addTermsNConditionsByAdmin' OR $api == '/api/updateTermsNConditionsByAdmin' OR $api == '/api/deleteTermsNConditionsByAdmin' OR $api == '/api/setStatusOfTermsNConditionsByAdmin') {

                $keys = Redis::keys('qa:getTermsNConditionsByAdmin*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

                $keys = Redis::keys('qa:getTermsNConditionsByUser*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }

            }

            //NotifyByAdmin
            if ($api == '/api/addNotifyByAdmin' OR $api == '/api/updateNotifyByAdmin' OR $api == '/api/deleteNotifyByAdmin') {

                $keys = Redis::keys('qa:getNotifyByAdmin*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
            }

            //KeywordByAdmin
            if ($api == '/api/addKeywordByAdmin' OR $api == '/api/updateKeywordByAdmin' OR $api == '/api/deleteKeywordByAdmin') {

                $keys = Redis::keys('qa:getKeywordByAdmin*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
            }

            //requestForCoinsToPay
            /*if ($api == '/api/requestForCoinsToPay' OR $api == 'payRSFromByAdmin') {

                $keys = Redis::keys('qa:getExpenseDetailByAdmin*');
                foreach ($keys as $key) {
                    Redis::del($key);
                }
            }*/


        } catch (Exception $e) {
            Log::error("DeleteCacheKey Middleware : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Delete Cache Key.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
    }
}
