import { Component, OnInit } from '@angular/core';
import { DataService } from '../data.service';

@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.scss']
})
export class HeaderComponent implements OnInit {

  constructor(public dataService: DataService) { }

  ngOnInit() {
  }

  doLogout() {
    this.dataService.showLoader();
    this.dataService.postData("doLogout",
      {}, {
        headers: ({
          'Authorization': 'Bearer ' + localStorage.getItem('token')
        })
      }).then((result: any) => {
        this.dataService.hideLoader();
        localStorage.clear();
        this.dataService.navigate('login');
      }).catch(err => {
        this.dataService.hideLoader();
        localStorage.clear();
        this.dataService.navigate('login');
      });
  }
}
