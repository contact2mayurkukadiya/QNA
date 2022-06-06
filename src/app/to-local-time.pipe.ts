import { Pipe, PipeTransform } from '@angular/core';
import * as moment from 'moment';

@Pipe({
  name: 'toLocalTime'
})
export class ToLocalTimePipe implements PipeTransform {

  transform(value: any, args?: any): any {
    return moment.utc(value).local().format('YYYY-MM-DD hh:mm:ss A');
  }

}
