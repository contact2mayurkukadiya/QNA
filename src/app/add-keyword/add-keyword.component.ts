import { Component, OnInit, Inject } from '@angular/core';
import { DataService } from '../data.service';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { ERROR } from '../app.constants';

@Component({
  selector: 'app-add-keyword',
  templateUrl: './add-keyword.component.html',
  styleUrls: ['./add-keyword.component.scss']
})
export class AddKeywordComponent implements OnInit {

  error: any = "";
  minValueLimit = 0;
  isUpdatingMode: any = false;
  list: any = [
    { title: 'CASH CREDIT', value: 'CASH_CREDIT' }
  ];

  keywordDetail: any = {
    keyword_id: null,
    keyword: null,
    value: null,
    description: null,
    skuname: this.list[0].value
  }

  constructor(public dataService: DataService, public dialogRef: MatDialogRef<AddKeywordComponent>, @Inject(MAT_DIALOG_DATA) private dialogData: any) {
    if (dialogData) {
      this.isUpdatingMode = true;
      this.keywordDetail.keyword_id = dialogData.keyword_id
      this.keywordDetail.keyword = dialogData.keyword;
      this.keywordDetail.value = dialogData.value;
      this.keywordDetail.description = dialogData.description;
      this.keywordDetail.skuname = dialogData.skuname;
    }
  }

  ngOnInit() {
  }

  addKeyword(keywordDetail:any) {
    if (!this.dataService.isExist(keywordDetail)) {
      this.error = ERROR.KEYWORD_LENGTH
    }
    else if (!this.dataService.isExist(keywordDetail.keyword)) {
      this.error = ERROR.KEYWORD_LENGTH
    }
    else if (!this.dataService.isExist(keywordDetail.value)) {
      this.error = ERROR.KEYWORD_VALUE_LENGTH
    }
    else if (!this.dataService.isExist(keywordDetail.description)) {
      this.error = ERROR.KEYWORD_DESCRIPTION_LENGTH
    }
    else {
      this.dataService.showLoader();
      this.dataService.postData('addKeywordByAdmin', {
        keyword: keywordDetail.keyword,
        value: keywordDetail.value,
        description: keywordDetail.description,
        skuname: keywordDetail.skuname,
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

  updateKeyword(keywordDetail:any) {
    if (!this.dataService.isExist(keywordDetail)) {
      this.error = ERROR.KEYWORD_LENGTH
    }
    else if (!this.dataService.isExist(keywordDetail.keyword)) {
      this.error = ERROR.KEYWORD_LENGTH
    }
    else if (!this.dataService.isExist(keywordDetail.value)) {
      this.error = ERROR.KEYWORD_VALUE_LENGTH
    }
    else if (!this.dataService.isExist(keywordDetail.description)) {
      this.error = ERROR.KEYWORD_DESCRIPTION_LENGTH
    }
    else {
      this.dataService.showLoader();
      this.dataService.postData('updateKeywordByAdmin', {
        keyword_id: this.dialogData.keyword_id || '',
        keyword: keywordDetail.keyword,
        value: keywordDetail.value,
        description: keywordDetail.description,
        skuname: keywordDetail.skuname
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
  close() {
    this.dialogRef.close(false);
  }

}
