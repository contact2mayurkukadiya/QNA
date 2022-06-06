import { Component, OnInit, Inject } from '@angular/core';
import { ERROR } from '../app.constants';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { DataService } from '../data.service';

@Component({
  selector: 'app-add-terms-and-condition',
  templateUrl: './add-terms-and-condition.component.html',
  styleUrls: ['./add-terms-and-condition.component.scss']
})
export class AddTermsAndConditionComponent implements OnInit {

  error: any = "";
  minValueLimit = 0;

  isUpdatingMode: any = false;

  TnCDetail: any = {
    subject: null,
    description: null
  }

  constructor(public dataService: DataService, public dialogRef: MatDialogRef<AddTermsAndConditionComponent>, @Inject(MAT_DIALOG_DATA) private dialogData: any) {
    if (dialogData) {
      this.isUpdatingMode = true;
      this.TnCDetail.subject = dialogData.subject;
      this.TnCDetail.description = dialogData.description;
    }
  }

  ngOnInit() {
  }

  addTnC(TnCDetail) {
    if (!this.dataService.isExist(TnCDetail)) {
      this.error = ERROR.TNCSUBJECT_LENGTH
    }
    else if (!this.dataService.isExist(TnCDetail.subject)) {
      this.error = ERROR.TNCSUBJECT_LENGTH
    }
    else if (!this.dataService.isExist(TnCDetail.description)) {
      this.error = ERROR.TNCDESCRIPTION_LENGTH
    }
    else {
      this.dataService.showLoader();
      this.dataService.postData('addTermsNConditionsByAdmin', {
        subject: TnCDetail.subject,
        description: TnCDetail.description,
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

  updateTnC(TnCDetail) {
    if (!this.dataService.isExist(TnCDetail)) {
      this.error = ERROR.TNCSUBJECT_LENGTH
    }
    else if (!this.dataService.isExist(TnCDetail.subject)) {
      this.error = ERROR.TNCSUBJECT_LENGTH
    }
    else if (!this.dataService.isExist(TnCDetail.description)) {
      this.error = ERROR.TNCDESCRIPTION_LENGTH
    }
    else {
      this.dataService.showLoader();
      this.dataService.postData('updateTermsNConditionsByAdmin', {
        term_n_condition_id: this.dialogData.term_n_condition_id || '',
        subject: TnCDetail.subject,
        description: TnCDetail.description,
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
