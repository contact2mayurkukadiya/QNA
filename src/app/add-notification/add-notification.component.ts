import { Component, OnInit, Inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { DataService } from '../data.service';
import { ERROR } from '../app.constants';

@Component({
  selector: 'app-add-notification',
  templateUrl: './add-notification.component.html',
  styleUrls: ['./add-notification.component.scss']
})
export class AddNotificationComponent implements OnInit {

  error: any = "";
  minValueLimit = 0;
  isUpdatingMode: any = false;
  list: any = [
    { title: 'CASH CREDIT', value: 'CASH_CREDIT' }
  ];

  noteDetail: any = {
    notify_id: null,
    notification: null,
    skuname: this.list[0].value
  }

  constructor(public dataService: DataService, public dialogRef: MatDialogRef<AddNotificationComponent>, @Inject(MAT_DIALOG_DATA) private dialogData: any) {
    if (dialogData) {
      this.isUpdatingMode = true;
      this.noteDetail.notify_id = dialogData.notify_id
      this.noteDetail.notification = dialogData.alert_data;
      this.noteDetail.skuname = dialogData.skuname;
    }
  }

  ngOnInit() {
  }

  addNotification(noteDetail:any) {
    if (!this.dataService.isExist(noteDetail)) {
      this.error = ERROR.NOTIFICATION_LENGTH
    }
    else if (!this.dataService.isExist(noteDetail.notification)) {
      this.error = ERROR.NOTIFICATION_LENGTH
    }
    else {
      this.dataService.showLoader();
      this.dataService.postData('addNotifyByAdmin', {
        alert_data: noteDetail.notification,
        skuname: noteDetail.skuname,
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

  updateNotification(noteDetail:any) {
    if (!this.dataService.isExist(noteDetail)) {
      this.error = ERROR.NOTIFICATION_LENGTH
    }
    else if (!this.dataService.isExist(noteDetail.notification)) {
      this.error = ERROR.NOTIFICATION_LENGTH
    }
    else {
      this.dataService.showLoader();
      this.dataService.postData('updateNotifyByAdmin', {
        notify_id: this.dialogData.notify_id || '',
        alert_data: noteDetail.notification,
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
