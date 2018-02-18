console.log('month-controller.js');
//TODO: add error handling to prevent TypeError when user clicks beyond currently loaded calendar months
let months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September','October','November','December'];
let monthsAbbreviations = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];

let changeMonth = function(monthCalendar, direction){
  console.log("changeMonth()");
  let currentMonth = monthCalendar.querySelector('[data-month-active="true"]');
  deactivateMonth(monthCalendar, currentMonth);
  activateMonth(currentMonth);
  var targetMonth = direction ? currentMonth.nextElementSibling : currentMonth.previousElementSibling;  
  activateMonth(currentMonth);
  let monthNumber = targetMonth.dataset.month;
  changeMonthLabel(monthNumber);
  changeEventsMonth(targetMonth);
}

var activateMonth = function(targetMonth){
  targetMonth.dataset.monthActive = true;
  targetMonth.classList.remove('inactive-month');
  targetMonth.classList.add('month');
}

var deactivateMonth = function(monthCalendar, currentMonth){
    currentMonth.classList.remove('month');
    currentMonth.classList.add('inactive-month');
    currentMonth.dataset.monthActive = false;
}

var nextMonthOffset = function(index){
  if(index < 11){
    return index+1;
  }
  else{
    return 0;
  }
}
var prevMonthOffset = function(index){
  if(index > 0){
    return index-1;
  }
  else{
    return 11;
  }
}

var setCalendarNavigationBarLabel = function(month){
  let label = document.querySelector('[class="calendar-navigation-bar-label"]');
  label.textContent = month;
}

var setCalendarNavigationBarNextButton = function(month){
  var nextButton = document.getElementById('calendar-next');
  nextButton.textContent = month;
}

var setCalendarNavigationBarPrevButton = function(month){
  var prevButton = document.getElementById('calendar-prev');
  prevButton.textContent = month;  
}

var setMonthsLabel = function(month){
  let monthLabel = document.querySelector('div[class="current-month-label"]');
  monthLabel.textContent = month;
}

var setEventsInLabel = function(month){
  var eventLabel = document.querySelector('[class="event-status-label"]');
  var tempText = 'events in '+month;
  eventLabel.textContent = tempText;
}

let changeMonthLabel = function(monthNumber){
  let statusBar = document.getElementById('calendar-status-bar');
  let statusBarBack = document.getElementById('calendar-back-button');
  for(var monthId = 0; monthId < months.length; monthId++){
    if((monthId+1) === parseInt(monthNumber)){
      setCalendarNavigationBarLabel(months[monthId])
      setCalendarNavigationBarNextButton(months[nextMonthOffset(monthId)]);
      setCalendarNavigationBarPrevButton(months[prevMonthOffset(monthId)]);
      setEventsInLabel(months[monthId]);
      setMonthsLabel(months[monthId]); 
    }
  }
}

let initUiControls = function(){
  let monthCalendar = document.getElementById('calendar-widget');
  let buttonNextMonth = document.getElementById('calendar-next');
  let buttonPrevMonth = document.getElementById('calendar-prev');
  buttonNextMonth.onclick = function(){
    changeMonth(monthCalendar, true);
  }
  buttonPrevMonth.onclick = function(){
    changeMonth(monthCalendar, false);
  }
}

initUiControls();
