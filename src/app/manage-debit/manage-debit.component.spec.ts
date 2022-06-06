import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ManageDebitComponent } from './manage-debit.component';

describe('ManageDebitComponent', () => {
  let component: ManageDebitComponent;
  let fixture: ComponentFixture<ManageDebitComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ManageDebitComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ManageDebitComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
