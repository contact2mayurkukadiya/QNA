import { Component, OnInit, Inject } from '@angular/core';
import { ERROR } from '../app.constants';
import { DataService } from '../data.service';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';

@Component({
  selector: 'app-approve-coin-dialog',
  templateUrl: './approve-coin-dialog.component.html',
  styleUrls: ['./approve-coin-dialog.component.scss']
})
export class ApproveCoinDialogComponent implements OnInit {

  error: any = '';
  approve_coin: any = null;
  constructor(public dataService: DataService, public dialogRef: MatDialogRef<ApproveCoinDialogComponent>, @Inject(MAT_DIALOG_DATA) public dialogData: any) { }

  ngOnInit() {
  }

  makePayment(approve_coin) {
    if (!this.dataService.isExist(approve_coin)) {
      this.error = ERROR.APPROVE_COIN_INVALID
    }
    else if (approve_coin < 0) {
      this.error = ERROR.APPROVE_COIN_LOW
    }
    else if (!this.dataService.isInt(approve_coin)) {
      this.error = ERROR.VALID_INT_NUMBER + ' In Approved Coins'
    }
    else if (this.dialogData.row && approve_coin > this.dialogData.row.request_coin) {
      this.error = ERROR.INVALID_APPROVE_COIN
    }
    else {
      this.dialogRef.close({
        coin: approve_coin
      });
    }
  }

  ReplaceValidNumber(data) {
    return data.replace(/[^0-9]/g, '');
  }


  close() {
    this.dialogRef.close(false);
  }

}
