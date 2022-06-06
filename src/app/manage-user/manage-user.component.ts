import { Component, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { AddRoundComponent } from '../add-round/add-round.component';
import { DataService } from '../data.service';
import { ERROR } from '../app.constants';
import { HttpHeaders } from '@angular/common/http';
import { Router } from '@angular/router';

@Component({
  selector: 'app-manage-user',
  templateUrl: './manage-user.component.html',
  styleUrls: ['./manage-user.component.scss']
})
export class ManageUserComponent implements OnInit {

  perPage = [5, 15, 25, 50, 75, 100];
  per_page: any;
  sortField: any = 'update_time';
  order = 'DESC';
  page = 1;
  total_record: any;
  data: any = [];
  data_fetch_error: any = "";
  searchList: any = [
    {
      title: 'Please Choose',
      field: ''
    },
    {
      title: 'First Name',
      field: 'first_name'
    },
    {
      title: 'Last Name',
      field: 'last_name'
    }, {
      title: 'Email Id',
      field: 'email_id'
    }, {
      title: 'Phone Number',
      field: 'phone_no'
    }
  ]
  searchData: any = {
    searchType: this.searchList[0].field,
    searchQuery: ''
  }
  isSearching: any = false;

  constructor(public dialog: MatDialog, public dataService: DataService, public router: Router) {
    this.per_page = this.perPage[0];
    this.getAllUser();
  }

  ngOnInit() {
  }

  changeOrder(perpage, sortfield, order, pageno = 1) {
    this.sortField = sortfield;
    this.order = this.order == 'ASC' ? 'DESC' : 'ASC';
    this.isSearching = false;
    this.searchData = {
      searchType: this.searchList[0].field,
      searchQuery: ''
    }
    this.getAllUser(perpage, sortfield, this.order, pageno)
  }

  getAllUser(perpage = this.perPage[0], sortfield = 'update_time', sorttype = 'DESC', pageno = 1) {
    this.page = pageno;
    this.data_fetch_error = null;
    this.dataService.showLoader();
    this.dataService.postData("getAllUserForAdmin",
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
          this.total_record = result.data.total_record;
          if (result.data.user_detail.length <= 0) {
            if (this.page == 1) {
              this.data_fetch_error = "No Data Found";
              this.data = [];
            } else {
              this.page -= 1;
              this.getAllUser(perpage, sortfield, this.order, this.page)
            }
          }
          else if (result.data) {
            this.data = this.dataService.applyIndex(result.data.user_detail, this.per_page, this.page);
          }
        }
        else if (result.code == 201) {
          this.data_fetch_error = "No Data Found";
          this.dataService.hideLoader();
          this.dataService.showSnackBar(result.message, '', 3000, 'error');
        }
        else {
          this.data_fetch_error = "No Data Found";
          this.dataService.hideLoader();
          this.dataService.showSnackBar(ERROR.OFFLINE, '', 3000, 'error');
        }
      }).catch(err => {
        this.data_fetch_error = "No Data Found";
        this.dataService.hideLoader();
        this.dataService.showSnackBar(ERROR.OFFLINE, '', 3000, 'error');
      });
  }

  openChat(row) {
    this.router.navigate(['chat/', row.user_id, row.first_name]);
  }

  resetSearch() {
    this.isSearching = false;
    this.searchData = {
      searchType: this.searchList[0].field,
      searchQuery: ''
    }
    this.getAllUser();
  }

  searchUser(searchData) {
    if (typeof searchData === 'undefined' || searchData === null || searchData === '') {
      this.dataService.showSnackBar('Please Choose Search Type', '', 3000, 'error');
    }
    else if (typeof searchData.searchType === 'undefined' || searchData.searchType === null || searchData.searchType === '') {
      this.dataService.showSnackBar('Please Choose Search Type', '', 3000, 'error');
    }
    else if (typeof searchData.searchQuery === 'undefined' || searchData.searchQuery === null || searchData.searchQuery === '') {
      this.dataService.showSnackBar('Please Choose Search Query', '', 3000, 'error');
    }
    else {
      this.dataService.showLoader();
      this.dataService.postData("searchUserForAdmin",
        {
          "search_type": searchData.searchType,
          "search_query": searchData.searchQuery,
        }, {
          headers: ({
            'Authorization': 'Bearer ' + localStorage.getItem('token')
          })
        }).then((result: any) => {
          if (result.code == 200) {
            this.dataService.hideLoader();
            this.total_record = result.data.total_record;
            if (result.data.user_detail.length <= 0) {
              this.isSearching = true;
              this.data_fetch_error = "No Data Found";
              this.data = [];
            }
            else if (result.data) {
              this.isSearching = true;
              this.data_fetch_error = null;
              this.data = this.dataService.applyIndex(result.data.user_detail, this.per_page, this.page);
            }
          }
          else if (result.code == 201) {
            this.data_fetch_error = "No Data Found";
            this.dataService.hideLoader();
            this.dataService.showSnackBar(result.message, '', 3000, 'error');
          }
          else {
            this.data_fetch_error = "No Data Found";
            this.dataService.hideLoader();
            this.dataService.showSnackBar(ERROR.OFFLINE, '', 3000, 'error');
          }
        }).catch(err => {
          this.data_fetch_error = "No Data Found";
          this.dataService.hideLoader();
          this.dataService.showSnackBar(ERROR.OFFLINE, '', 3000, 'error');
        });
    }
  }
}
