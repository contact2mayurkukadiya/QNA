import { Component, OnInit } from '@angular/core';
import { DataService } from '../data.service';
import { MatDialog } from '@angular/material/dialog';
import { ERROR } from '../app.constants';
import { AddDebitComponent } from '../add-debit/add-debit.component';

@Component({
  selector: 'app-manage-debit',
  templateUrl: './manage-debit.component.html',
  styleUrls: ['./manage-debit.component.scss']
})
export class ManageDebitComponent implements OnInit {

  sortField: any = 'update_time';
  order = 'DESC';
  total_record: any;
  data: any = [];
  error: any = '';
  data_fetch_error: any = "";
  updatingRoundId: any;
  RowDataForUpdateCancelRef: any = {};

  constructor(public dialog: MatDialog, public dataService: DataService) {
    this.getAllDebit();
  }

  ngOnInit() {
  }

  openModelForAddDebit() {
    let addDebitRef = this.dialog.open(AddDebitComponent, {
      hasBackdrop: true,
      disableClose: true,
      panelClass: 'add-debit-wrapper'
    })
    addDebitRef.afterClosed().toPromise().then(result => {
      if (result) {
        this.getAllDebit(this.sortField, this.order);
      }
    });
  }

  changeOrder(sortfield, order) {
    this.sortField = sortfield;
    this.order = this.order == 'ASC' ? 'DESC' : 'ASC';
    this.getAllDebit(sortfield, this.order)
  }

  getAllDebit(sortfield = 'update_time', sorttype = 'DESC') {
    this.data_fetch_error = null;
    this.dataService.showLoader();
    this.dataService.postData("getDebitByAdmin",
      {
        "page": 0,   //there is no pagination in this table
        "item_count": 0,
        "order_by": sortfield,
        "order_type": sorttype

      }, {
        headers: ({
          'Authorization': 'Bearer ' + localStorage.getItem('token')
        })
      }).then((result: any) => {
        if (result.code == 200) {
          this.dataService.hideLoader();
          this.total_record = result.data.total_round;
          if (result.data.result.length <= 0) {
            this.data_fetch_error = "No Data Found";
            this.data = [];
          }
          else if (result.data) {
            this.data = this.dataService.applyIndex(result.data.result, 0, 0);
          }
        }
        else if (result.code == 201) {
          this.data_fetch_error = "No Data Found"
          this.dataService.hideLoader();
          this.dataService.showSnackBar(result.message, '', 3000, 'error');
        }
        else {
          this.data_fetch_error = "No Data Found"
          this.dataService.hideLoader();
          this.dataService.showSnackBar(ERROR.OFFLINE, '', 3000, 'error');
        }
      }).catch(err => {
        this.data_fetch_error = "No Data Found"
        this.dataService.hideLoader();
        this.dataService.showSnackBar(ERROR.OFFLINE, '', 3000, 'error');
      });
  }

  deleteDebitDetail(debit_id) {
    this.dataService.askConfirmation('Delete Confirmation', 'Are you sure you want to delete Debit Detail ?', 'Yes', 'No').then(result => {
      if (result) {
        this.dataService.showLoader();
        this.dataService.postData('deleteDebitByAdmin', {
          'debit_id': debit_id
        }, {
            headers: ({
              'Authorization': 'Bearer ' + localStorage.getItem('token')
            })
          }).then((result: any) => {
            if (result.code == 200) {
              this.dataService.hideLoader();
              this.getAllDebit(this.sortField, this.order);
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
            console.log('Error', err);
            this.dataService.hideLoader();
            this.dataService.showSnackBar(ERROR.OFFLINE, '', 3000, 'error');
          });
      }
      else { }
    });
  }

  openModelForUpdateDebit(row) {
    let addDebitRef = this.dialog.open(AddDebitComponent, {
      hasBackdrop: true,
      disableClose: true,
      panelClass: 'add-debit-wrapper',
      data: row
    })
    addDebitRef.afterClosed().toPromise().then(result => {
      if (result) {
        this.getAllDebit(this.sortField, this.order);
      }
    });
  }

}
