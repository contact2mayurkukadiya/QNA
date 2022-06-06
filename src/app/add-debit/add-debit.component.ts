import { Component, OnInit, Inject } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { DataService } from '../data.service';
import { ERROR } from '../app.constants';

@Component({
  selector: 'app-add-debit',
  templateUrl: './add-debit.component.html',
  styleUrls: ['./add-debit.component.scss']
})
export class AddDebitComponent implements OnInit {

  error: any = "";
  minValueLimit = 0;

  isUpdatingMode: any = false;

  debitDetail: any = {
    debit_id: null,
    expenses_no: null,
    expenses_name: null,
    expenses_price: null,
    trans_per: null,
    coins: null,
    amount: null,
    invite_amt: null
  }

  constructor(public dataService: DataService, public dialogRef: MatDialogRef<AddDebitComponent>, @Inject(MAT_DIALOG_DATA) private dialogData: any) {
    if (dialogData) {
      this.isUpdatingMode = true;
      this.debitDetail.debit_id = dialogData.debit_id;
      this.debitDetail.expenses_no = dialogData.expenses_no;
      this.debitDetail.expenses_name = dialogData.expenses_name;
      this.debitDetail.expenses_price = dialogData.expenses_price;
      this.debitDetail.trans_per = dialogData.trans_per
      this.debitDetail.coins = dialogData.coins
      this.debitDetail.amount = dialogData.amount
      this.debitDetail.invite_amt = dialogData.invite_amt
    }
  }

  ngOnInit() {
  }

