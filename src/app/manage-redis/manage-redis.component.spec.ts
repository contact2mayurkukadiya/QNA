import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ManageRedisComponent } from './manage-redis.component';

describe('ManageRedisComponent', () => {
  let component: ManageRedisComponent;
  let fixture: ComponentFixture<ManageRedisComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ManageRedisComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ManageRedisComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
