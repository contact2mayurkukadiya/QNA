import { Component, OnInit } from '@angular/core';
import { ERROR } from '../app.constants';
import { DataService } from '../data.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent implements OnInit {

  adminDetail: any = {
    email: '',
    password: ''
  }
  errorMessage: any = "";
  token: any;

  constructor(public router: Router, public dataService: DataService) {
    // this.router.events.subscribe(event => {
    //   if (event instanceof NavigationEnd) {
    //     ga('set', 'page', event.urlAfterRedirects);
    //     ga('send', 'pageview');
    //   }
    // });
    
    this.token = localStorage.getItem('token');
    if (typeof this.token != 'undefined' && this.token != null && this.token != '') {
      this.dataService.navigate('dashboard');
    }
  }

  ngOnInit() {
  }

  doAdminLogin(detail) {
    if (typeof detail == 'undefined' || detail == null || detail == '') {
      this.errorMessage = ERROR.EMAIL_LENGTH
    }
    else if (typeof detail.email == 'undefined' || detail.email == null || detail.email == '') {
      this.errorMessage = ERROR.EMAIL_LENGTH
    }
    else if (!this.dataService.validateEmail(detail.email)) {
      this.errorMessage = ERROR.VALID_EMAIL
    }
    else if (typeof detail.password == 'undefined' || detail.password == null || detail.password == '') {
      this.errorMessage = ERROR.PASSWORD_LENGTH
    }
    else {
      this.errorMessage = "";
      this.dataService.showLoader();
      this.dataService.postData('doLoginForAdmin', {
        email_id: detail.email,
        password: detail.password
      }, {}).then((result: any) => {
        if (result.code == 200) {
          this.dataService.hideLoader();
          this.dataService.showSnackBar(result.message, '', 3000, 'success');
          localStorage.setItem('token', result.data.token);
          localStorage.setItem('a_d', JSON.stringify(result.data.user_detail));
          this.dataService.navigate('dashboard');
        }
        else if (result.code == 201) {
          this.dataService.hideLoader();
          this.dataService.showSnackBar(result.message, '', 5000, 'error');
        }
        else {
          this.dataService.hideLoader();
          this.dataService.showSnackBar(ERROR.OFFLINE, '', 5000, 'error');
        }
      }).catch(err => {
        console.log('Error', err);
        this.dataService.hideLoader();
        this.dataService.showSnackBar(ERROR.OFFLINE, '', 5000, 'error');
      });
    }
  }
}
