import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ManageFaqComponent } from './manage-faq.component';

describe('ManageFaqComponent', () => {
  let component: ManageFaqComponent;
  let fixture: ComponentFixture<ManageFaqComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ManageFaqComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ManageFaqComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
