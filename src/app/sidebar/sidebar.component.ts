import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-sidebar',
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.scss']
})
export class SidebarComponent implements OnInit {

  isSetting: any = false;
  isUser: any = false;
  isContact: any = false;
  isRedis: any = false;
  isFaq: any = false;
  isNotification: any = false;
  isKeyword: any = false;
  isTnC: any = false;
  isRegister: any = false;
  isDebit: any = false;
  isExpense: any = false;
  isDashboard: any = true;
  round_id: any = '';
  user_id: any = '';
  user_name: any = '';
  url: any;

  constructor(public router: Router, private activatedRoute: ActivatedRoute) {
    this.activatedRoute.params.subscribe(params => {
      this.round_id = params['round_id'];
      this.user_id = params['user_id'];
      this.user_name = params['user_name'];
    });
  }

  ngOnInit() {
    this.url = this.router.url;
    this.isDashboard = false;
    this.isSetting = false;
    this.isUser = false;
    this.isContact = false;
    this.isFaq = false;
    this.isNotification = false;
    this.isKeyword = false;
    this.isTnC = false;
    this.isRedis = false;
    this.isRegister = false;
    this.isDebit = false;
    this.isExpense = false;

    switch (this.url) {
      case '/dashboard':
      case '/dashboard/' + this.round_id:
        this.isDashboard = true;
        break;
      case '/setting':
        this.isSetting = true;
        break;
      case '/user':
        this.isUser = true;
        break;
      case '/contact':
        this.isContact = true;
        break;
      case '/faq':
        this.isFaq = true;
        break;
      case '/notification':
        this.isNotification = true;
        break;
      case '/keyword':
        this.isKeyword = true;
        break;
      case '/tnc':
        this.isTnC = true;
        break;
      case '/redis':
        this.isRedis = true;
        break;
      case '/register':
        this.isRegister = true;
        break;
      case '/debit':
        this.isDebit = true;
        break;
      case '/expense':
        this.isExpense = true;
        break;
    }
  }

  navigate(url) {
    this.router.navigate([url]);
  }
}
