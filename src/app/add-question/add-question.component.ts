import { Component, OnInit, Inject } from '@angular/core';
import { DataService } from '../data.service';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { ERROR } from '../app.constants';

@Component({
  selector: 'app-add-question',
  templateUrl: './add-question.component.html',
  styleUrls: ['./add-question.component.scss']
})
export class AddQuestionComponent implements OnInit {

  questionDetail: any = {
    round_id: null,
    question: null,
    answer_a: null,
    answer_b: null,
    answer_c: null,
    answer_d: null,
    real_answer: null,
    image: null
  }
  error: any = '';
  fileToUpload: any = '';
  validFileExtensions: any = [".jpg", ".jpeg", ".bmp", ".gif", ".png"];

  isUpdatingMode = false;
  roundDetail: any = {};

  constructor(public dataService: DataService, public dialogRef: MatDialogRef<AddQuestionComponent>, @Inject(MAT_DIALOG_DATA) private dialogData: any) {
    if (dialogData) {
      this.isUpdatingMode = true;
      this.questionDetail.question = dialogData.question;
      this.questionDetail.answer_a = dialogData.answer_a;
      this.questionDetail.answer_b = dialogData.answer_b;
      this.questionDetail.answer_c = dialogData.answer_c;
      this.questionDetail.answer_d = dialogData.answer_d;
      this.questionDetail.real_answer = dialogData.real_answer;
      this.questionDetail.image = dialogData.question_compressed_image;
    }
    if (!this.isEmptyJSON(JSON.parse(localStorage.getItem('selected_round')))) {
      this.roundDetail = JSON.parse(localStorage.getItem('selected_round'));
      this.questionDetail.round_id = this.roundDetail.round_id
    }
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

  close() {
    this.dialogRef.close(false);
  }

  validateFile(file) {
    var sFileName = file.name;
    if (sFileName.length > 0) {
      var blnValid = false;
      for (var j = 0; j < this.validFileExtensions.length; j++) {
        var sCurExtension = this.validFileExtensions[j];
        if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
          blnValid = true;
          break;
        }
      }

      if (!blnValid) {
        // alert("Sorry, " + sFileName + " is invalid, allowed extensions are: " + this.validFileExtensions.join(", "));
        return false;
      }
    }
    return true;
  }

  displayImage($event, id) {
    let files = $event.target.files;
    if (this.validateFile(files.item(0))) {
      this.fileToUpload = files.item(0);
      this.dataService.readURL(this.fileToUpload, id);
    }
    else {
      this.fileToUpload = "";
    }
  }

  addQuestion(questionDetail) {
    if (!this.dataService.isExist(questionDetail)) {
      this.error = ERROR.QUESTION_NAME_LENGTH
    }
    else if (!this.dataService.isExist(questionDetail.question)) {
      this.error = ERROR.QUESTION_NAME_LENGTH
    }
    else if (!this.dataService.isExist(questionDetail.answer_a)) {
      this.error = ERROR.ANSWER_LENGTH + 'A'
    }
    else if (!this.dataService.isExist(questionDetail.answer_b)) {
      this.error = ERROR.ANSWER_LENGTH + 'B'
    }
    else if (!this.dataService.isExist(questionDetail.answer_c)) {
      this.error = ERROR.ANSWER_LENGTH + 'C'
    }
    else if (!this.dataService.isExist(questionDetail.answer_d)) {
      this.error = ERROR.ANSWER_LENGTH + 'D'
    }
    else if (!this.dataService.isExist(questionDetail.real_answer)) {
      this.error = ERROR.REAL_ANSWER_LENGTH;
    }
    else {
      this.error = "";
      this.dataService.showLoader();
      let formData: FormData = new FormData();
      if (this.dataService.isExist(this.fileToUpload)) {
        formData.append('file', this.fileToUpload)
      }
      formData.append('request_data', JSON.stringify(this.questionDetail));
      this.dataService.postData('addQuestionAnswerByAdmin', formData, {
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
      });
    }
  }

  updateQuestion(questionDetail) {
    if (!this.dataService.isExist(questionDetail)) {
      this.error = ERROR.QUESTION_NAME_LENGTH
    }
    else if (!this.dataService.isExist(questionDetail.question)) {
      this.error = ERROR.QUESTION_NAME_LENGTH
    }
    else if (!this.dataService.isExist(questionDetail.answer_a)) {
      this.error = ERROR.ANSWER_LENGTH + 'A'
    }
    else if (!this.dataService.isExist(questionDetail.answer_b)) {
      this.error = ERROR.ANSWER_LENGTH + 'B'
    }
    else if (!this.dataService.isExist(questionDetail.answer_c)) {
      this.error = ERROR.ANSWER_LENGTH + 'C'
    }
    else if (!this.dataService.isExist(questionDetail.answer_d)) {
      this.error = ERROR.ANSWER_LENGTH + 'D'
    }
    else if (!this.dataService.isExist(questionDetail.real_answer)) {
      this.error = ERROR.REAL_ANSWER_LENGTH;
    }
    else {
      this.error = "";
      this.dataService.showLoader();
      let formData: FormData = new FormData();
      if (this.dataService.isExist(this.fileToUpload)) {
        formData.append('file', this.fileToUpload)
      }
      this.questionDetail['question_id'] = this.dialogData.question_id;
      formData.append('request_data', JSON.stringify(this.questionDetail));
      this.dataService.postData('updateQuestionAnswerByAdmin', formData, {
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
      });
    }
  }

}
