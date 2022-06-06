// live URL 
const LIVE = {
  API_URL: '../api/public/api/'
}
const LOCAL = {
  // API_URL: 'http://134.209.11.134/question_answer/api/public/api/'
  API_URL: 'http://localhost/QNA/api/public/api/'
}

// export const HOST =  LIVE;
export const HOST = LOCAL;


export const ERROR = {
  EMAIL_LENGTH: 'Please Enter Email',
  VALID_EMAIL: 'Please Enter Valid Email',
  PASSWORD_LENGTH: 'Please Enter Password',
  OFFLINE: 'Unable To Connect With Server, Please try Again',
  OLD_PASSWORD_LENGTH: 'Please Enter Old Password',
  NEW_PASSWORD_LENGTH: 'Please Enter New Password',
  CONFIRM_PASSWORD_LENGTH: 'Please Enter Confirm Password',
  CONFIRM_PASSWORD_MATCH: "Password and Confirm Password Are Not Match",
  ROUNDNAME_LENGTH: 'Please Enter Round Name',
  ENTRYCOIN_LENGTH: 'Please Enter Entry coins For Round',
  ENTRYCOIN_MIN_VALUE: 'Entry Coins For Round Is Low',
  ENTRYCOIN_NAN: 'Please Eneter Valid Entry Coins',
  COINSPERANSWER_LENGTH: 'Please Enter coins Per Answer',
  COINSPERANSWER_MIN_VALUE: 'Coins Per Answer Is Low',
  SECONDSTOANSWER_LENGTH: 'Please Enter Answer Time In Second',
  SECONDSTOANSWER_MIN_VALUE: 'Answer Time In Second Is Low',
  COINSMINUS_LENGTH: 'Please Enter Coins Minus Per Second',
  COINSMINUS_MIN_VALUE: 'Coins Minus Per Second Is Low',
  TOTALQUESTION_LENGTH: 'Please Enter Total Number Of Question',
  TOTALQUESTION_MIN_VALUE: 'Total Question Is Low',
  TIMEBREAK_LENGTH: 'Please Enter Time Break',
  TIMEBREAK_MIN_VALUE: 'Time Break Is Low',
  VALID_INT_NUMBER: 'Please Enter Valid Number',
  QUESTION_NAME_LENGTH: 'Please Enter Question',
  ANSWER_LENGTH: 'Please Enter Answer',
  REAL_ANSWER_LENGTH: 'Please Choose Correct Answer From All Option',
  FAQQUESTION_LENGTH: 'Please Enter FAQ Question',
  FAQANSWER_LENGTH: 'Please Enter FAQ Answer',
  TNCSUBJECT_LENGTH: "Please Enter Terms and Condition's Subject",
  TNCDESCRIPTION_LENGTH: "Please Enter Terms and Condition's Description",
  MESSAGELENGTH: 'Please Enter Some Text',
  ADMINFIRSTNAME_LENGTH: 'Please Enter FIrst Name',
  ADMINLASTNAME_LENGTH: 'Please Enter Last Name',

  // debit tab messages
  EXPENSENO_LENGTH: 'Please Enter Expense Number',
  EXPENSENAME_LENGTH: 'Please Enter Expense Name',
  EXPENSEPRICE_LENGTH: 'Please Enter Expense Price',
  TRANSPER_LENGTH: 'Please Enter Transaction Percentage',
  COINS_LENGTH: 'Please Enter Coins',
  AMOUNT_LENGTH: 'Please Enter Amount',
  INVITEAMOUNT_LENGTH: 'Please Enter Invite Amount',
  INVALID_PERCENTAGE: 'Please Enter Valid Percentage',
  MIN_VALUE_ERROR: 'Is Low',

  // notification messaage error
  NOTIFICATION_LENGTH: 'Please Enter Some Text.',

  // keyword page error
  KEYWORD_LENGTH: 'Please Enter Keyword.',
  KEYWORD_VALUE_LENGTH: 'Please Enter Value.',
  KEYWORD_DESCRIPTION_LENGTH: 'Please Enter Description.',

  // for approve coin payment dialog
  APPROVE_COIN_INVALID: ' Please Enter Approve Coin.',
  APPROVE_COIN_LOW: 'Approve Coins Are Low.',
  INVALID_APPROVE_COIN: 'Please Enter Approve Coins Less Then Requested Coins.',

  // for filter expense
  MIN_AMT_MIN: ' Minimum Amount Is Low.',
  MAX_AMT_MIN: ' Maximum Amount Is Low.'
}