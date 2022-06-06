import { Component, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { DataService } from '../data.service';
import { ERROR } from '../app.constants';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-manage-contact-us',
  templateUrl: './manage-contact-us.component.html',
  styleUrls: ['./manage-contact-us.component.scss']
})
export class ManageContactUsComponent implements OnInit {

  // manage round section variable 
  perPage = [5, 15, 25, 50, 75, 100];
  per_page: any;
  page = 1;
  total_record: any;
  data: any = [];
  data_fetch_error: any = "";
  /* userDetail: any = {
    user_name: '',
    user_id: ''
  } */

  updatingRowId: any = null;
  updatingContactId: any = '';
  RowDataForUpdateCancelRef: any = {};

  constructor(public dialog: MatDialog, public dataService: DataService, public route: ActivatedRoute) {
    this.per_page = this.perPage[2];
    /*  this.route.params.subscribe(params => {
       this.userDetail.user_id = params.user_id,
         this.userDetail.user_name = params.user_name
     }).unsubscribe(); */
    this.getAllChat();
  }

  ngOnInit() {
  }

  isEmptyJSON(obj) {
    for (var key in obj) {
      if (obj.hasOwnProperty(key))
        return false;
    }
    return true;
  }

  getAllChat(perpage = this.perPage[2], pageno = 1) {
    this.page = pageno;
    this.data_fetch_error = null;
    this.dataService.showLoader();
    this.dataService.postData("getContactDetailByAdmin",
      {
        "page": pageno,
        "item_count": perpage,
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
              this.getAllChat(perpage, this.page)
            }
          } else {
            this.data = this.dataService.applyIndex(result.data.result, this.per_page, this.page);
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
        console.log('error', err);
        this.data_fetch_error = "No Data Found";
        this.dataService.hideLoader();
        this.dataService.showSnackBar(ERROR.OFFLINE, '', 3000, 'error');
      });
  }

  back() {
    this.dataService.navigate('user');
  }

  sendMessage(row) {
    if (typeof row == 'undefined' || row == null || row == '') {
      this.dataService.showSnackBar(ERROR.MESSAGELENGTH, '', 3000, 'error');
    }
    else if (typeof row.answer.trim() == 'undefined' || row.answer.trim() == null || row.answer.trim() == '') {
      this.dataService.showSnackBar(ERROR.MESSAGELENGTH, '', 3000, 'error');
    }
    else {
      this.dataService.showLoader();
      this.dataService.postData("replayToContactByAdmin",
        {
          "answer": row.answer,
          'sender_user_id': row.sender_user_id,   // user id
          'contact_id': row.contact_id
        }, {
          headers: ({
            'Authorization': 'Bearer ' + localStorage.getItem('token')
          })
        }).then((result: any) => {
          if (result.code == 200) {
            this.dataService.hideLoader();
            this.ExitUpdating();
            this.getAllChat();
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
          console.log('error', err);
          this.data_fetch_error = "No Data Found";
          this.dataService.hideLoader();
          this.dataService.showSnackBar(ERROR.OFFLINE, '', 3000, 'error');
        });
    }
  }

  deleteContact(contact_id) {
    if (contact_id) {
      this.dataService.askConfirmation('Delete confirmation', 'Are you sure you want to delete this message ?', 'Yes', 'No').then(result => {
        if (result) {
          this.dataService.showLoader();
          this.dataService.postData("deleteContactByAdmin",
            {
              "contact_id": contact_id,
            }, {
              headers: ({
                'Authorization': 'Bearer ' + localStorage.getItem('token')
              })
            }).then((result: any) => {
              if (result.code == 200) {
                this.dataService.hideLoader();
                this.getAllChat(this.per_page, this.page);
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
              console.log('error', err);
              this.data_fetch_error = "No Data Found";
              this.dataService.hideLoader();
              this.dataService.showSnackBar(ERROR.OFFLINE, '', 3000, 'error');
            });
        }
      });
    }
  }

  enterUpdating(row, i = null) {
    if (this.updatingRowId != null) {
      // exit updating if any updating mode is on.
      this.data[this.updatingRowId] = this.RowDataForUpdateCancelRef;
      this.updatingContactId = "";
    }
    this.updatingRowId = i;
    this.updatingContactId = row.contact_id;
    this.RowDataForUpdateCancelRef = JSON.parse(JSON.stringify(row));
  }

  ExitUpdating(i = null) {
    if (i != null) {
      // replace original data i.e stored while entering into updating mode.
      this.data[i] = this.RowDataForUpdateCancelRef;
    }
    this.updatingRowId = null;
    this.updatingContactId = "";
  }

}
