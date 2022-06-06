import { Component, OnInit } from '@angular/core';
import { ERROR } from '../app.constants';
import { MatDialog } from '@angular/material/dialog';
import { DataService } from '../data.service';
import { ApproveCoinDialogComponent } from '../approve-coin-dialog/approve-coin-dialog.component';

@Component({
  selector: 'app-manage-expense',
  templateUrl: './manage-expense.component.html',
  styleUrls: ['./manage-expense.component.scss']
})
export class ManageExpenseComponent implements OnInit {

  perPage = [5, 15, 25, 50, 75, 100];
  per_page: any;
  sortField: any = 'update_time';
  order = 'DESC';
  page = 1;
  total_record: any;
  data: any = [];
  error: any = '';
  data_fetch_error: any = "";

  downloadData: any = {
    min_amt: null,
    max_amt: null
  };

  statusList: any = [
    { title: 'Pending', value: 0 },
    { title: 'Success', value: 1 },
    { title: 'Return', value: 2 },
    { title: 'Cancel', value: 3 }
  ];

  activeStatus: any = this.statusList[0].value;

  constructor(public dialog: MatDialog, public dataService: DataService) {
    this.per_page = this.perPage[0];
    this.getAllExpense();
  }

  ngOnInit() {
  }

  changeOrder(perpage, sortfield, order, pageno = 1) {
    this.sortField = sortfield;
    this.order = this.order == 'ASC' ? 'DESC' : 'ASC';
    this.getAllExpense(perpage, sortfield, this.order, pageno)
  }

  getAllExpense(perpage = this.perPage[0], sortfield = 'update_time', sorttype = 'DESC', pageno = 1) {
    this.page = pageno;
    this.per_page = perpage;
    this.data_fetch_error = null;

    let payload = {
      "page": pageno,
      "item_count": perpage,
      "order_by": sortfield,
      "order_type": sorttype,
      "skuname": 'CASH_CREDIT',
      "status": this.activeStatus
    }
    if (typeof this.downloadData.min_amt != 'undefined' && this.downloadData.min_amt != null && this.downloadData.min_amt != '') {
      if (this.downloadData.min_amt < 0) {
        this.dataService.showSnackBar(ERROR.MIN_AMT_MIN, '', 3000, 'error');
        return;
      }
      else if (!this.dataService.isInt(this.downloadData.min_amt)) {
        this.dataService.showSnackBar(ERROR.VALID_INT_NUMBER + 'In Minimum Amount.', '', 3000, 'error');
        return;
      }
      else {
        payload['min_amt'] = this.downloadData.min_amt
      }
    }
    if (typeof this.downloadData.max_amt != 'undefined' && this.downloadData.max_amt != null && this.downloadData.max_amt != '') {
      if (this.downloadData.max_amt < 0) {
        this.dataService.showSnackBar(ERROR.MAX_AMT_MIN, '', 3000, 'error');
        return;
      }
      else if (!this.dataService.isInt(this.downloadData.max_amt)) {
        this.dataService.showSnackBar(ERROR.VALID_INT_NUMBER + 'In Maximum Amount.', '', 3000, 'error');
        return;
      }
      else {
        payload['max_amt'] = this.downloadData.max_amt
      }
    }
    this.dataService.showLoader();
    this.dataService.postData("getExpenseDetailByAdmin",
      payload, {
        headers: ({
          'Authorization': 'Bearer ' + localStorage.getItem('token')
        })
      }).then((result: any) => {
        if (result.code == 200) {
          this.dataService.hideLoader();
          this.total_record = result.data.total_items;

          result.data.result = result.data.result;

          if (result.data.result.length <= 0) {
            if (this.page == 1) {
              this.data_fetch_error = "No Data Found";
              this.data = [];
            } else {
              this.page -= 1;
              this.getAllExpense(perpage, sortfield, this.order, this.page)
            }
          }
          else if (result.data) {
            this.data = this.dataService.applyIndex(result.data.result, this.per_page, this.page);
          }
        }
        else if (result.code == 201) {
          this.data_fetch_error = "No Data Found";
          this.data = [];
          this.dataService.hideLoader();
          this.dataService.showSnackBar(result.message, '', 3000, 'error');
        }
        else {
          this.data_fetch_error = "No Data Found";
          this.data = [];
          this.dataService.hideLoader();
          this.dataService.showSnackBar(ERROR.OFFLINE, '', 3000, 'error');
        }
      }).catch(err => {
        this.data_fetch_error = "No Data Found";
        this.data = [];
        this.dataService.hideLoader();
        this.dataService.showSnackBar(ERROR.OFFLINE, '', 3000, 'error');
      });
  }

  makePayment(row, status) {
    if (status == 1) {
      let dialog = this.dialog.open(ApproveCoinDialogComponent, {
        hasBackdrop: true,
        disableClose: true,
        data: {
          row: row,
          status: status
        }
      });
      dialog.afterClosed().subscribe(result => {
        if (result != false) {
          this.pay({
            "user_id": row.user_id,
            "expense_id": row.expense_id,
            "request_coin": row.request_coin,
            "approve_coin": result.coin,
            "status": status,
          })
        }
      })
    } else {
      this.pay({
        "user_id": row.user_id,
        "expense_id": row.expense_id,
        "request_coin": row.request_coin,
        "status": status,
      })
    }
  }

  pay(data) {
    this.dataService.showLoader();
    this.dataService.postData("payRSFromByAdmin", data, {
      headers: ({
        'Authorization': 'Bearer ' + localStorage.getItem('token')
      })
    }).then((result: any) => {
      if (result.code == 200) {
        this.dataService.hideLoader();
        this.dataService.showSnackBar(result.message, '', 3000, 'success');
        this.getAllExpense(this.per_page, this.sortField, this.order, this.page);
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
    });
  }

  exportDetail() {
    let payload = {
      "page": this.page,
      "item_count": this.per_page,
      "skuname": 'CASH_CREDIT',
      "status": this.activeStatus
    }
    if (typeof this.downloadData.min_amt != 'undefined' && this.downloadData.min_amt != null && this.downloadData.min_amt != '') {
      if (this.downloadData.min_amt < 0) {
        this.dataService.showSnackBar(ERROR.MIN_AMT_MIN, '', 3000, 'error');
        return;
      }
      else if (!this.dataService.isInt(this.downloadData.min_amt)) {
        this.dataService.showSnackBar(ERROR.VALID_INT_NUMBER + 'In Minimum Amount.', '', 3000, 'error');
        return;
      }
      else {
        payload['min_amt'] = this.downloadData.min_amt
      }
    }
    if (typeof this.downloadData.max_amt != 'undefined' && this.downloadData.max_amt != null && this.downloadData.max_amt != '') {
      if (this.downloadData.max_amt < 0) {
        this.dataService.showSnackBar(ERROR.MAX_AMT_MIN, '', 3000, 'error');
        return;
      }
      else if (!this.dataService.isInt(this.downloadData.max_amt)) {
        this.dataService.showSnackBar(ERROR.VALID_INT_NUMBER + 'In Maximum Amount.', '', 3000, 'error');
        return;
      }
      else {
        payload['max_amt'] = this.downloadData.max_amt
      }
    }
    this.dataService.showLoader();
    this.dataService.postData("exportExpenseDetailByAdmin", payload, {
      headers: ({
        'Authorization': 'Bearer ' + localStorage.getItem('token')
      })
    }).then((result: any) => {
      if (result.code == 200) {
        this.dataService.hideLoader();
        window.open(result.data.result);
        this.dataService.showSnackBar('Expense Exported Successfully', '', 3000, 'success');
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
    });
  }
}
