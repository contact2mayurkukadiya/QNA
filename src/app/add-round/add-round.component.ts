import { Component, OnInit } from '@angular/core';
import { DataService } from '../data.service';
import { ERROR } from '../app.constants';
import { MatDialogRef } from '@angular/material/dialog';

@Component({
  selector: 'app-add-round',
  templateUrl: './add-round.component.html',
  styleUrls: ['./add-round.component.scss']
})
export class AddRoundComponent implements OnInit {

  error: any = "";
  minValueLimit = 0;

  roundDetail: any = {
    round_name: null,
    entry_coins: null,
    coin_per_answer: null,
    sec_to_answer: null,
    coins_minus: null
  }

  constructor(public dataService: DataService, public dialogRef: MatDialogRef<AddRoundComponent>) { }

  ngOnInit() {
  }

  addRound(roundDetail) {
    if (!this.dataService.isExist(roundDetail)) {
      this.error = ERROR.ROUNDNAME_LENGTH
    }
    else if (!this.dataService.isExist(roundDetail.round_name)) {
      this.error = ERROR.ROUNDNAME_LENGTH
    }
    else if (!this.dataService.isExist(roundDetail.entry_coins)) {
      this.error = ERROR.ENTRYCOIN_LENGTH
    }
    else if (roundDetail.entry_coins < 0) {
      this.error = ERROR.ENTRYCOIN_MIN_VALUE
    }
    else if (!this.dataService.isInt(roundDetail.entry_coins)) {
      this.error = ERROR.VALID_INT_NUMBER + ' In Entry Coins'
    }
    else if (!this.dataService.isExist(roundDetail.coin_per_answer)) {
      this.error = ERROR.COINSPERANSWER_LENGTH
    }
    else if (roundDetail.coin_per_answer < 0) {
      this.error = ERROR.COINSPERANSWER_MIN_VALUE
    }
    else if (!this.dataService.isInt(roundDetail.coin_per_answer)) {
      this.error = ERROR.VALID_INT_NUMBER + ' In Coins Per Answer'
    }
    else if (!this.dataService.isExist(roundDetail.sec_to_answer)) {
      this.error = ERROR.SECONDSTOANSWER_LENGTH
    }
    else if (roundDetail.sec_to_answer < 0) {
      this.error = ERROR.SECONDSTOANSWER_MIN_VALUE
    }
    else if (!this.dataService.isExist(roundDetail.coins_minus)) {
      this.error = ERROR.COINSMINUS_LENGTH
    }
    else if (roundDetail.coins_minus < 0) {
      this.error = ERROR.COINSMINUS_MIN_VALUE
    }
    else if (!this.dataService.isInt(roundDetail.coins_minus)) {
      this.error = ERROR.VALID_INT_NUMBER + ' In Coins Minus'
    }
    else if (!this.dataService.isExist(roundDetail.total_question_for_user)) {
      this.error = ERROR.TOTALQUESTION_LENGTH
    }
    else if (roundDetail.total_question_for_user < 0) {
      this.error = ERROR.TOTALQUESTION_MIN_VALUE
    }
    else if (!this.dataService.isInt(roundDetail.total_question_for_user)) {
      this.error = ERROR.VALID_INT_NUMBER + ' In Total Coin'
    }
    else if (!this.dataService.isExist(roundDetail.time_break)) {
      this.error = ERROR.TIMEBREAK_LENGTH
    }
    else if (roundDetail.time_break < 0) {
      this.error = ERROR.TIMEBREAK_MIN_VALUE
    }
    else if (!this.dataService.isInt(roundDetail.time_break)) {
      this.error = ERROR.VALID_INT_NUMBER + ' In Time Break'
    }
    else {
      this.dataService.showLoader();
      this.dataService.postData('addRoundDetailByAdmin', {
        round_name: roundDetail.round_name,
        entry_coins: roundDetail.entry_coins,
        coin_per_answer: roundDetail.coin_per_answer,
        sec_to_answer: roundDetail.sec_to_answer,
        coins_minus: roundDetail.coins_minus,
        total_question_for_user: roundDetail.total_question_for_user,
        time_break: roundDetail.time_break
      }, {
          headers: ({
            'Authorization': 'Bearer ' + localStorage.getItem('token')
          })
        }).then((result: any) => {
          if (result.code == 200) {
            this.dataService.hideLoader();
            if (this.dialogRef)
              this.dialogRef.close(true);
          }
          else if (result.code == 201) {
            this.dataService.hideLoader();
            this.dataService.showSnackBar(result.message, '', 3000, 'error');
          }
          else {
            this.dataService.hideLoader();
            this.dataService.showSnackBar(ERROR.OFFLINE, '', 3000, 'error');
          }
        }).catch(err => {
          this.dataService.hideLoader();
          this.dataService.showSnackBar(ERROR.OFFLINE, '', 3000, 'error');
        })
    }
  }

  ReplaceValidNumber(data) {
    return data.replace(/[^0-9]/g, '');
  }


  close() {
    this.dialogRef.close(false);
  }
}
