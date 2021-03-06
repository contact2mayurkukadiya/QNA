define({ "api": [
  {
    "type": "post",
    "url": "addFAQByAdmin",
    "title": "addFAQByAdmin",
    "name": "addFAQByAdmin",
    "group": "Admin_FAQ",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"faq_question\":\"Question ?\",//compulsory\n\"faq_answer\":\"Answer,//compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"FAQ added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin_FAQ"
  },
  {
    "type": "post",
    "url": "deleteFAQByAdmin",
    "title": "deleteFAQByAdmin",
    "name": "deleteFAQByAdmin",
    "group": "Admin_FAQ",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"faq_id\":1//compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"FAQ deleted successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin_FAQ"
  },
  {
    "type": "post",
    "url": "getFAQByAdmin",
    "title": "getFAQByAdmin",
    "name": "getFAQByAdmin",
    "group": "Admin_FAQ",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"page\":1, //compulsory\n\"item_count\":10, //compulsory\n\"order_by\":\"update_time\",\n\"order_type\":\"DESC\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"FAQ fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_round\": 2,\n\"is_next_page\": false,\n\"result\": [\n{\n\"faq_id\": 2,\n\"faq_question\": \"Can I use?\",\n\"faq_answer\": \"You can copy, modify, distribute, and use the images, even for commercial purposes, all without asking for permission or giving credits to the artist. However, depicted content may still be protected by trademarks, publicity or privacy rights\",\n\"is_active\": 1,\n\"create_time\": \"2019-04-26 16:12:12\",\n\"update_time\": \"2019-04-26 21:44:17\"\n},\n{\n\"faq_id\": 1,\n\"faq_question\": \"Can I use your images?\",\n\"faq_answer\": \"You can copy, modify, distribute, and use the images, even for commercial purposes, all without asking for permission or giving credits to the artist. However, depicted content may still be protected by trademarks, publicity or privacy rights\",\n\"is_active\": 1,\n\"create_time\": \"2019-04-26 16:12:02\",\n\"update_time\": \"2019-04-26 21:42:02\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin_FAQ"
  },
  {
    "type": "post",
    "url": "setStatusOfFAQByAdmin",
    "title": "setStatusOfFAQByAdmin",
    "name": "setStatusOfFAQByAdmin",
    "group": "Admin_FAQ",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"is_active\":1//compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Set status successfully of FAQ.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin_FAQ"
  },
  {
    "type": "post",
    "url": "updateFAQByAdmin",
    "title": "updateFAQByAdmin",
    "name": "updateFAQByAdmin",
    "group": "Admin_FAQ",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"faq_id\":2,//compulsory\n\"faq_question\":\"Question ?\",//compulsory\n\"faq_answer\":\"Answer\"//compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"FAQ updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin_FAQ"
  },
  {
    "type": "post",
    "url": "doLoginForAdmin",
    "title": "doLoginForAdmin",
    "name": "doLoginForAdmin",
    "group": "Admin_Login",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"email_id\":\"admin@gmail.com\",\n\"password\":\"demo@123\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Login successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"token\": \"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3QvcXVlc3Rpb25fYW5zd2VyL2FwaS9wdWJsaWMvYXBpL2RvTG9naW5Gb3JBZG1pbiIsImlhdCI6MTU1NDgzMDgwMSwiZXhwIjoxNTU2MDQwNDAxLCJuYmYiOjE1NTQ4MzA4MDEsImp0aSI6IjkyTzdGSkNINjFveUtNZkkifQ.MXxcV3tN2w-yLj-g70KVjF5xls_bIIEpYAAtZe37eM8\",\n\"user_detail\": {\n\"user_id\": 1,\n\"first_name\": \"admin\",\n\"last_name\": \"admin\",\n\"email_id\": \"admin@gmail.com\",\n\"gender\": 0,\n\"coins\": 0,\n\"is_active\": 1,\n\"create_time\": \"2019-04-03 17:05:58\",\n\"update_time\": \"2019-04-03 22:35:58\"\n}\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/LoginController.php",
    "groupTitle": "Admin_Login"
  },
  {
    "type": "post",
    "url": "addQuestionAnswerByAdmin",
    "title": "addQuestionAnswerByAdmin",
    "name": "addQuestionAnswerByAdmin",
    "group": "Admin_Quesion_Answer",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "request_data:{\n\"round_id\":1,//compulsory\n\"question\":\"What is this?\",//compulsory\n\"answer_a\":\"Answer a\",//compulsory\n\"answer_b\":\"Answer b\",//compulsory\n\"answer_c\":\"Answer c\",//compulsory\n\"answer_d\":\"Answer d\",//compulsory\n\"real_answer\":\"answer_a\"//compulsory\n}\nfile:i.jpg //if question with image",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Question added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin_Quesion_Answer"
  },
  {
    "type": "post",
    "url": "addQuestionAnswerFromExcelByAdmin",
    "title": "addQuestionAnswerFromExcelByAdmin",
    "name": "addQuestionAnswerFromExcelByAdmin",
    "group": "Admin_Quesion_Answer",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "file:i.jpg //Currently use csv file",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Question added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin_Quesion_Answer"
  },
  {
    "type": "post",
    "url": "deleteQuestionAnswerByAdmin",
    "title": "deleteQuestionAnswerByAdmin",
    "name": "deleteQuestionAnswerByAdmin",
    "group": "Admin_Quesion_Answer",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"question_id\":1//compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Question deleted successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin_Quesion_Answer"
  },
  {
    "type": "post",
    "url": "getQuestionAnswerByAdmin",
    "title": "getQuestionAnswerByAdmin",
    "name": "getQuestionAnswerByAdmin",
    "group": "Admin_Quesion_Answer",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"page\":1,\n\"item_count\":10,\n\"order_by\":\"round_name\",\n\"order_type\":\"asc\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Question fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_question\": 7,\n\"is_next_page\": false,\n\"result\": [\n{\n\"question_id\": 6,\n\"round_name\": \"round 4\",\n\"question_thumbnail_image\": \"\",\n\"question_compressed_image\": \"\",\n\"question_original_image\": \"\",\n\"question\": \"What is this?\",\n\"answer_a\": \"answer round 1\",\n\"answer_b\": \"answer round 1\",\n\"answer_c\": \"answer round 1\",\n\"answer_d\": \"answer round 1\",\n\"real_answer\": \"answer_a\",\n\"update_time\": \"2019-04-05 23:22:56\"\n},\n{\n\"question_id\": 7,\n\"round_name\": \"round 4\",\n\"question_thumbnail_image\": \"http://localhost/question_answer/image_bucket/thumbnail/5ca7967d8a0d0_question_image_1554486909.jpg\",\n\"question_compressed_image\": \"http://localhost/question_answer/image_bucket/compressed/5ca7967d8a0d0_question_image_1554486909.jpg\",\n\"question_original_image\": \"http://localhost/question_answer/image_bucket/original/5ca7967d8a0d0_question_image_1554486909.jpg\",\n\"question\": \"What is this?\",\n\"answer_a\": \"answer round 1\",\n\"answer_b\": \"answer round 1\",\n\"answer_c\": \"answer round 1\",\n\"answer_d\": \"answer round 1\",\n\"real_answer\": \"answer_c\",\n\"update_time\": \"2019-04-05 23:25:10\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin_Quesion_Answer"
  },
  {
    "type": "post",
    "url": "getQuestionAnswerFromRoundByAdmin",
    "title": "getQuestionAnswerFromRoundByAdmin",
    "name": "getQuestionAnswerFromRoundByAdmin",
    "group": "Admin_Quesion_Answer",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"round_id\":3,\n\"page\":1,\n\"item_count\":2,\n\"order_by\":\"question\",\n\"order_type\":\"asc\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Question fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_question\": 3,\n\"is_next_page\": true,\n\"result\": [\n{\n\"round_id\": 1,\n\"round_name\": \"round 3\",\n\"entry_coins\": 200,\n\"coin_per_answer\": 20,\n\"sec_to_answer\": 20,\n\"coins_minus\": 200,\n\"is_active\": 1,\n\"total_question\": 2,\n\"questions_detail\": [\n{\n\"question_id\": 1,\n\"question\": \"What is this?\",\n\"question_thumbnail_image\": \"http://localhost/question_answer/image_bucket/thumbnail/5ca6304872446_question_image_1554395208.jpg\",\n\"question_compressed_image\": \"http://localhost/question_answer/image_bucket/compressed/5ca6304872446_question_image_1554395208.jpg\",\n\"question_original_image\": \"http://localhost/question_answer/image_bucket/original/5ca6304872446_question_image_1554395208.jpg\",\n\"answer_a\": \"Answer a\",\n\"answer_b\": \"Answer b\",\n\"answer_c\": \"Answer c update\",\n\"answer_d\": \"Answer d\",\n\"real_answer\": \"b\"\n},\n{\n\"question_id\": 2,\n\"question\": \"What is this?\",\n\"question_thumbnail_image\": \"\",\n\"question_compressed_image\": \"\",\n\"question_original_image\": \"\",\n\"answer_a\": \"Answer a cat 1 jwfh @#$%^\",\n\"answer_b\": \"Answer a cat 1 jwfh @#$%^\",\n\"answer_c\": \"Answer a cat 1 jwfh @#$%^\",\n\"answer_d\": \"Answer a cat 1 jwfh @#$%^\",\n\"real_answer\": \"a\"\n}\n]\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin_Quesion_Answer"
  },
  {
    "type": "post",
    "url": "updateQuestionAnswerByAdmin",
    "title": "updateQuestionAnswerByAdmin",
    "name": "updateQuestionAnswerByAdmin",
    "group": "Admin_Quesion_Answer",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"question_id\":1,//compulsory\n\"round_id\":1,//compulsory\n\"question\":\"What is this?\",//compulsory\n\"answer_a\":\"Answer a\",//compulsory\n\"answer_b\":\"Answer b\",//compulsory\n\"answer_c\":\"Answer c update\",//compulsory\n\"answer_d\":\"Answer d\",//compulsory\n\"real_answer\":\"answer_b\"//compulsory\n}\nfile:1.jpg //if question with image",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Question updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin_Quesion_Answer"
  },
  {
    "type": "post",
    "url": "addRoundDetailByAdmin",
    "title": "addRoundDetailByAdmin",
    "name": "addRoundDetailByAdmin",
    "group": "Admin_round",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"round_name\":\"round 3\",//compulsory\n\"entry_coins\":150,//compulsory\n\"coin_per_answer\":20,//compulsory\n\"sec_to_answer\":20,//compulsory\n\"coins_minus\":200//compulsory\n\"total_question_for_user\":20, //compulsory\n\"time_break\":2 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Round added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin_round"
  },
  {
    "type": "post",
    "url": "deleteRoundDetailByAdmin",
    "title": "deleteRoundDetailByAdmin",
    "name": "deleteRoundDetailByAdmin",
    "group": "Admin_round",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"round_id\":1//compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Round deleted successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin_round"
  },
  {
    "type": "post",
    "url": "getRoundDetailByAdmin",
    "title": "getRoundDetailByAdmin",
    "name": "getRoundDetailByAdmin",
    "group": "Admin_round",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"page\":1, //compulsory\n\"item_count\":10, //compulsory\n\"order_by\":\"update_time\"\n\"order_type\":\"asc\"\n\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Round fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"result\": [\n{\n\"round_id\": 1,\n\"round_name\": \"round 3\",\n\"entry_coins\": 200,\n\"coin_per_answer\": 20,\n\"sec_to_answer\": 20,\n\"coins_minus\": 200,\n\"time_break\": 2,\n\"total_question_for_user\": 20,\n\"is_active\": 1,\n\"create_time\": \"2019-04-03 17:20:46\",\n\"update_time\": \"2019-04-03 23:00:17\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin_round"
  },
  {
    "type": "post",
    "url": "updateRoundDetailByAdmin",
    "title": "updateRoundDetailByAdmin",
    "name": "updateRoundDetailByAdmin",
    "group": "Admin_round",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"round_id\":1,//compulsory\n\"round_name\":\"round 3\",//compulsory\n\"entry_coins\":150,//compulsory\n\"coin_per_answer\":20,//compulsory\n\"sec_to_answer\":20,//compulsory\n\"coins_minus\":200//compulsory\n\"total_question_for_user\":20, //compulsory\n\"time_break\":2 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Round updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin_round"
  },
  {
    "type": "post",
    "url": "getAllUserForAdmin",
    "title": "getAllUserForAdmin",
    "name": "getAllUserForAdmin",
    "group": "Admin_user",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"page\":1, //compulsory\n\"item_count\":10, //compulsory\n\"order_by\":\"update_time\"\n\"order_type\":\"desc\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"All user fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_record\": 1,\n\"is_next_page\": false,\n\"user_detail\": [\n{\n\"user_id\": 2,\n\"first_name\": \"user\",\n\"last_name\": \"user\",\n\"email_id\": \"user@gmail.com\",\n\"gender\": \"1\",\n\"phone_no\": \"7896541230\",\n\"coins\": 0,\n\"signup_type\": 0,\n\"is_active\": 1,\n\"is_contact\": 1,\n\"create_time\": \"2019-04-11 00:37:18\",\n\"update_time\": \"2019-04-11 00:37:18\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin_user"
  },
  {
    "type": "post",
    "url": "searchUserForAdmin",
    "title": "searchUserForAdmin",
    "name": "searchUserForAdmin",
    "group": "Admin_user",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"search_type\":\"phone_no\", //compulsory field : first_name,last_name,email_id,phone_no\n\"search_query\":\"54321\" //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Users fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"user_detail\": [\n{\n\"user_id\": 3,\n\"first_name\": \"jesal\",\n\"last_name\": \"Petel\",\n\"email_id\": \"jesal@grr.la\",\n\"gender\": \"1\",\n\"phone_no\": \"786543210\",\n\"coins\": 0,\n\"signup_type\": 1,\n\"is_active\": 1,\n\"is_contact\": 0,\n\"create_time\": \"2019-05-05 16:48:46\",\n\"update_time\": \"2019-05-05 22:18:46\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin_user"
  },
  {
    "type": "post",
    "url": "addDebitByAdmin",
    "title": "addDebitByAdmin",
    "name": "addDebitByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"expenses_no\":1, //compulsory\n\"expenses_name\":\"expenses_price\", //compulsory\n\"expenses_price\":200, //compulsory\n\"trans_per\":0.10, //compulsory\n\"coins\":200, //compulsory\n\"amount\":10, //compulsory\n\"invite_amt\":10 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Debit added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "addKeywordByAdmin",
    "title": "addKeywordByAdmin",
    "name": "addKeywordByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"keyword\":\"KAdsGoogleBottomBannerApp\", //compulsory\n\"value\":\"KAdsGoogleBottomBannerApp\",\n\"description\":\"the test description\",\n\"skuname\":\"CASH_CREDIT\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Keyword added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "addNotifyByAdmin",
    "title": "addNotifyByAdmin",
    "name": "addNotifyByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"alert_data\":\"test\", //compulsory\n\"skuname\":\"CASH_CREDIT\" //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Notify added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "addTermsNConditionsByAdmin",
    "title": "addTermsNConditionsByAdmin",
    "name": "addTermsNConditionsByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"subject\":\"Use of the Service\", //compulsory\n\"description\":\"In connection with your use of the Service you will not engage in or use any data mining, robots, scraping or similar data gathering or extraction methods. The technology and software underlying the Service or distributed in connection therewith is the property of Pixabay and our licensors, affiliates and partners and you are granted no license in respect of that Software.\" //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Term and condition added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "adminRegisterByAdmin",
    "title": "adminRegisterByAdmin",
    "name": "adminRegisterByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"first_name\":\"Herry\", //compulsory\n\"last_name\":\"portal\", //compulsory\n\"email_id\":\"admin@grr.la\", //compulsory\n\"password\":\"12345\" //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Admin registered successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "clearRedisCache",
    "title": "clearRedisCache",
    "name": "clearRedisCache",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Redis Keys Deleted Successfully.\",\n\"cause\": \"\",\n\"data\": \"{}\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "deleteContactByAdmin",
    "title": "deleteContactByAdmin",
    "name": "deleteContactByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"contact_id\":1//compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Contact deleted successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "deleteDebitByAdmin",
    "title": "deleteDebitByAdmin",
    "name": "deleteDebitByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:}",
          "content": "{\n\"debit_id\":1 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Debit deleted successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "deleteKeywordByAdmin",
    "title": "deleteKeywordByAdmin",
    "name": "deleteKeywordByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:}",
          "content": "{\n\"keyword_id\":1 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Keyword deleted successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "deleteNotifyByAdmin",
    "title": "deleteNotifyByAdmin",
    "name": "deleteNotifyByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:}",
          "content": "{\n\"notify_id\":1 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Notify deleted successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "deleteRedisKeys",
    "title": "deleteRedisKeys",
    "name": "deleteRedisKeys",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"keys_list\": [\n{\n\"key\": \"sr:getAllFriendByUser2\" //compulsory\n},\n{\n\"key\": \"sr:getAllProfileImgForUser2\"\n},\n{\n\"key\":\"sr:getAllTraitNameForUser2\"\n}\n\n]\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Redis Keys Deleted Successfully.\",\n\"cause\": \"\",\n\"data\": \"{}\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "deleteTermsNConditionsByAdmin",
    "title": "deleteTermsNConditionsByAdmin",
    "name": "deleteTermsNConditionsByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"term_n_condition_id\":1//compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Term and condition deleted successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "exportExpenseDetailByAdmin",
    "title": "exportExpenseDetailByAdmin",
    "name": "exportExpenseDetailByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"page\":1, //compulsory\n\"item_count\":5, //compulsory\n\"min_amt\":15, //optional\n\"max_amt\":20, //optional\n\"skuname\":\"CASH_CREDIT\", //compulsory\n\"status\":1 //compulsory 0=Pending,1=Success,2=Return,3=Cancel\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Expense file successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"result\": \"http://localhost/question_answer/image_bucket/exported/expense_master_2019_05_21_18_19_19.csv\"\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getAdminData",
    "title": "getAdminData",
    "name": "getAdminData",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Admin's data fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"result\": [\n{\n\"user_id\": 6,\n\"first_name\": \"Herry\",\n\"last_name\": \"portal\",\n\"email_id\": \"herry@grr.la\",\n\"is_active\": 1,\n\"create_time\": \"2019-05-05 06:16:05\",\n\"update_time\": \"2019-05-05 11:46:05\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getContactDetailByAdmin",
    "title": "getContactDetailByAdmin",
    "name": "getContactDetailByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"page\":1, //compulsory\n\"item_count\":10 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Contact detail fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_items\": 3,\n\"is_next_page\": false,\n\"result\": [\n{\n\"contact_id\": 1,\n\"answer_user_id\": \"1\",\n\"sender_user_id\": 2,\n\"email_id\": \"elsa@grr.la\",\n\"phone_no\": \"7896541230\",\n\"subject\": \"subject\",\n\"description\": \"What is app? Please, send description of app.\",\n\"answer\": \"I'm using the single activity multi fragments with navigation component.how do i hide the bottom navigation bar for some of the fragments?\",\n\"create_time\": \"2019-05-04 14:22:58\",\n\"update_time\": \"2019-05-04 20:56:57\"\n},\n{\n\"contact_id\": 3,\n\"answer_user_id\": \"\",\n\"sender_user_id\": 2,\n\"email_id\": \"elsa@grr.la\",\n\"phone_no\": \"7896541230\",\n\"subject\": \"subject\",\n\"description\": \"What is app? Please, send description of app.\",\n\"answer\": \"\",\n\"create_time\": \"2019-05-04 14:58:47\",\n\"update_time\": \"2019-05-04 20:28:47\"\n},\n{\n\"contact_id\": 2,\n\"answer_user_id\": \"\",\n\"sender_user_id\": 2,\n\"email_id\": \"elsa@grr.la\",\n\"phone_no\": \"7896541230\",\n\"subject\": \"subject\",\n\"description\": \"What is app? Please, send description of app.\",\n\"answer\": \"\",\n\"create_time\": \"2019-05-04 14:58:45\",\n\"update_time\": \"2019-05-04 20:28:45\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getDebitByAdmin",
    "title": "getDebitByAdmin",
    "name": "getDebitByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:}",
          "content": "// Give all data in one page by set page = 0 & item_count = 0\n{\n\"page\":0, //compulsory\n\"item_count\":0, //compulsory\n\"order_by\":\"update_time\", //optional\n\"order_type\":\"DESC, //optional\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Debit fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_items\": 3,\n\"is_next_page\": false,\n\"result\": [\n{\n\"debit_id\": 2,\n\"expenses_no\": 2,\n\"expenses_name\": \"expenses price\",\n\"expenses_price\": 678,\n\"trans_per\": 0.1,\n\"coins\": 200,\n\"amount\": 10,\n\"invite_amt\": 10,\n\"create_time\": \"2019-05-05 07:49:40\",\n\"update_time\": \"2019-05-05 15:18:28\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getExpenseDetailByAdmin",
    "title": "getExpenseDetailByAdmin",
    "name": "getExpenseDetailByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"page\":1, //compulsory\n\"item_count\":5, //compulsory\n\"min_amt\":15, //optional\n\"max_amt\":20, //optional\n\"skuname\":\"CASH_CREDIT\", //compulsory\n\"status\":1 //compulsory 0=Pending,1=Success,2=Return,3=Cancel\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Expense fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_items\": 1,\n\"is_next_page\": false,\n\"result\": [\n{\n\"expense_id\": 2,\n\"req_phone_no\": \"786543210\",\n\"user_id\":2,\n\"is_phone_no_verify\": 1,\n\"request_coin\": \"200\",\n\"approve_coin\": \"180\",\n\"payment\": \"10\",\n\"pay\": \"17.82\",\n\"status\": 1\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getKeywordByAdmin",
    "title": "getKeywordByAdmin",
    "name": "getKeywordByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:}",
          "content": "// Give all data in one page by set page = 0 & item_count = 0\n{\n\"page\":0, //compulsory\n\"item_count\":0, //compulsory\n\"order_by\":\"update_time\", //optional\n\"order_type\":\"DESC, //optional\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Keywords fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_items\": 5,\n\"is_next_page\": false,\n\"result\": [\n{\n\"keyword_id\": 3,\n\"keyword\": \"KAdsGoogleBottomBannerApp\",\n\"value\": \"KAdsGoogleBottomBannerApp\",\n\"description\": \"the test description\",\n\"skuname\": \"CASH_CREDIT\",\n\"is_active\": 1,\n\"create_time\": \"2019-05-06 19:17:15\",\n\"update_time\": \"2019-05-07 00:47:15\"\n},\n{\n\"keyword_id\": 1,\n\"keyword\": \"KAdsGoogleBottomBannerApp\",\n\"value\": \"\",\n\"description\": \"\",\n\"skuname\": \"\",\n\"is_active\": 1,\n\"create_time\": \"2019-05-06 19:10:28\",\n\"update_time\": \"2019-05-07 00:40:28\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getNotifyByAdmin",
    "title": "getNotifyByAdmin",
    "name": "getNotifyByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "// Give all data in one page by set page = 0 & item_count = 0\n{\n\"page\":0, //compulsory\n\"item_count\":0, //compulsory\n\"order_by\":\"update_time\", //optional\n\"order_type\":\"DESC, //optional\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Notify fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_items\": 3,\n\"is_next_page\": false,\n\"result\": [\n{\n\"notify_id\": 5,\n\"user_id\": 1,\n\"alert_data\": \"Push\",\n\"skuname\": \"CASH_CREDIT\",\n\"is_active\": 1,\n\"create_time\": \"2019-05-06 18:32:37\",\n\"update_time\": \"2019-05-07 00:02:37\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getRedisKeyDetail",
    "title": "getRedisKeyDetail",
    "name": "getRedisKeyDetail",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"key\": \"sr:getAllTraitNameForUser2\" //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Redis Key Detail Fetched Successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"keys_detail\": [\n{\n\"trait_id\": 1,\n\"trait_name\": \"Unchanging\",\n\"is_selected\": 0\n},\n{\n\"trait_id\": 2,\n\"trait_name\": \"Cute\",\n\"is_selected\": 1\n},\n{\n\"trait_id\": 3,\n\"trait_name\": \"Challenging\",\n\"is_selected\": 0\n},\n{\n\"trait_id\": 4,\n\"trait_name\": \"Humorous\",\n\"is_selected\": 0\n},\n{\n\"trait_id\": 5,\n\"trait_name\": \"Good-natured\",\n\"is_selected\": 1\n},\n{\n\"trait_id\": 6,\n\"trait_name\": \"Relaxed\",\n\"is_selected\": 1\n},\n{\n\"trait_id\": 7,\n\"trait_name\": \"Crude\",\n\"is_selected\": 1\n},\n{\n\"trait_id\": 8,\n\"trait_name\": \"Dishonest\",\n\"is_selected\": 1\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getRedisKeys",
    "title": "getRedisKeys",
    "name": "getRedisKeys",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Redis keys fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"keys_list\": [\n\"qa:424f74a6a7ed4d4ed4761507ebcd209a6ef0937b:timer\",\n\"qa:getQuestionAnswerByAdmin:1:50:qm.id:DESC\",\n\"qa:getAllUserForAdmin1:10:update_time:DESC\",\n\"qa:424f74a6a7ed4d4ed4761507ebcd209a6ef0937b\"\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "getTermsNConditionsByAdmin",
    "title": "getTermsNConditionsByAdmin",
    "name": "getTermsNConditionsByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"page\":1, //compulsory\n\"item_count\":10, //compulsory\n\"order_by\":\"update_time\",\n\"order_type\":\"DESC\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Terms and conditions fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_items\": 2,\n\"is_next_page\": false,\n\"result\": [\n{\n\"term_n_condition_id\": 3,\n\"subject\": \"License for Images and Videos ??? Pixabay License\",\n\"description\": \"sale or distribution of Images or Videos as digital stock photos or as digital wallpapers;sale or distribution of Images or Videos e.g. as a posters, digital prints or physical products, without adding any additional elements or otherwise adding value\",\n\"is_active\": 0,\n\"create_time\": \"2019-04-28 04:08:35\",\n\"update_time\": \"2019-04-28 09:45:28\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "payRSFromByAdmin",
    "title": "payRSFromByAdmin",
    "name": "payRSFromByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"expense_id\":1,//compulsory\n\"request_coin\":200,//compulsory\n\"approve_coin\":150,//compulsory\n\"status\":1//compulsory 0=Pending,1=Success,2=Return,3=Cancel\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Payment paid successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "replayToContactByAdmin",
    "title": "replayToContactByAdmin",
    "name": "replayToContactByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"contact_id\":2, //compulsory\n\"answer\":\"I'm using the single activity multi fragments with navigation component.how do i hide the bottom navigation bar for some of the fragments?\", //compulsory\n\"sender_user_id\":3 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Replay has been sent successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "setAdminStatus",
    "title": "setAdminStatus",
    "name": "setAdminStatus",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"is_active\":0, //compulsory\n\"user_id\":3 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Admin deactivated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "setStatusOfTermsNConditionsByAdmin",
    "title": "setStatusOfTermsNConditionsByAdmin",
    "name": "setStatusOfTermsNConditionsByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"is_active\":1,//compulsory\n\"term_n_condition_id\":1//compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Set status successfully of term and condition.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "updateAdminData",
    "title": "updateAdminData",
    "name": "updateAdminData",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"email_id\":\"anna@gmail.com\", //compulsory\n\"first_name\":\"Anna\", //compulsory\n\"last_name\":\"Lisa\", //compulsory\n\"user_id\":5 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Admin's data updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "updateDebitByAdmin",
    "title": "updateDebitByAdmin",
    "name": "updateDebitByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:}",
          "content": "{\n\"debit_id\":1, //compulsory\n\"expenses_no\":1, //compulsory\n\"expenses_name\":\"expenses_price\", //compulsory\n\"expenses_price\":200, //compulsory\n\"trans_per\":0.10, //compulsory\n\"coins\":200, //compulsory\n\"amount\":10, //compulsory\n\"invite_amt\":10 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Debit updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "updateKeywordByAdmin",
    "title": "updateKeywordByAdmin",
    "name": "updateKeywordByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:}",
          "content": "{\n\"keyword_id\":1,, //compulsory\n\"keyword\":\"KAdsGoogleBottomBannerApp\", //compulsory\n\"value\":\"KAdsGoogleBottomBannerApp\",\n\"description\":\"the test description\",\n\"skuname\":\"CASH_CREDIT\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Keyword updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "updateNotifyByAdmin",
    "title": "updateNotifyByAdmin",
    "name": "updateNotifyByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:}",
          "content": "{\n\"notify_id\":1, //compulsory\n\"alert_data\":\"test\" //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Notify updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "updateTermsNConditionsByAdmin",
    "title": "updateTermsNConditionsByAdmin",
    "name": "updateTermsNConditionsByAdmin",
    "group": "Admin",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"term_n_condition_id\":2, //compulsory\n\"subject\":\"License for Images and Videos\",  //compulsory\n\"description\":\"sale or distribution of Images or Videos as digital stock photos or as digital wallpapers;sale or distribution of Images or Videos\"  //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Term and condition updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Admin"
  },
  {
    "type": "post",
    "url": "changePassword",
    "title": "changePassword",
    "name": "changePassword",
    "group": "Common_For_All",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"current_password\":\"demo@1234\",\n\"new_password\":\"demo@123\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Password Changed successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"token\": \"\"\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/LoginController.php",
    "groupTitle": "Common_For_All"
  },
  {
    "type": "post",
    "url": "doLogout",
    "title": "doLogout",
    "name": "doLogout",
    "group": "Common_For_All",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\nKey: Authorization\nValue: Bearer token\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"User have successfully logged out.\",\n\"cause\": \"\",\n\"data\": {\n\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/LoginController.php",
    "groupTitle": "Common_For_All"
  },
  {
    "type": "post",
    "url": "getFAQByUser",
    "title": "getFAQByUser",
    "name": "getFAQByUser",
    "group": "General",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n//Give all data in one page by set page = 0 & item_count = 0\n\"page\":1, //compulsory\n\"item_count\":10 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"FAQ fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_round\": 2,\n\"is_next_page\": false,\n\"result\": [\n{\n\"faq_id\": 2,\n\"faq_question\": \"Can I use?\",\n\"faq_answer\": \"You can copy, modify, distribute, and use the images, even for commercial purposes, all without asking for permission or giving credits to the artist. However, depicted content may still be protected by trademarks, publicity or privacy rights\",\n\"is_active\": 1,\n\"create_time\": \"2019-04-26 16:12:12\",\n\"update_time\": \"2019-04-26 21:44:17\"\n},\n{\n\"faq_id\": 1,\n\"faq_question\": \"Can I use your images?\",\n\"faq_answer\": \"You can copy, modify, distribute, and use the images, even for commercial purposes, all without asking for permission or giving credits to the artist. However, depicted content may still be protected by trademarks, publicity or privacy rights\",\n\"is_active\": 1,\n\"create_time\": \"2019-04-26 16:12:02\",\n\"update_time\": \"2019-04-26 21:42:02\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/GeneralController.php",
    "groupTitle": "General"
  },
  {
    "type": "post",
    "url": "getTermsNConditionsByUser",
    "title": "getTermsNConditionsByUser",
    "name": "getTermsNConditionsByUser",
    "group": "General",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n//Give all data in one page by set page = 0 & item_count = 0\n\"page\":1, //compulsory\n\"item_count\":10 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"FAQ fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"total_round\": 2,\n\"is_next_page\": false,\n\"result\": [\n{\n\"faq_id\": 2,\n\"faq_question\": \"Can I use?\",\n\"faq_answer\": \"You can copy, modify, distribute, and use the images, even for commercial purposes, all without asking for permission or giving credits to the artist. However, depicted content may still be protected by trademarks, publicity or privacy rights\",\n\"is_active\": 1,\n\"create_time\": \"2019-04-26 16:12:12\",\n\"update_time\": \"2019-04-26 21:44:17\"\n},\n{\n\"faq_id\": 1,\n\"faq_question\": \"Can I use your images?\",\n\"faq_answer\": \"You can copy, modify, distribute, and use the images, even for commercial purposes, all without asking for permission or giving credits to the artist. However, depicted content may still be protected by trademarks, publicity or privacy rights\",\n\"is_active\": 1,\n\"create_time\": \"2019-04-26 16:12:02\",\n\"update_time\": \"2019-04-26 21:42:02\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/GeneralController.php",
    "groupTitle": "General"
  },
  {
    "type": "post",
    "url": "statusCode",
    "title": "statusCode",
    "name": "statusCode",
    "group": "Status_Code",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Body:",
          "content": "{\n400 : Bad Request,\n401 : Token Expired,\n404 : Not Found,\n201 : Error Message,\n200 : Success,\n425 : Unassigned\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/AdminController.php",
    "groupTitle": "Status_Code"
  },
  {
    "type": "post",
    "url": "doLoginForSocialUser",
    "title": "doLoginForSocialUser",
    "name": "doLoginForSocialUser",
    "group": "User_Login",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"signup_type\":2,\n\"social_uid\":123456,\n\"first_name\":\"test\",\n\"last_name\":\"demo\",\n\"email_id\":\"demo@grr.la\",\n\"device_info\":{\n\"device_carrier\": \"\",\n\"device_country_code\": \"IN\",\n\"device_reg_id\": \"115a1a110\",\n\"device_default_time_zone\": \"Asia/Calcutta\",\n\"device_language\": \"en\",\n\"device_library_version\": \"1\",\n\"device_application_version\":\"\",\n\"device_local_code\": \"NA\",\n\"device_model_name\": \"Micromax AQ4501\",\n\"device_os_version\": \"6.0.1\",\n\"device_platform\": \"android\",\n\"device_registration_date\": \"2016-05-06T15:58:11 +0530\",\n\"device_resolution\": \"480x782\",\n\"device_type\": \"phone\",\n\"device_udid\": \"109111aa1121\",\n\"device_vendor_name\": \"Micromax\"\n}\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Login successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"token\": \"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjI2LCJpc3MiOiJodHRwOi8vbG9jYWxob3N0L3F1ZXN0aW9uX2Fuc3dlci9hcGkvcHVibGljL2FwaS9kb0xvZ2luRm9yU29jaWFsVXNlciIsImlhdCI6MTU1NTA5NTkxMCwiZXhwIjoxNTU2MzA1NTEwLCJuYmYiOjE1NTUwOTU5MTAsImp0aSI6ImtEYWNyYktpdndiTERTTW0ifQ.BAzXOq0-fGhYhEOCNG6K48_y4to_wiSvSwh87Gwq74w\",\n\"user_detail\": {\n\"user_id\": 26,\n\"first_name\": \"test\",\n\"last_name\": \"demo\",\n\"email_id\": \"demo@grr.la\",\n\"gender\": 0,\n\"coins\": 0,\n\"phone_no\": \"\",\n\"is_active\": 1,\n\"create_time\": \"2019-04-12 18:56:52\",\n\"update_time\": \"2019-04-13 00:26:52\"\n}\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/RegisterController.php",
    "groupTitle": "User_Login"
  },
  {
    "type": "post",
    "url": "doLoginForUser",
    "title": "doLoginForUser",
    "name": "doLoginForUser",
    "group": "User_Login",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Body:",
          "content": "{\n\"email_id\":\"test1@grr.la\",  //compulsory\n\"password\":\"demo@123\",      //compulsory\n\"device_info\": {\n\"device_carrier\": \"\",\n\"device_country_code\": \"IN\",\n\"device_reg_id\": \"asdvasghhasfhdgasfdffd\",  //compulsory\n\"device_default_time_zone\": \"Asia/Calcutta\",\n\"device_language\": \"en\",\n\"device_latitude\": \"\",\n\"device_library_version\": \"1\",\n\"device_local_code\": \"NA\",\n\"device_longitude\": \"\",\n\"device_model_name\": \"Micromax AQ4501\",\n\"device_os_version\": \"6.0.1\",\n\"device_platform\": \"android\",      //compulsory\n\"device_registration_date\": \"2016-05-06T15:58:11 +0530\",\n\"device_resolution\": \"480x782\",\n\"device_type\": \"phone\",\n\"device_udid\": \"1a7b0b368a12d370\",   //compulsory\n\"device_vendor_name\": \"Micromax\",\n\"project_package_name\": \"com.test.projectsetup\"\n}\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Login successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"token\": \"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEyLCJpc3MiOiJodHRwOi8vbG9jYWxob3N0L3F1ZXN0aW9uX2Fuc3dlci9hcGkvcHVibGljL2FwaS9kb0xvZ2luRm9yVXNlciIsImlhdCI6MTU1NTE4NjUzOCwiZXhwIjoxNTU2Mzk2MTM4LCJuYmYiOjE1NTUxODY1MzgsImp0aSI6IlJINjB0NENtRHdRSmQ4dU8ifQ.LqNA1oTURa8rZC5gxb9TtEtsrXYZC4xcWVn89vY59ok\",\n\"user_detail\": {\n\"user_id\": 12,\n\"first_name\": \"john\",\n\"last_name\": \"peter\",\n\"email_id\": \"test1@grr.la\",\n\"gender\": 1,\n\"coins\": 0,\n\"phone_no\": \"7896541230\",\n\"is_active\": 1,\n\"create_time\": \"2019-04-12 16:50:15\",\n\"update_time\": \"2019-04-12 22:20:16\"\n}\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/LoginController.php",
    "groupTitle": "User_Login"
  },
  {
    "type": "post",
    "url": "forgotPasswordForSendOTP",
    "title": "forgotPasswordForSendOTP",
    "name": "forgotPasswordForSendOTP",
    "group": "User_Login",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"email_id\":\"demo12@grr.la\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"The OTP will be sent via massage on verified phone number\",\n\"cause\": \"\",\n\"data\": {\n\"phone_no\": \"8160891945\"\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/LoginController.php",
    "groupTitle": "User_Login"
  },
  {
    "type": "post",
    "url": "newPasswordForUser",
    "title": "newPasswordForUser",
    "name": "newPasswordForUser",
    "group": "User_Login",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"email_id\":\"roy12@grr.la\",\n\"token\":\"9d797c648a1fa17aa3356c8f4ec6b2c93ad80b08b80ab1e861be5c9d610c24b4b6c55bd0e3583d133f3af964e03209d81c81\",\n\"new_password\":\"demo@123\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Password updated successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/LoginController.php",
    "groupTitle": "User_Login"
  },
  {
    "type": "post",
    "url": "resendOTPForUser",
    "title": "resendOTPForUser",
    "name": "resendOTPForUser",
    "group": "User_Login",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"email_id\":\"demo12@grr.la\" OR \"user_registration_temp_id\":56\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"The OTP will be sent via email on verified email ID.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/LoginController.php",
    "groupTitle": "User_Login"
  },
  {
    "type": "post",
    "url": "signupUser",
    "title": "signupUser",
    "name": "signupUser",
    "group": "User_Login",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"first_name\":\"test\",\n\"last_name\":\"test\",\n\"phone_no\":7896541230,\n\"email_id\":\"test@grr.la\",\n\"password\":\"123456\",\n\"signup_type\":1\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"User registered successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"user_registration_temp_id\": 6\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/RegisterController.php",
    "groupTitle": "User_Login"
  },
  {
    "type": "post",
    "url": "verifyOTP",
    "title": "verifyOTP",
    "name": "verifyOTP",
    "group": "User_Login",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"email_id\":\"roy12@grr.la\",\n\"otp_token\":2780\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"The OTP verified successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"token\": \"9d797c648a1fa17aa3356c8f4ec6b2c93ad80b08b80ab1e861be5c9d610c24b4b6c55bd0e3583d133f3af964e03209d81c81\"\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/LoginController.php",
    "groupTitle": "User_Login"
  },
  {
    "type": "post",
    "url": "verifyOTPForRegisterUser",
    "title": "verifyOTPForRegisterUser",
    "name": "verifyOTPForRegisterUser",
    "group": "User_Login",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"user_registration_temp_id\":14, //compulsory\n\"otp_token\": 8924,             //compulsory\n\"device_info\":{\n\"device_carrier\": \"\",\n\"device_country_code\": \"IN\",\n\"device_reg_id\": \"115a1a110\",\n\"device_default_time_zone\": \"Asia/Calcutta\",\n\"device_language\": \"en\",\n\"device_library_version\": \"1\",\n\"device_application_version\":\"\",\n\"device_local_code\": \"NA\",\n\"device_model_name\": \"Micromax AQ4501\",\n\"device_os_version\": \"6.0.1\",\n\"device_platform\": \"android\",\n\"device_registration_date\": \"2016-05-06T15:58:11 +0530\",\n\"device_resolution\": \"480x782\",\n\"device_type\": \"phone\",\n\"device_udid\": \"109111aa1121\",\n\"device_vendor_name\": \"Micromax\"\n}\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Login successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"token\": \"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEyLCJpc3MiOiJodHRwOi8vbG9jYWxob3N0L3F1ZXN0aW9uX2Fuc3dlci9hcGkvcHVibGljL2FwaS92ZXJpZnlPVFBGb3JSZWdpc3RlclVzZXIiLCJpYXQiOjE1NTUwODc4MTcsImV4cCI6MTU1NjI5NzQxNywibmJmIjoxNTU1MDg3ODE3LCJqdGkiOiJsTndxODdvcnRpOWY0amRGIn0.t_12bY72-z3Gx2McPBsYIhjIUm1fqe8jcWQVZgv2qFA\",\n\"user_detail\": {\n\"user_id\": 12,\n\"first_name\": \"John\",\n\"last_name\": \"Peter\",\n\"email_id\": \"john@grr.la\",\n\"gender\": 1,\n\"coins\": 0,\n\"phone_no\": \"7896541230\",\n\"is_active\": 1,\n\"create_time\": \"2019-04-12 16:50:15\",\n\"update_time\": \"2019-04-12 22:20:16\"\n}\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/RegisterController.php",
    "groupTitle": "User_Login"
  },
  {
    "type": "post",
    "url": "addCoinBySomeTaskForUser",
    "title": "addCoinBySomeTaskForUser",
    "name": "addCoinBySomeTaskForUser",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"token\":\"bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjYsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3QvcXVlc3Rpb25fYW5zd2VyL2FwaS9wdWJsaWMvYXBpL2RvTG9naW5Gb3JVc2VyIiwiaWF0IjoxNTYwNTI5NTAxLCJleHAiOjE1NjE3MzkxMDEsIm5iZiI6MTU2MDUyOTUwMSwianRpIjoiRERVR2ptUFpTUXA5blRJeSJ9.d7vXAsg8oJbbsIsp49Rv3mIgh8kgXn-V2RaXZYTZwYs\",\n\"coins\":20\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Coins added successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "contactByUser",
    "title": "contactByUser",
    "name": "contactByUser",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"token\":\"bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjYsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3QvcXVlc3Rpb25fYW5zd2VyL2FwaS9wdWJsaWMvYXBpL2RvTG9naW5Gb3JVc2VyIiwiaWF0IjoxNTYwNTI5NTAxLCJleHAiOjE1NjE3MzkxMDEsIm5iZiI6MTU2MDUyOTUwMSwianRpIjoiRERVR2ptUFpTUXA5blRJeSJ9.d7vXAsg8oJbbsIsp49Rv3mIgh8kgXn-V2RaXZYTZwYs\",\n\"subject\":\"subject\",//compulsory\n\"description\":\"What is app? Please, send description of app.\"//compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Contact has been sent successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getContactDetailByUser",
    "title": "getContactDetailByUser",
    "name": "getContactDetailByUser",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"token\":\"bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjYsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3QvcXVlc3Rpb25fYW5zd2VyL2FwaS9wdWJsaWMvYXBpL2RvTG9naW5Gb3JVc2VyIiwiaWF0IjoxNTYwNTI5NTAxLCJleHAiOjE1NjE3MzkxMDEsIm5iZiI6MTU2MDUyOTUwMSwianRpIjoiRERVR2ptUFpTUXA5blRJeSJ9.d7vXAsg8oJbbsIsp49Rv3mIgh8kgXn-V2RaXZYTZwYs\",\n\"page\":1, //compulsory\n\"item_count\":10 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Contact detail fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"is_next_page\": true,\n\"result\": [\n{\n\"contact_id\": 1,\n\"sender_user_id\": 2,\n\"answer_user_id\": \"1\",\n\"subject\": \"subject\",\n\"description\": \"What is app? Please, send description of app.\",\n\"answer\": \"I'm using the single activity multi fragments with navigation component.how do i hide the bottom navigation bar for some of the fragments?\",\n\"create_time\": \"2019-05-04 14:22:58\",\n\"update_time\": \"2019-05-04 20:56:57\"\n},\n{\n\"contact_id\": 3,\n\"sender_user_id\": 2,\n\"answer_user_id\": \"\",\n\"subject\": \"subject\",\n\"description\": \"What is app? Please, send description of app.\",\n\"answer\": \"\",\n\"create_time\": \"2019-05-04 14:58:47\",\n\"update_time\": \"2019-05-04 20:28:47\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getRoundDetailByUser",
    "title": "getRoundDetailByUser",
    "name": "getRoundDetailByUser",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"token\":\"bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjYsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3QvcXVlc3Rpb25fYW5zd2VyL2FwaS9wdWJsaWMvYXBpL2RvTG9naW5Gb3JVc2VyIiwiaWF0IjoxNTYwNTI5NTAxLCJleHAiOjE1NjE3MzkxMDEsIm5iZiI6MTU2MDUyOTUwMSwianRpIjoiRERVR2ptUFpTUXA5blRJeSJ9.d7vXAsg8oJbbsIsp49Rv3mIgh8kgXn-V2RaXZYTZwYs\",\n\"page\":1, //compulsory\n\"item_count\":10, //compulsory\n\"order_by\":\"update_time\"\n\"order_type\":\"asc\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Round fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"result\": [\n{\n\"round_id\": 1,\n\"round_name\": \"round 3\",\n\"entry_coins\": 200,\n\"coin_per_answer\": 20,\n\"sec_to_answer\": 20,\n\"coins_minus\": 200,\n\"is_active\": 1,\n\"create_time\": \"2019-04-03 17:20:46\",\n\"update_time\": \"2019-04-03 23:00:17\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getQuestionByRoundForUser",
    "title": "getQuestionByRoundForUser",
    "name": "getRoundDetailByUser",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"token\":\"bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjYsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3QvcXVlc3Rpb25fYW5zd2VyL2FwaS9wdWJsaWMvYXBpL2RvTG9naW5Gb3JVc2VyIiwiaWF0IjoxNTYwNTI5NTAxLCJleHAiOjE1NjE3MzkxMDEsIm5iZiI6MTU2MDUyOTUwMSwianRpIjoiRERVR2ptUFpTUXA5blRJeSJ9.d7vXAsg8oJbbsIsp49Rv3mIgh8kgXn-V2RaXZYTZwYs\",\n\"round_id\":3\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Question fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"round_detail\": [\n{\n\"round_id\": 3,\n\"round_name\": \"round 3\",\n\"entry_coins\": 160,\n\"coin_per_answer\": 20,\n\"sec_to_answer\": 20,\n\"coins_minus\": 200,\n\"is_active\": 1,\n\"create_time\": \"2019-04-14 12:29:28\",\n\"update_time\": \"2019-04-14 17:59:28\"\n}\n],\n\"questions\": [\n{\n\"question_id\": 1,\n\"question\": \"What is this?\",\n\"question_thumbnail_image\": \"\",\n\"question_compressed_image\": \"\",\n\"question_original_image\": \"\",\n\"answer_a\": \"answer round 1\",\n\"answer_b\": \"answer round 1\",\n\"answer_c\": \"answer round 1\",\n\"answer_d\": \"answer round 1\",\n\"real_answer\": \"answer_a\"\n},\n{\n\"question_id\": 3,\n\"question\": \"What is this?\",\n\"question_thumbnail_image\": \"http://localhost/question_answer/image_bucket/thumbnail/5cb37b24b8757_question_image_1555266340.jpg\",\n\"question_compressed_image\": \"http://localhost/question_answer/image_bucket/compressed/5cb37b24b8757_question_image_1555266340.jpg\",\n\"question_original_image\": \"http://localhost/question_answer/image_bucket/original/5cb37b24b8757_question_image_1555266340.jpg\",\n\"answer_a\": \"answer round 1\",\n\"answer_b\": \"answer round 1\",\n\"answer_c\": \"answer round 1\",\n\"answer_d\": \"answer round 1\",\n\"real_answer\": \"answer_a\"\n}\n]\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "getUserProfileByUser",
    "title": "getUserProfileByUser",
    "name": "getUserProfileByUser",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "//must be bearer token\n{\n\"token\":\"bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjYsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3QvcXVlc3Rpb25fYW5zd2VyL2FwaS9wdWJsaWMvYXBpL2RvTG9naW5Gb3JVc2VyIiwiaWF0IjoxNTYwNTI5NTAxLCJleHAiOjE1NjE3MzkxMDEsIm5iZiI6MTU2MDUyOTUwMSwianRpIjoiRERVR2ptUFpTUXA5blRJeSJ9.d7vXAsg8oJbbsIsp49Rv3mIgh8kgXn-V2RaXZYTZwYs\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Profile fetched successfully.\",\n\"cause\": \"\",\n\"data\": {\n\"user_detail\": {\n\"user_id\": 28,\n\"first_name\": \"elsa\",\n\"last_name\": \"Pater\",\n\"email_id\": \"elsa@grr.la\",\n\"gender\": 1,\n\"coins\": 0,\n\"phone_no\": \"8160891945\",\n\"is_active\": 1,\n\"create_time\": \"2019-04-13 21:47:41\",\n\"update_time\": \"2019-04-14 03:19:24\"\n}\n}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "registerUserDeviceByDeviceUdid",
    "title": "registerUserDeviceByDeviceUdid",
    "name": "registerUserDeviceByDeviceUdid",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"user_id\":11,\n\"device_carrier\": \"\",\n\"device_country_code\": \"IN\",\n\"device_reg_id\": \"115a1a110\",  //Mandatory\n\"device_default_time_zone\": \"Asia/Calcutta\",\n\"device_language\": \"en\",\n\"device_library_version\": \"1\",\n\"device_application_version\":\"\",\n\"device_local_code\": \"NA\",\n\"device_model_name\": \"Micromax AQ4501\",\n\"device_os_version\": \"6.0.1\",\n\"device_platform\": \"android\",  //Mandatory\n\"device_registration_date\": \"2016-05-06T15:58:11 +0530\",\n\"device_resolution\": \"480x782\",\n\"device_type\": \"phone\",\n\"device_udid\": \"109111aa1121\", //Mandatory\n\"device_vendor_name\": \"Micromax\"\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Device registered successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/RegisterController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "requestForCoinsToPay",
    "title": "requestForCoinsToPay",
    "name": "requestForCoinsToPay",
    "group": "User",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "Request-Header:",
          "content": "{\n}",
          "type": "json"
        },
        {
          "title": "Request-Body:",
          "content": "{\n\"token\":\"bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjYsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3QvcXVlc3Rpb25fYW5zd2VyL2FwaS9wdWJsaWMvYXBpL2RvTG9naW5Gb3JVc2VyIiwiaWF0IjoxNTYwNTI5NTAxLCJleHAiOjE1NjE3MzkxMDEsIm5iZiI6MTU2MDUyOTUwMSwianRpIjoiRERVR2ptUFpTUXA5blRJeSJ9.d7vXAsg8oJbbsIsp49Rv3mIgh8kgXn-V2RaXZYTZwYs\",\n\"skuname\":\"CASH_CREDIT\", //compulsory\n\"req_phone_no\":\"1234567890\", //compulsory\n\"request_coin\":200 //compulsory\n}",
          "type": "json"
        },
        {
          "title": "Success-Response:",
          "content": "{\n\"code\": 200,\n\"message\": \"Request has been sent successfully.\",\n\"cause\": \"\",\n\"data\": {}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/UserController.php",
    "groupTitle": "User"
  }
] });