  addDebit(debitDetail:any) {
    if (!this.dataService.isExist(debitDetail)) {
      this.error = ERROR.EXPENSENO_LENGTH
    }
    else if (!this.dataService.isExist(debitDetail.expenses_no)) {
      this.error = ERROR.EXPENSENO_LENGTH
    }
    else if (!this.dataService.isExist(debitDetail.expenses_name)) {
      this.error = ERROR.EXPENSENAME_LENGTH
    }
    else if (!this.dataService.isExist(debitDetail.expenses_price)) {
      this.error = ERROR.EXPENSEPRICE_LENGTH
    }
    else if (debitDetail.expenses_price < 0) {
      this.error = 'Expenses Price ' + ERROR.MIN_VALUE_ERROR
    }
    else if (!this.dataService.isInt(debitDetail.expenses_price)) {
      this.error = ERROR.VALID_INT_NUMBER + ' In Expenses Price'
    }
    else if (!this.dataService.isExist(debitDetail.trans_per)) {
      this.error = ERROR.TRANSPER_LENGTH
    }
    else if (debitDetail.trans_per < 0) {
      this.error = 'Transaction Percentage ' + ERROR.MIN_VALUE_ERROR
    }
    else if (debitDetail.trans_per > 100) {
      this.error = ERROR.INVALID_PERCENTAGE
    }
    else if (!this.dataService.isExist(debitDetail.coins)) {
      this.error = ERROR.COINS_LENGTH
    }
    else if (debitDetail.coins < 0) {
      this.error = 'Coins ' + ERROR.MIN_VALUE_ERROR
    }
    else if (!this.dataService.isInt(debitDetail.coins)) {
      this.error = ERROR.VALID_INT_NUMBER + ' In Coins'
    }
    else if (!this.dataService.isExist(debitDetail.amount)) {
      this.error = ERROR.AMOUNT_LENGTH
    }
    else if (debitDetail.amount < 0) {
      this.error = 'Amount ' + ERROR.MIN_VALUE_ERROR
    }
    else if (!this.dataService.isInt(debitDetail.amount)) {
      this.error = ERROR.VALID_INT_NUMBER + ' In Amount'
    }
    else if (!this.dataService.isExist(debitDetail.invite_amt)) {
      this.error = ERROR.INVITEAMOUNT_LENGTH
    }
    else if (debitDetail.invite_amt < 0) {
      this.error = 'Invite Amount ' + ERROR.MIN_VALUE_ERROR
    }
    else if (!this.dataService.isInt(debitDetail.invite_amt)) {
      this.error = ERROR.VALID_INT_NUMBER + ' In Invite Amount'
    }
    else {
      this.dataService.showLoader();
      this.dataService.postData('addDebitByAdmin', {
        expenses_no: debitDetail.expenses_no,
        expenses_name: debitDetail.expenses_name,
        expenses_price: debitDetail.expenses_price,
        trans_per: debitDetail.trans_per,
        coins: debitDetail.coins,
        amount: debitDetail.amount,
        invite_amt: debitDetail.invite_amt
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


  updateDebit(debitDetail:any) {
    if (!this.dataService.isExist(debitDetail)) {
      this.error = ERROR.EXPENSENO_LENGTH
    }
    else if (!this.dataService.isExist(debitDetail.expenses_no)) {
      this.error = ERROR.EXPENSENO_LENGTH
    }
    else if (!this.dataService.isExist(debitDetail.expenses_name)) {
      this.error = ERROR.EXPENSENAME_LENGTH
    }
    else if (!this.dataService.isExist(debitDetail.expenses_price)) {
      this.error = ERROR.EXPENSEPRICE_LENGTH
    }
    else if (debitDetail.expenses_price < 0) {
      this.error = 'Expenses Price ' + ERROR.MIN_VALUE_ERROR
    }
    else if (!this.dataService.isInt(debitDetail.expenses_price)) {
      this.error = ERROR.VALID_INT_NUMBER + ' In Expenses Price'
    }
    else if (!this.dataService.isExist(debitDetail.trans_per)) {
      this.error = ERROR.TRANSPER_LENGTH
    }
    else if (debitDetail.trans_per < 0) {
      this.error = 'Transaction Percentage ' + ERROR.MIN_VALUE_ERROR
    }
    else if (debitDetail.trans_per > 100) {
      this.error = ERROR.INVALID_PERCENTAGE
    }
    else if (!this.dataService.isExist(debitDetail.coins)) {
      this.error = ERROR.COINS_LENGTH
    }
    else if (debitDetail.coins < 0) {
      this.error = 'Coins ' + ERROR.MIN_VALUE_ERROR
    }
    else if (!this.dataService.isInt(debitDetail.coins)) {
      this.error = ERROR.VALID_INT_NUMBER + ' In Coins'
    }
    else if (!this.dataService.isExist(debitDetail.amount)) {
      this.error = ERROR.AMOUNT_LENGTH
    }
    else if (debitDetail.amount < 0) {
      this.error = 'Amount ' + ERROR.MIN_VALUE_ERROR
    }
    else if (!this.dataService.isInt(debitDetail.amount)) {
      this.error = ERROR.VALID_INT_NUMBER + ' In Amount'
    }
    else if (!this.dataService.isExist(debitDetail.invite_amt)) {
      this.error = ERROR.INVITEAMOUNT_LENGTH
    }
    else if (debitDetail.invite_amt < 0) {
      this.error = 'Invite Amount ' + ERROR.MIN_VALUE_ERROR
    }
    else if (!this.dataService.isInt(debitDetail.invite_amt)) {
      this.error = ERROR.VALID_INT_NUMBER + ' In Invite Amount'
    }
    else {
      this.dataService.showLoader();
      this.dataService.postData('updateDebitByAdmin', {
        debit_id: debitDetail.debit_id,
        expenses_no: debitDetail.expenses_no,
        expenses_name: debitDetail.expenses_name,
        expenses_price: debitDetail.expenses_price,
        trans_per: debitDetail.trans_per,
        coins: debitDetail.coins,
        amount: debitDetail.amount,
        invite_amt: debitDetail.invite_amt
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


  ReplaceValidNumber(data:any) {
    return data.replace(/[^0-9]/g, '');
  }


  close() {
    this.dialogRef.close(false);
  }
}
