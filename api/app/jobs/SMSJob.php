<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\Job;
use Mail;
use Log;
use DB;
use Config;
use GuzzleHttp\Client;

class SMSJob extends Job implements ShouldQueue
{

    use InteractsWithQueue, SerializesModels;


    protected $user_id;

    protected $email_id;

    protected $message_body;

    protected $phone_no;

    protected $api_name;

    protected $api_description;

    public function __construct($user_id, $email_id,$phone_no, $message_body, $api_name, $api_description)
    {
        $this->user_id = $user_id;
        $this->email_id = $email_id;
        $this->phone_no = $phone_no;
        $this->message_body = $message_body;
        $this->api_name = $api_name;
        $this->api_description = $api_description;
    }
    public function handle()
    {
        $user_id = $this->user_id;
        $email_id = $this->email_id;
        $phone_no = $this->phone_no;
        $message_body = $this->message_body;
        $api_name = $this->api_name;
        $api_description = $this->api_description;

        $user_name = Config::get('constant.MOBICOMM_USERNAME');
        $api_key = Config::get('constant.MOBICOMM_API_KEY');
        $senderid = Config::get('constant.MOBICOMM_SENDER_ID');
        $accusage = Config::get('constant.MOBICOMM_ACCUSAGE');
        $url = "http://mobicomm.dove-sms.com//submitsms.jsp?user=" .
            $user_name .
            "&key=" . $api_key .
            "&mobile=" . $phone_no .
            "&message=" . $message_body .
            "&senderid=" . $senderid .
            "&accusage=" . $accusage;
        Log::info('url', [$url]);
        $client = new Client();
        try {
            $response = $client->request('get', $url);
        } catch (Exception $e) {
            Log::error("otp not send : ", ["error" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }


    public function failed()
    {
        Log::error('SMSJob.php failed()',['failed_job_id'=>1]);
        $user_id = $this->user_id;
        $api_name = $this->api_name;
        $api_description = $this->api_description;
        $job_name = 'SMSJob';

        // get failed job max id
        $failed_job_id_result = DB::select('SELECT max(id) as max_id FROM failed_jobs');
        log::info($failed_job_id_result);
        if (count($failed_job_id_result) > 0) {
            $failed_job_id = $failed_job_id_result[0]->max_id == NULL ? 0 : $failed_job_id_result[0]->max_id;
            log::info($failed_job_id_result);
            // add failed job detail
            DB::beginTransaction();
            DB::insert('INSERT INTO failed_jobs_detail
                        (failed_job_id, user_id, api_name, api_description, job_name)
                        VALUES (?,?,?,?,?)',
                [$failed_job_id, $user_id, $api_name, $api_description, $job_name]);
            DB::commit();

            // send email to admin
            $template = 'simple';
            $email_id = Config::get('constant.ADMIN_EMAIL_ID'); //'jaimisha.optimumbrew@gmail.com';
            $subject = 'Email failed';
            $message_body = 'Failed Job Id = ' . $failed_job_id . '<br>' . 'User Id = ' . $user_id . '<br>' . 'API Name = ' . $api_name . '<br>' . 'API Description = ' . $api_description;
            $data = array('template' => $template, 'email' => $email_id, 'subject' => $subject, 'message_body' => $message_body);
            Mail::send($data['template'], $data, function ($message) use ($data) {
                $message->to($data['email'])->subject($data['subject']);
                //$message->bcc('contact2pooja36@gmail.com')->subject($data['subject']);
            });
        }
        // log failed job
        Log::error('SMSJob.php failed()',['failed_job_id'=>$failed_job_id,'user_id'=>$user_id,'api_name'=>$api_name,'api_description'=> $api_description]);
    }
}
