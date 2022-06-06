import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { NgbModule, NgbPaginationModule } from '@ng-bootstrap/ng-bootstrap';

import { HttpClientModule } from '@angular/common/http';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';

import { FormsModule } from '@angular/forms';
import { LoginComponent } from './login/login.component';
import { LoadingIndicatorComponent } from './loading-indicator/loading-indicator.component';
import { DashboardComponent } from './dashboard/dashboard.component';
import { SidebarComponent } from './sidebar/sidebar.component';
import { HeaderComponent } from './header/header.component';
import { SettingComponent } from './setting/setting.component';
import { AddRoundComponent } from './add-round/add-round.component';
import { ToLocalTimePipe } from './to-local-time.pipe';
import { ConfirmationComponent } from './confirmation/confirmation.component';
import { AddQuestionComponent } from './add-question/add-question.component';
import { ManageQuestionComponent } from './manage-question/manage-question.component';
import { ManageUserComponent } from './manage-user/manage-user.component';
import { ManageRedisComponent } from './manage-redis/manage-redis.component';
import { ManageFaqComponent } from './manage-faq/manage-faq.component';
import { AddFaqComponent } from './add-faq/add-faq.component';
import { ManageTermsAndConditionComponent } from './manage-terms-and-condition/manage-terms-and-condition.component';
import { AddTermsAndConditionComponent } from './add-terms-and-condition/add-terms-and-condition.component';
import { ManageContactUsComponent } from './manage-contact-us/manage-contact-us.component';
import { RegisterComponent } from './register/register.component';
import { AddAdminComponent } from './add-admin/add-admin.component';
import { ManageDebitComponent } from './manage-debit/manage-debit.component';
import { AddDebitComponent } from './add-debit/add-debit.component';
import { ManageNotificationComponent } from './manage-notification/manage-notification.component';
import { AddNotificationComponent } from './add-notification/add-notification.component';
import { ManageKeywordComponent } from './manage-keyword/manage-keyword.component';
import { AddKeywordComponent } from './add-keyword/add-keyword.component';
import { ManageExpenseComponent } from './manage-expense/manage-expense.component';
import { ApproveCoinDialogComponent } from './approve-coin-dialog/approve-coin-dialog.component';
import { MaterialModule } from './material.module';

@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    LoadingIndicatorComponent,
    DashboardComponent,
    SidebarComponent,
    HeaderComponent,
    SettingComponent,
    AddRoundComponent,
    ToLocalTimePipe,
    ConfirmationComponent,
    AddQuestionComponent,
    ManageQuestionComponent,
    ManageUserComponent,
    ManageRedisComponent,
    ManageFaqComponent,
    AddFaqComponent,
    ManageTermsAndConditionComponent,
    AddTermsAndConditionComponent,
    ManageContactUsComponent,
    RegisterComponent,
    AddAdminComponent,
    ManageDebitComponent,
    AddDebitComponent,
    ManageNotificationComponent,
    AddNotificationComponent,
    ManageKeywordComponent,
    AddKeywordComponent,
    ManageExpenseComponent,
    ApproveCoinDialogComponent,
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    NgbModule,
    NgbPaginationModule,
    BrowserAnimationsModule,
    FormsModule,
    HttpClientModule,
    MaterialModule
  ],
  providers: [],
  bootstrap: [AppComponent],
  entryComponents: [
    LoadingIndicatorComponent,
    AddRoundComponent,
    AddQuestionComponent,
    AddFaqComponent,
    ConfirmationComponent,
    AddTermsAndConditionComponent,
    AddAdminComponent,
    AddDebitComponent,
    AddNotificationComponent,
    AddKeywordComponent,
    ApproveCoinDialogComponent
  ]
})
export class AppModule { }
