import { Component, OnInit } from '@angular/core';
import { ERROR } from '../app.constants';
import { DataService } from '../data.service';

@Component({
  selector: 'app-setting',
  templateUrl: './setting.component.html',
  styleUrls: ['./setting.component.scss']
})
export class SettingComponent implements OnInit {

  oldPassword: any = "";
  newPassword: any = "";
  confirmPassword: any = "";

  error: any = "";
  token: any = "";
  constructor(public dataService: DataService) {
    this.token = localStorage.getItem('token');
  }

  ngOnInit() {
  }

  changePassword(oldPassword, newPassword, confirmPassword) {
    if (typeof oldPassword == 'undefined' || oldPassword == null || oldPassword == '') {
      this.error = ERROR.OLD_PASSWORD_LENGTH;
    }
    else if (typeof newPassword == 'undefined' || newPassword == null || newPassword == '') {
      this.error = ERROR.NEW_PASSWORD_LENGTH;
    }
    else if (typeof confirmPassword == 'undefined' || confirmPassword == null || confirmPassword == '') {
      this.error = ERROR.CONFIRM_PASSWORD_LENGTH;
    }
    else if (newPassword != confirmPassword) {
      this.error = ERROR.CONFIRM_PASSWORD_MATCH;
    }
    else {
      this.dataService.showLoader();
      this.dataService.postData('changePassword', {
        'current_password': oldPassword,
        'new_password': newPassword
      }, {
          headers: ({
            'Authorization': 'Bearer ' + localStorage.getItem('token')
          })
        }).then((result: any) => {
          if (result.code == 200) {
            this.dataService.hideLoader();
            this.dataService.showSnackBar(result.message, '', 3000, 'success');
            if (result.data) {
              localStorage.setItem('token', result.data.token);
            }
          }
          else if (result.code == 201) {
            this.dataService.hideLoader();
            this.dataService.showSnackBar(result.message, '', 3000, 'error');
          }
          else {
            this.dataService.hideLoader();
            this.dataService.showSnackBar(ERROR.OFFLINE, '', 3000, 'error');
          }
        }).catch((err: any) => {
          console.log('Error', err);
          this.dataService.hideLoader();
          this.dataService.showSnackBar(ERROR.OFFLINE, '', 3000, 'error');
        });
    }
  }
}
