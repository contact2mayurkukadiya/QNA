import { NgModule } from "@angular/core";
import { Routes, RouterModule } from "@angular/router";
import { LoginComponent } from "./login/login.component";
import { DashboardComponent } from './dashboard/dashboard.component';
import { AuthGuard } from './auth.guard';
import { SettingComponent } from './setting/setting.component';
import { ManageQuestionComponent } from './manage-question/manage-question.component';
import { ManageUserComponent } from './manage-user/manage-user.component';
import { ManageRedisComponent } from './manage-redis/manage-redis.component';
import { ManageFaqComponent } from './manage-faq/manage-faq.component';
import { ManageTermsAndConditionComponent } from './manage-terms-and-condition/manage-terms-and-condition.component';
import { ManageContactUsComponent } from './manage-contact-us/manage-contact-us.component';
import { RegisterComponent } from './register/register.component';
import { ManageDebitComponent } from './manage-debit/manage-debit.component';
import { ManageNotificationComponent } from './manage-notification/manage-notification.component';
import { ManageKeywordComponent } from './manage-keyword/manage-keyword.component';
import { ManageExpenseComponent } from './manage-expense/manage-expense.component';

const routes: Routes = [
  { path: "", component: LoginComponent },
  { path: "login", component: LoginComponent },
  { path: "dashboard", component: DashboardComponent, canActivate: [AuthGuard] },
  { path: "dashboard/:round_id", component: ManageQuestionComponent, canActivate: [AuthGuard] },
  { path: "user", component: ManageUserComponent, canActivate: [AuthGuard] },
  { path: "faq", component: ManageFaqComponent, canActivate: [AuthGuard] },
  { path: "notification", component: ManageNotificationComponent, canActivate: [AuthGuard] },
  { path: "keyword", component: ManageKeywordComponent, canActivate: [AuthGuard] },
  // { path: "chat/:user_id/:user_name", component: ManageContactUsComponent, canActivate: [AuthGuard] },
  { path: "contact", component: ManageContactUsComponent, canActivate: [AuthGuard] },
  { path: "tnc", component: ManageTermsAndConditionComponent, canActivate: [AuthGuard] },
  { path: "redis", component: ManageRedisComponent, canActivate: [AuthGuard] },
  { path: "register", component: RegisterComponent, canActivate: [AuthGuard] },
  { path: "debit", component: ManageDebitComponent, canActivate: [AuthGuard] },
  { path: "expense", component: ManageExpenseComponent, canActivate: [AuthGuard] },
  { path: "setting", component: SettingComponent, canActivate: [AuthGuard] },
];

@NgModule({
  imports: [RouterModule.forRoot(routes, { useHash: true })],
  exports: [RouterModule]
})
export class AppRoutingModule { }
