import { Component, OnInit } from '@angular/core';
import { DataService } from '../data.service';
import { ERROR } from '../app.constants';

@Component({
  selector: 'app-manage-redis',
  templateUrl: './manage-redis.component.html',
  styleUrls: ['./manage-redis.component.scss']
})
export class ManageRedisComponent implements OnInit {

  data_fetch_error: any = '';
  data: any = [];

  constructor(public dataService: DataService) {
    this.getAllKey();
  }

  ngOnInit() {
  }

  getAllKey() {
    this.data = [];
    this.data_fetch_error = null;
    this.dataService.showLoader();
    this.dataService.postData("getRedisKeys", {}, {
      headers: ({
        'Authorization': 'Bearer ' + localStorage.getItem('token')
      })
    }).then((result: any) => {
      if (result.code == 200) {
        this.dataService.hideLoader();
        if (result.data.keys_list.length <= 0) {
          this.data_fetch_error = "No Data Found"
        }
        else if (result.data) {
          // this.data = result.data.keys_list;
          result.data.keys_list.forEach(element => {
            this.data.push({ key: element, isSelected: false });
          });

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

  selectAll() {
    if (this.data) {
      this.data.forEach(element => {
        element.isSelected = true;
      });
    }
  }

  deleteKeys(data) {
    this.dataService.showLoader();
    let payload = [];
    data.forEach(element => {
      if (element.isSelected == true) {
        payload.push({ key: element.key });
      }
    });
    this.dataService.postData("deleteRedisKeys", { 'keys_list': payload }, {
      headers: ({
        'Authorization': 'Bearer ' + localStorage.getItem('token')
      })
    }).then((result: any) => {
      if (result.code == 200) {
        this.dataService.hideLoader();
        this.dataService.showSnackBar(result.message, '', 3000, 'success');
        this.getAllKey();
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
