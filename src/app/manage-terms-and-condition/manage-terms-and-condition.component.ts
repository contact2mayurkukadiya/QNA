import { Component, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { DataService } from '../data.service';
import { ERROR } from '../app.constants';
import { AddTermsAndConditionComponent } from '../add-terms-and-condition/add-terms-and-condition.component';

@Component({
  selector: 'app-manage-terms-and-condition',
  templateUrl: './manage-terms-and-condition.component.html',
  styleUrls: ['./manage-terms-and-condition.component.scss']
})
export class ManageTermsAndConditionComponent implements OnInit {

  perPage = [5, 15, 25, 50, 75, 100];
  per_page: any;
  sortField: any = 'update_time';
  order = 'DESC';
  page = 1;
  total_record: any;
  data: any = [];
  error: any = '';
  data_fetch_error: any = "";

  constructor(public dialog: MatDialog, public dataService: DataService) {
    this.per_page = this.perPage[0];
    this.getAllTnC();
  }

  ngOnInit() {
  }

  openModelForAddTnC() {
    let addRoundRef = this.dialog.open(AddTermsAndConditionComponent, {
      hasBackdrop: true,
      disableClose: true,
      panelClass: 'add-tnc-wrapper'
    })
    addRoundRef.afterClosed().toPromise().then(result => {
      if (result) {
        this.getAllTnC(this.per_page, this.sortField, this.order, this.page);
      }
    });
  }

  changeOrder(perpage, sortfield, order, pageno = 1) {
    this.sortField = sortfield;
    this.order = this.order == 'ASC' ? 'DESC' : 'ASC';
    this.getAllTnC(perpage, sortfield, this.order, pageno)
  }

  getAllTnC(perpage = this.perPage[0], sortfield = 'update_time', sorttype = 'DESC', pageno = 1) {
    this.page = pageno;
    this.data_fetch_error = null;
    this.dataService.showLoader();
    this.dataService.postData("getTermsNConditionsByAdmin",
      {
        "page": pageno,
        "item_count": perpage,
        "order_by": sortfield,
        "order_type": sorttype

      }, {
        headers: ({
          'Authorization': 'Bearer ' + localStorage.getItem('token')
        })
      }).then((result: any) => {
        if (result.code == 200) {
          this.dataService.hideLoader();
          this.total_record = result.data.total_items;
          if (result.data.result.length <= 0) {
            if (this.page == 1) {
              this.data_fetch_error = "No Data Found";
              this.data = [];
            } else {
              this.page -= 1;
              this.getAllTnC(perpage, sortfield, this.order, this.page)
            }
          }
          else if (result.data) {
            this.data = this.dataService.applyIndex(result.data.result, this.per_page, this.page);
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

  deleteTnC(term_n_condition_id) {
    this.dataService.askConfirmation('Delete Confirmation', 'Are you sure you want to delete Terms and Conditions ?', 'Yes', 'No').then(result => {
      if (result) {
        this.dataService.showLoader();
        this.dataService.postData('deleteTermsNConditionsByAdmin', {
          'term_n_condition_id': term_n_condition_id
        }, {
            headers: ({
              'Authorization': 'Bearer ' + localStorage.getItem('token')
            })
          }).then((result: any) => {
            if (result.code == 200) {
              this.dataService.hideLoader();
              this.getAllTnC(this.per_page, this.sortField, this.order, this.page);
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

  updateStatus(row, item) {
    this.dataService.showLoader();
    let isActive = 0;
    if (item == true) {
      isActive = 1;
    }
    else {
      isActive = 0;
    }
    this.dataService.postData('setStatusOfTermsNConditionsByAdmin', {
      'term_n_condition_id': row.term_n_condition_id,
      'is_active': isActive
    }, {
        headers: ({
          'Authorization': 'Bearer ' + localStorage.getItem('token')
        })
      }).then((result: any) => {
        if (result.code == 200) {
          this.dataService.hideLoader();
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

  openDialogForUpdate(row) {
    let addRoundRef = this.dialog.open(AddTermsAndConditionComponent, {
      hasBackdrop: true,
      disableClose: true,
      panelClass: 'add-tnc-wrapper',
      data: row
    })
    addRoundRef.afterClosed().toPromise().then(result => {
      if (result) {
        this.getAllTnC(this.per_page, this.sortField, this.order, this.page);
      }
    });
  }

}
