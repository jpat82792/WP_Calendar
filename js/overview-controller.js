console.log('overview-controller.js');

var scrollToMonth = function(){
  let calendarOverview = document.getElementById('calendar-overview-widget');
  let currentMonth = calendarOverview.querySelector('[data-month-current="true"]');
  let currentMonthPoint = currentMonth.getBoundingClientRect();
  window.scrollTo(currentMonthPoint.x,currentMonthPoint.y);
}

scrollToMonth();

