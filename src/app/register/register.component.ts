import { Component, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { DataService } from '../data.service';
import { AddAdminComponent } from '../add-admin/add-admin.component';
import { ERROR } from '../app.constants';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.scss']
})
export class RegisterComponent implements OnInit {

  total_record: any;
  data: any = [];
  error: any = '';
  data_fetch_error: any = "";

  constructor(public dialog: MatDialog, public dataService: DataService) {
    this.getAllAdmin();
  }

  ngOnInit() {
  }

  openModelForAddAdmin() {
    let addRoundRef = this.dialog.open(AddAdminComponent, {
      hasBackdrop: true,
      disableClose: true,
      panelClass: 'add-admin-wrapper'
    })
    addRoundRef.afterClosed().toPromise().then(result => {
      if (result) {
        this.getAllAdmin();
      }
    });
  }

  getAllAdmin() {
    this.data_fetch_error = null;
    this.dataService.showLoader();
    this.dataService.postData("getAdminData",
      {}, {
        headers: ({
          'Authorization': 'Bearer ' + localStorage.getItem('token')
        })
      }).then((result: any) => {
        if (result.code == 200) {
          this.dataService.hideLoader();
          this.total_record = result.data.total_round;
          if (result.data.result.length <= 0) {
            this.data_fetch_error = "No Data Found"
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

  updateStatus(row, item) {
    this.dataService.showLoader();
    let isActive = 0;
    if (item == true) {
      isActive = 1;
    }
    else {
      isActive = 0;
    }
    this.dataService.postData('setAdminStatus', {
      'user_id': row.user_id,
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
    let addRoundRef = this.dialog.open(AddAdminComponent, {
      hasBackdrop: true,
      disableClose: true,
      panelClass: 'add-admin-wrapper',
      data: row
    })
    addRoundRef.afterClosed().toPromise().then(result => {
      if (result) {
        if (row.user_id === JSON.parse(localStorage.getItem('a_d')).user_id) {
          this.dataService.showSnackBar('You have been logged out due to email id changes. Please login again', '', 3000, 'success');
          localStorage.clear();
          this.dataService.navigate('login');
        }
        else {
          this.getAllAdmin();
        }
      }
    });
  }
}
