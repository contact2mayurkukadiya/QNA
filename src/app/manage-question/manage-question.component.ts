import { Component, OnInit, OnDestroy } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { AddQuestionComponent } from '../add-question/add-question.component';
import { DataService } from '../data.service';
import { ERROR } from '../app.constants';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-manage-question',
  templateUrl: './manage-question.component.html',
  styleUrls: ['./manage-question.component.scss']
})
export class ManageQuestionComponent implements OnInit, OnDestroy {

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
  updatingQuestionId: any;
  RowDataForUpdateCancelRef: any = {};

  round_id: any = "";
  roundDetail: any = {};

  fileToUpload: any;
  clearRef: any;

  constructor(public dialog: MatDialog, public dataService: DataService, private activatedRoute: ActivatedRoute) {
    this.per_page = this.perPage[0];
    this.activatedRoute.params.subscribe(params => {
      this.round_id = parseInt(params['round_id']);
    });
    if (!this.isEmptyJSON(JSON.parse(localStorage.getItem('selected_round')))) {
      this.roundDetail = JSON.parse(localStorage.getItem('selected_round'));
    }
    this.getAllQuestion();
  }

  ngOnInit() {
  }

  ngOnDestroy() {
    if (localStorage.getItem('selected_round')) {
      localStorage.removeItem('selected_round');
    }
  }

  isEmptyJSON(obj) {
    for (var key in obj) {
      if (obj.hasOwnProperty(key))
        return false;
    }
    return true;
  }

  openModelForAddQuestion() {
    let addQuestionRef = this.dialog.open(AddQuestionComponent, {
      hasBackdrop: true,
      disableClose: true,
      panelClass: 'add-question-wrapper'
    })
    addQuestionRef.afterClosed().toPromise().then(result => {
      if (result) {
        this.getAllQuestion(this.per_page, this.sortField, this.order, this.page);
      }
    })
  }

  changeOrder(perpage, sortfield, order, pageno = 1) {
    this.sortField = sortfield;
    this.order = this.order == 'ASC' ? 'DESC' : 'ASC';
    this.getAllQuestion(perpage, sortfield, this.order, pageno)
  }

  getAllQuestion(perpage = this.perPage[0], sortfield = 'update_time', sorttype = 'DESC', pageno = 1) {
    this.page = pageno;
    this.data_fetch_error = null;
    this.dataService.showLoader();
    this.dataService.postData("getQuestionAnswerFromRoundByAdmin",
      {
        "round_id": this.round_id,
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
          this.total_record = result.data.total_question;
          if (result.data.result.length <= 0 || result.data.result[0].questions_detail.length <= 0) {
            if (this.page == 1) {
              this.data_fetch_error = "No Data Found";
              this.data = [];
            } else {
              this.page -= 1;
              this.getAllQuestion(perpage, sortfield, this.order, this.page)
            }
          } else if (result.data.result[0].questions_detail) {
            this.data = this.dataService.applyIndex(result.data.result[0].questions_detail, this.per_page, this.page);
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

  deleteQuestion(question_id) {
    this.dataService.askConfirmation('Delete Confirmation', 'Are you sure you want to delete this question ?', 'Yes', 'No').then(result => {
      if (result) {
        this.dataService.showLoader();
        this.dataService.postData('deleteQuestionAnswerByAdmin', {
          'question_id': question_id
        }, {
            headers: ({
              'Authorization': 'Bearer ' + localStorage.getItem('token')
            })
          }).then((result: any) => {
            if (result.code == 200) {
              this.dataService.hideLoader();
              this.getAllQuestion(this.per_page, this.sortField, this.order, this.page);
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

  openModalforUpdate(row) {
    let addQuestionRef = this.dialog.open(AddQuestionComponent, {
      hasBackdrop: true,
      disableClose: true,
      panelClass: 'add-question-wrapper',
      data: row
    })
    addQuestionRef.afterClosed().toPromise().then(result => {
      if (result) {
        this.getAllQuestion(this.per_page, this.sortField, this.order, this.page);
      }
    })
  }

  back() {
    this.dataService.navigate('dashboard');
  }

  handleFileInput($events) {
    let files = $events.target.files;
    this.fileToUpload = files.item(0);
  }

  sendCsvFile() {
    if (this.fileToUpload) {
      const formData: FormData = new FormData();
      formData.append('file', this.fileToUpload, this.fileToUpload.name);
      this.dataService.showLoader();
      this.dataService.postData('addQuestionAnswerFromExcelByAdmin', formData, {
        headers: ({
          'Authorization': 'Bearer ' + localStorage.getItem('token')
        })
      }).then((result: any) => {
        if (result.code == 200) {
          this.dataService.hideLoader();
          this.fileToUpload = null;
          this.clearRef = null;
          this.getAllQuestion(this.per_page, this.sortField, this.order, this.page);
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
    else {
      this.dataService.showSnackBar('Please choose File', '', 3000, 'error');
    }
  }
}
