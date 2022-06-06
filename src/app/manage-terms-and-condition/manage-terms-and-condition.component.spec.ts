import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ManageTermsAndConditionComponent } from './manage-terms-and-condition.component';

describe('ManageTermsAndConditionComponent', () => {
  let component: ManageTermsAndConditionComponent;
  let fixture: ComponentFixture<ManageTermsAndConditionComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ManageTermsAndConditionComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ManageTermsAndConditionComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
