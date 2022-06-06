import { Injectable } from "@angular/core";
import { HttpClient } from "@angular/common/http";
import { HOST } from "./app.constants";
import { Router } from '@angular/router';
import { MatDialog } from '@angular/material/dialog';
import { LoadingIndicatorComponent } from './loading-indicator/loading-indicator.component';
import { ConfirmationComponent } from './confirmation/confirmation.component';
import { MatSnackBar } from "@angular/material/snack-bar";

@Injectable({
  providedIn: "root"
})
export class DataService {

  dialogRef: any;
  confirmationDialogRef: any;

  constructor(private http: HttpClient, public router: Router, public dialog: MatDialog, private snackBar: MatSnackBar) { }

  postData(q, body, header): Promise<any> {
    return this.http.post(HOST.API_URL + q, body, header).toPromise().then((result: any) => {
      if (result.code == 400) {
        localStorage.clear();
        this.router.navigate(['/login']);
      }
      else if (result.code == 401) {
        var new_token = result.data.new_token;
        header = {
          Headers: { 'Authorization': 'Bearer ' + new_token }
        }
        return this.postData(q, body, header);
      }
      else {
        return result;
      }
    });
  }

  validateEmail(email) {
    let filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (filter.test(email)) {
      return true;
    }
    else { return false; }
  }

  showLoader() {
    this.dialogRef = this.dialog.open(LoadingIndicatorComponent, {
      panelClass: 'loading-panel-class',
      hasBackdrop: true,
      disableClose: true
    });
  }

  hideLoader() {
    if (typeof this.dialogRef == 'undefined' || this.dialogRef == null || this.dialogRef == '') {
      return;
    }
    this.dialogRef.close();
  }

  showSnackBar(message: string, action: any, duration = 2000, dialogType = "success") {
    this.snackBar.open(message, action, {
      duration: duration,
      panelClass: ['snakBar-panel-class', dialogType]
    });
  }

  navigate(path, data = {}) {
    this.router.navigate([path, data]);
  }

  askConfirmation(title, description, acceptBtnText = 'Yes', deniedBtnText = 'No'): Promise<any> {
    return new Promise(resolve => {
      this.confirmationDialogRef = this.dialog.open(ConfirmationComponent, {
        panelClass: 'confirmation-panel-class',
        data: {
          title: title,
          description: description,
          acceptBtnText: acceptBtnText,
          deniedBtnText: deniedBtnText
        },
        disableClose: true
      });

      this.confirmationDialogRef.afterClosed().toPromise().then(result => {
        resolve(result);
      });
    });
  }

  isExist(data) {
    // triple eqal is for checking type of data also.
    if (typeof data === 'undefined' || data === null || data === '') {
      return false;
    }
    else {
      return true;
    }
  }

  isInt(n) {
    return Number(n) === n && n % 1 === 0;
  }

  readURL(file, displayContainer) {
    if (file) {
      var reader = new FileReader();
      reader.onload = function (e: any) {
        let element = document.getElementById(displayContainer);
        element.setAttribute('src', e.target.result);
      };
      reader.readAsDataURL(file);
    }
  }

  applyIndex(data, pageSize, currentPage) {
    data.forEach((element, index) => {
      element.index = pageSize * (currentPage - 1) + (index + 1)
    });
    return data;
  }
}
