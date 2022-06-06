import { Component, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { AddRoundComponent } from '../add-round/add-round.component';
import { DataService } from '../data.service';
import { ERROR } from '../app.constants';
import { HttpHeaders } from '@angular/common/http';

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss']
})
export class DashboardComponent implements OnInit {

  // manage round section variable 
  perPage = [5, 15, 25, 50, 75, 100];
  per_page: any;
  sortField: any = 'update_time';
  order = 'DESC';
  page = 1;
  total_record: any;
  data: any = [];
  error: any = '';
  data_fetch_error: any = "";
  updatingRoundId: any;
  RowDataForUpdateCancelRef: any = {};


  constructor(public dialog: MatDialog, public dataService: DataService) {
    this.per_page = this.perPage[0];
    this.getAllRound();
  }

  ngOnInit() {
  }

  // mmanage round section start from here

  openModelForAddRound() {
    let addRoundRef = this.dialog.open(AddRoundComponent, {
      hasBackdrop: true,
      disableClose: true,
      panelClass: 'add-round-wrapper'
    })
    addRoundRef.afterClosed().toPromise().then(result => {
      if (result) {
        this.getAllRound(this.per_page, this.sortField, this.order, this.page);
      }
    });
  }

  changeOrder(perpage, sortfield, order, pageno = 1) {
    this.sortField = sortfield;
    this.order = this.order == 'ASC' ? 'DESC' : 'ASC';
    this.getAllRound(perpage, sortfield, this.order, pageno)
  }

