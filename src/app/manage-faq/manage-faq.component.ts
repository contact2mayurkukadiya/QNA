import { Component, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { DataService } from '../data.service';
import { ERROR } from '../app.constants';
import { AddFaqComponent } from '../add-faq/add-faq.component';

@Component({
  selector: 'app-manage-faq',
  templateUrl: './manage-faq.component.html',
  styleUrls: ['./manage-faq.component.scss']
})
export class ManageFaqComponent implements OnInit {

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
    this.getAllFaq();
  }

  ngOnInit() {
  }

  openModelForAddFaq() {
    let addRoundRef = this.dialog.open(AddFaqComponent, {
      hasBackdrop: true,
      disableClose: true,
      panelClass: 'add-faq-wrapper'
    })
    addRoundRef.afterClosed().toPromise().then(result => {
      if (result) {
        this.getAllFaq(this.per_page, this.sortField, this.order, this.page);
      }
    });
  }

  changeOrder(perpage, sortfield, order, pageno = 1) {
    this.sortField = sortfield;
    this.order = this.order == 'ASC' ? 'DESC' : 'ASC';
    this.getAllFaq(perpage, sortfield, this.order, pageno)
  }

  getAllFaq(perpage = this.perPage[0], sortfield = 'update_time', sorttype = 'DESC', pageno = 1) {
    this.page = pageno;
    this.data_fetch_error = null;
    this.dataService.showLoader();
    this.dataService.postData("getFAQByAdmin",
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
          this.total_record = result.data.total_round;
          if (result.data.result.length <= 0) {
            if (this.page == 1) {
              this.data_fetch_error = "No Data Found"
              this.data = [];
            } else {
              this.page -= 1;
              this.getAllFaq(perpage, sortfield, this.order, this.page)
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

  deleteFaq(faq_id) {
    this.dataService.askConfirmation('Delete Confirmation', 'Are you sure you want to delete this FAQ ?', 'Yes', 'No').then(result => {
      if (result) {
        this.dataService.showLoader();
        this.dataService.postData('deleteFAQByAdmin', {
          'faq_id': faq_id
        }, {
            headers: ({
              'Authorization': 'Bearer ' + localStorage.getItem('token')
            })
          }).then((result: any) => {
            if (result.code == 200) {
              this.dataService.hideLoader();
              this.getAllFaq(this.per_page, this.sortField, this.order, this.page);
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
    this.dataService.postData('setStatusOfFAQByAdmin', {
      'faq_id': row.faq_id,
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
    let addRoundRef = this.dialog.open(AddFaqComponent, {
      hasBackdrop: true,
      disableClose: true,
      panelClass: 'add-faq-wrapper',
      data: row
    })
    addRoundRef.afterClosed().toPromise().then(result => {
      if (result) {
        this.getAllFaq(this.per_page, this.sortField, this.order, this.page);
      }
    });
  }
}
