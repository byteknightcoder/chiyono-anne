$('.calendar').pignoseCalendar({

  // en, ko, fr, ch, de, jp, pt, fr
  lang: 'jp',

  // You can change auto initialized date at first.
  date: moment(),

  // light, dark
  theme: 'light',

  // date format
  format: 'YYYY-MM-DD',

  // CSS class array
  classOnDays: [],

  // array of enabled dates
  enabledDates: [],

  // disabled dates
  disabledDates: [],

  // You can disable by using a weekday number array (This is a sequential number start from 0 [sunday]).
  disabledWeekdays: [],

  // This option is advanced way to using disable settings, You can give multiple disabled range by double array date string by formatted to 'YYYY-MM-DD'.
  disabledRanges: [],

  // Set and array of events to pin on calendar. 
  // Each event is an object that must contain a date string formatted to 'YYYY-MM-DD' and class key that belong to classOnEvents dictionary indicating its color on calendar. 
  // Events with not color class suitable are all pin with  #5c6270. 
  // Issues may arise when the number of color class increase, this could break the calendar cell.
  schedules: [],

  // Set a dictionary with class 'key' and color code 'value' used for pin events class by date.
  scheduleOptions: {
      colors: {}
  },

  // Your first day of week (0: Sunday ~ 6: Saturday).
  week: 0,

  // If you set true this option, You can use multiple range picker by one click on your Calendar.
  pickWeeks: false,

  //  You can turn on/off initialized date by this option.
  initialize: true,
  
  // enable multiple date selection
  multiple: false,

  // use toggle in the calendar
  toggle: false,

  // shows buttons
  buttons: false,

  // reverse mode
  reverse: false,
  
  // display calendar as modal
  modal: false,

  // add button controller for confirm by user
  //buttons: false,

  // minimum disable date range by 'YYYY-MM-DD' formatted string
  minDate: null,

  // maximum disable date range by 'YYYY-MM-DD' formatted string
  maxDate: null,

  // called when you select a date on calendar (date click).
  /*select: functon (){},

  // If you set true this option, You can pass the check of disabled dates when multiple mode enabled.
  selectOver: false,

  // called when you apply a date on calendar. (OK button click).
  apply: functon (){},

  // called when you apply a date on calendar. (OK button click).
  click: null*/

});