import { Component, OnInit, Inject } from '@angular/core';
import { DataService } from '../data.service';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { ERROR } from '../app.constants';

@Component({
  selector: 'app-add-faq',
  templateUrl: './add-faq.component.html',
  styleUrls: ['./add-faq.component.scss']
})
export class AddFaqComponent implements OnInit {

  error: any = "";
  minValueLimit = 0;

  isUpdatingMode: any = false;

  faqDetail: any = {
    question: null,
    answer: null
  }

  constructor(public dataService: DataService, public dialogRef: MatDialogRef<AddFaqComponent>, @Inject(MAT_DIALOG_DATA) private dialogData: any) {
    if (dialogData) {
      this.isUpdatingMode = true;
      this.faqDetail.question = dialogData.faq_question;
      this.faqDetail.answer = dialogData.faq_answer;
    }
  }

  ngOnInit() {
  }

  addFaq(faqDetail:any) {
    if (!this.dataService.isExist(faqDetail)) {
      this.error = ERROR.FAQQUESTION_LENGTH
    }
    else if (!this.dataService.isExist(faqDetail.question)) {
      this.error = ERROR.FAQQUESTION_LENGTH
    }
    else if (!this.dataService.isExist(faqDetail.answer)) {
      this.error = ERROR.FAQANSWER_LENGTH
    }
    else {
      this.dataService.showLoader();
      this.dataService.postData('addFAQByAdmin', {
        faq_question: faqDetail.question,
        faq_answer: faqDetail.answer,
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

  updateFaq(faqDetail:any) {
    if (!this.dataService.isExist(faqDetail)) {
      this.error = ERROR.FAQQUESTION_LENGTH
    }
    else if (!this.dataService.isExist(faqDetail.question)) {
      this.error = ERROR.FAQQUESTION_LENGTH
    }
    else if (!this.dataService.isExist(faqDetail.answer)) {
      this.error = ERROR.FAQANSWER_LENGTH
    }
    else {
      this.dataService.showLoader();
      this.dataService.postData('updateFAQByAdmin', {
        faq_id: this.dialogData.faq_id || '',
        faq_question: faqDetail.question,
        faq_answer: faqDetail.answer,
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
