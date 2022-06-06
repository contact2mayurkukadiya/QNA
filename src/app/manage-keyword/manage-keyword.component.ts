import { Component, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { DataService } from '../data.service';
import { ERROR } from '../app.constants';
import { AddKeywordComponent } from '../add-keyword/add-keyword.component';

@Component({
  selector: 'app-manage-keyword',
  templateUrl: './manage-keyword.component.html',
  styleUrls: ['./manage-keyword.component.scss']
})
export class ManageKeywordComponent implements OnInit {

  sortField: any = 'update_time';
  order = 'DESC';
  total_record: any;
  data: any = [];
  error: any = '';
  data_fetch_error: any = "";
  updatingRoundId: any;
  RowDataForUpdateCancelRef: any = {};

  constructor(public dialog: MatDialog, public dataService: DataService) {
    this.getAllKeyword();
  }

  ngOnInit() {
  }

  openModelForAddKeyword() {
    let addRoundRef = this.dialog.open(AddKeywordComponent, {
      hasBackdrop: true,
      disableClose: true,
      panelClass: 'add-keyword-wrapper'
    })
    addRoundRef.afterClosed().toPromise().then(result => {
      if (result) {
        this.getAllKeyword(this.sortField, this.order);
      }
    });
  }

  openModelForUpdateKeyword(row: any = null) {
    let addRoundRef = this.dialog.open(AddKeywordComponent, {
      hasBackdrop: true,
      disableClose: true,
      panelClass: 'add-keyword-wrapper',
      data: row
    })
    addRoundRef.afterClosed().toPromise().then(result => {
      if (result) {
        this.getAllKeyword(this.sortField, this.order);
      }
    });
  }

  changeOrder(sortfield, order) {
    this.sortField = sortfield;
    this.order = this.order == 'ASC' ? 'DESC' : 'ASC';
    this.getAllKeyword(sortfield, this.order)
  }

  getAllKeyword(sortfield = 'update_time', sorttype = 'DESC') {
    this.data_fetch_error = null;
    this.dataService.showLoader();
    this.dataService.postData("getKeywordByAdmin",
      {
        "page": 0,
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
            this.data = this.dataService.applyIndex(result.data.result, 0, 1);
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

  deleteKeyword(keyword_id) {
    this.dataService.askConfirmation('Delete Confirmation', 'Are you sure you want to delete this notification ?', 'Yes', 'No').then(result => {
      if (result) {
        this.dataService.showLoader();
        this.dataService.postData('deleteKeywordByAdmin', {
          'keyword_id': keyword_id
        }, {
            headers: ({
              'Authorization': 'Bearer ' + localStorage.getItem('token')
            })
          }).then((result: any) => {
            if (result.code == 200) {
              this.dataService.hideLoader();
              this.getAllKeyword(this.sortField, this.order);
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
}
