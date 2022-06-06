<?php
Return [

    'ACTIVATION_LINK_PATH' => env('ACTIVATION_LINK_PATH_QA'),
    'PROJECT_NAME' => 'question_answer',
    'APP_HOST_NAME' => 'question_answer',//local
    'EXCEPTION_ERROR' => 'Question answer is unable to ',
    'DATE_FORMAT' => 'Y-m-d H:i:s',
    'PROJECT_NAME_FOR_MSG'=>'Question Answer',

    'PAGINATION_ITEM_COUNT' => '10',
    //'PROJECT_NAME' => basename(dirname(dirname(__DIR__))),
    'RESPONSE_HEADER_CACHE' => 'max-age=2592000',


    'ROLE_FOR_ADMIN' => 'admin',
    'ROLE_FOR_USER' => 'user',
    'ROLE_ID_FOR_USER' => '2',
    'ROLE_ID_FOR_ADMIN' => '1',
    'OTP_EXPIRATION_TIME' => '3',
    'CONTACT_RECEIVER_ID_FOR_USER_CONTACT' => 1,

    'PAGINATION_ITEM_LIMIT' => 50,


    'ADMIN_EMAIL_ID' => 'contact2pooja36@gmail.com',
    'SYSADMIN_EMAIL_ID' => 'contact2pooja36@gmail.com',
    'CLIENT_EMAIL_ID' => 'contact2pooja36@gmail.com',

    'COMPRESSED_IMAGES_DIRECTORY' => '/image_bucket/compressed/',
    'ORIGINAL_IMAGES_DIRECTORY' => '/image_bucket/original/',
    'THUMBNAIL_IMAGES_DIRECTORY' => '/image_bucket/thumbnail/',
    'EXPENSE_FILES_DIRECTORY' => '/image_bucket/exported/',

    /* quality of image compression */
    'QUALITY' => '75',

    /*-----------------------| AWS S3 BUCKET |-------------------------*/
    'STORAGE' => env('STORAGE'),
    'AWS_BUCKET' => env('AWS_BUCKET'),


    /* path to get/store files from s3 */
    'COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_QA' => env('COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_QA'),
    'ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_QA' => env('ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_QA'),
    'THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_QA' => env('THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN_QA'),
    'EXPENSE_FILES_DIRECTORY_OF_DIGITAL_OCEAN_QA' => env('EXPENSE_FILES_DIRECTORY_OF_DIGITAL_OCEAN_QA'),

    'THUMBNAIL_HEIGHT' => 240,
    'THUMBNAIL_WIDTH' => 320,

    /*-----------------------| SMS Credential |-------------------------*/

    'MOBICOMM_USERNAME'=>'PhotivoP',
    'MOBICOMM_API_KEY'=>'19ee3d23f4XX',
    'MOBICOMM_SENDER_ID'=>'INFOSM',
    'MOBICOMM_ACCUSAGE'=>1,

];
