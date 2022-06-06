import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AddTermsAndConditionComponent } from './add-terms-and-condition.component';

describe('AddTermsAndConditionComponent', () => {
  let component: AddTermsAndConditionComponent;
  let fixture: ComponentFixture<AddTermsAndConditionComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AddTermsAndConditionComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AddTermsAndConditionComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