  getAllRound(perpage = this.perPage[0], sortfield = 'update_time', sorttype = 'DESC', pageno = 1) {
    this.page = pageno;
    this.data_fetch_error = null;
    this.dataService.showLoader();
    this.dataService.postData("getRoundDetailByAdmin",
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
              this.data_fetch_error = "No Data Found";
              this.data = [];
            }
            else {
              this.page -= 1;
              this.getAllRound(perpage, sortfield, this.order, this.page)
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

  deleteRound(round_id) {
    this.dataService.askConfirmation('Delete Confirmation', 'All question of this round also will be deleted. Are you sure you want to delete this round ?', 'Yes', 'No').then(result => {
      if (result) {
        this.dataService.showLoader();
        this.dataService.postData('deleteRoundDetailByAdmin', {
          'round_id': round_id
        }, {
            headers: ({
              'Authorization': 'Bearer ' + localStorage.getItem('token')
            })
          }).then((result: any) => {
            if (result.code == 200) {
              this.dataService.hideLoader();
              this.getAllRound(this.per_page, this.sortField, this.order, this.page);
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

  enterUpdating(row) {
    this.updatingRoundId = row.round_id;
    this.RowDataForUpdateCancelRef = JSON.parse(JSON.stringify(row));
  }

  ExitUpdating(i = null) {
    if (i != null) {
      // replace original data i.e stored while entering into updating mode.
      this.data[i] = this.RowDataForUpdateCancelRef;
    }
    this.updatingRoundId = "";
  }

  updateRound(roundDetail) {
    if (!this.dataService.isExist(roundDetail)) {
      this.error = ERROR.ROUNDNAME_LENGTH;
      this.dataService.showSnackBar(this.error, '', 3000, 'error');
    }
    else if (!this.dataService.isExist(roundDetail.round_name)) {
      this.error = ERROR.ROUNDNAME_LENGTH;
      this.dataService.showSnackBar(this.error, '', 3000, 'error');
    }
    else if (!this.dataService.isExist(roundDetail.entry_coins)) {
      this.error = ERROR.ENTRYCOIN_LENGTH;
      this.dataService.showSnackBar(this.error, '', 3000, 'error');
    }
    else if (roundDetail.entry_coins < 0) {
      this.error = ERROR.ENTRYCOIN_MIN_VALUE;
      this.dataService.showSnackBar(this.error, '', 3000, 'error');
    }
    else if (!this.dataService.isInt(roundDetail.entry_coins)) {
      this.error = ERROR.VALID_INT_NUMBER + ' In Entry Coins';
      this.dataService.showSnackBar(this.error, '', 3000, 'error');
    }
    else if (!this.dataService.isExist(roundDetail.coin_per_answer)) {
      this.error = ERROR.COINSPERANSWER_LENGTH;
      this.dataService.showSnackBar(this.error, '', 3000, 'error');
    }
    else if (roundDetail.coin_per_answer < 0) {
      this.error = ERROR.COINSPERANSWER_MIN_VALUE;
      this.dataService.showSnackBar(this.error, '', 3000, 'error');
    }
    else if (!this.dataService.isInt(roundDetail.coin_per_answer)) {
      this.error = ERROR.VALID_INT_NUMBER + ' In Coins Per Answer';
      this.dataService.showSnackBar(this.error, '', 3000, 'error');
    }
    else if (!this.dataService.isExist(roundDetail.sec_to_answer)) {
      this.error = ERROR.SECONDSTOANSWER_LENGTH;
      this.dataService.showSnackBar(this.error, '', 3000, 'error');
    }
    else if (roundDetail.sec_to_answer < 0) {
      this.error = ERROR.SECONDSTOANSWER_MIN_VALUE;
      this.dataService.showSnackBar(this.error, '', 3000, 'error');
    }
    else if (!this.dataService.isExist(roundDetail.coins_minus)) {
      this.error = ERROR.COINSMINUS_LENGTH;
      this.dataService.showSnackBar(this.error, '', 3000, 'error');
    }
    else if (roundDetail.coins_minus < 0) {
      this.error = ERROR.COINSMINUS_MIN_VALUE;
      this.dataService.showSnackBar(this.error, '', 3000, 'error');
    }
    else if (!this.dataService.isInt(roundDetail.coins_minus)) {
      this.error = ERROR.VALID_INT_NUMBER + ' In Coins Minus';
      this.dataService.showSnackBar(this.error, '', 3000, 'error');
    }
    else if (!this.dataService.isExist(roundDetail.total_question_for_user)) {
      this.error = ERROR.TOTALQUESTION_LENGTH;
      this.dataService.showSnackBar(this.error, '', 3000, 'error');
    }
    else if (roundDetail.total_question_for_user < 0) {
      this.error = ERROR.TOTALQUESTION_MIN_VALUE;
      this.dataService.showSnackBar(this.error, '', 3000, 'error');
    }
    else if (!this.dataService.isInt(roundDetail.total_question_for_user)) {
      this.error = ERROR.VALID_INT_NUMBER + ' In Total Coin';
      this.dataService.showSnackBar(this.error, '', 3000, 'error');
    }
    else if (!this.dataService.isExist(roundDetail.time_break)) {
      this.error = ERROR.TIMEBREAK_LENGTH;
      this.dataService.showSnackBar(this.error, '', 3000, 'error');
    }
    else if (roundDetail.time_break < 0) {
      this.error = ERROR.TIMEBREAK_MIN_VALUE;
      this.dataService.showSnackBar(this.error, '', 3000, 'error');
    }
    else if (!this.dataService.isInt(roundDetail.time_break)) {
      this.error = ERROR.VALID_INT_NUMBER + ' In Time Break';
      this.dataService.showSnackBar(this.error, '', 3000, 'error');
    }
    else {
      this.dataService.showLoader();
      this.dataService.postData('updateRoundDetailByAdmin', {
        round_id: roundDetail.round_id,
        round_name: roundDetail.round_name,
        entry_coins: roundDetail.entry_coins,
        coin_per_answer: roundDetail.coin_per_answer,
        sec_to_answer: roundDetail.sec_to_answer,
        coins_minus: roundDetail.coins_minus,
        total_question_for_user: roundDetail.total_question_for_user,
        time_break: roundDetail.time_break
      }, {
          headers: ({
            'Authorization': 'Bearer ' + localStorage.getItem('token')
          })
        }).then((result: any) => {
          if (result.code == 200) {
            this.dataService.hideLoader();
            this.ExitUpdating();
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
  }


  // question management section start here

  openRound(row, i) {
    localStorage.setItem('selected_round', JSON.stringify(row));
    this.dataService.navigate('/dashboard/', row.round_id)
  }

}
