import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ApproveCoinDialogComponent } from './approve-coin-dialog.component';

describe('ApproveCoinDialogComponent', () => {
  let component: ApproveCoinDialogComponent;
  let fixture: ComponentFixture<ApproveCoinDialogComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ApproveCoinDialogComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ApproveCoinDialogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
