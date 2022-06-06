import { Component, OnInit, Inject } from '@angular/core';
import { DataService } from '../data.service';
import { ERROR } from '../app.constants';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';

@Component({
  selector: 'app-add-admin',
  templateUrl: './add-admin.component.html',
  styleUrls: ['./add-admin.component.scss']
})
export class AddAdminComponent implements OnInit {

  error: any = "";
  isUpdatingMode: any = false;
  adminDetail: any = {
    first_name: null,
    last_name: null,
    email_id: null,
    password: null,
    c_password: null,
    user_id: null
  }
  constructor(public dataService: DataService, public dialogRef: MatDialogRef<AddAdminComponent>, @Inject(MAT_DIALOG_DATA) private dialogData: any) {
    if (dialogData) {
      this.isUpdatingMode = true;
      this.adminDetail.first_name = dialogData.first_name;
      this.adminDetail.last_name = dialogData.last_name;
      this.adminDetail.email_id = dialogData.email_id;
      this.adminDetail.user_id = dialogData.user_id
    }
  }

  ngOnInit() {
  }

  addAdmin(adminDetail:any) {
    if (!this.dataService.isExist(adminDetail)) {
      this.error = ERROR.ADMINFIRSTNAME_LENGTH
    }
    else if (!this.dataService.isExist(adminDetail.first_name)) {
      this.error = ERROR.ADMINFIRSTNAME_LENGTH
    }
    else if (!this.dataService.isExist(adminDetail.last_name)) {
      this.error = ERROR.ADMINLASTNAME_LENGTH
    }
    else if (!this.dataService.isExist(adminDetail.email_id)) {
      this.error = ERROR.EMAIL_LENGTH
    }
    else if (!this.dataService.validateEmail(adminDetail.email_id)) {
      this.error = ERROR.VALID_EMAIL
    }
    else if (!this.dataService.isExist(adminDetail.password)) {
      this.error = ERROR.PASSWORD_LENGTH
    }
    else if (!this.dataService.isExist(adminDetail.c_password)) {
      this.error = ERROR.CONFIRM_PASSWORD_LENGTH
    }
    else if (adminDetail.c_password !== adminDetail.password) {
      this.error = ERROR.CONFIRM_PASSWORD_MATCH
    }
    else {
      this.dataService.showLoader();
      this.dataService.postData('adminRegisterByAdmin', {
        "first_name": adminDetail.first_name,
        "last_name": adminDetail.last_name,
        "email_id": adminDetail.email_id,
        "password": adminDetail.password
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


  updateAdmin(adminDetail:any) {
    if (!this.dataService.isExist(adminDetail)) {
      this.error = ERROR.ADMINFIRSTNAME_LENGTH
    }
    else if (!this.dataService.isExist(adminDetail.first_name)) {
      this.error = ERROR.ADMINFIRSTNAME_LENGTH
    }
    else if (!this.dataService.isExist(adminDetail.last_name)) {
      this.error = ERROR.ADMINLASTNAME_LENGTH
    }
    else if (!this.dataService.isExist(adminDetail.email_id)) {
      this.error = ERROR.EMAIL_LENGTH
    }
    else if (!this.dataService.validateEmail(adminDetail.email_id)) {
      this.error = ERROR.VALID_EMAIL
    }
    // else if (this.dataService.validateEmail(adminDetail.password)) {
    //   this.error = ERROR.PASSWORD_LENGTH
    // }
    // else if (this.dataService.validateEmail(adminDetail.c_password)) {
    //   this.error = ERROR.CONFIRM_PASSWORD_LENGTH
    // }
    // else if (adminDetail.c_password.trim() !== adminDetail.password.trim()) {
    //   this.error = ERROR.CONFIRM_PASSWORD_MATCH
    // }
    else {
      this.error = "";
      this.dataService.showLoader();
      this.dataService.postData('updateAdminData', {
        "email_id": adminDetail.email_id,
        "first_name": adminDetail.first_name,
        "last_name": adminDetail.last_name,
        "user_id": adminDetail.user_id
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
        });
    }
  }


  close() {
    this.dialogRef.close(false);
  }

}
