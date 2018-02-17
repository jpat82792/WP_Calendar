console.log('month-controller.js');
//TODO: add error handling to prevent TypeError when user clicks beyond currently loaded calendar months
let months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September','October','November','December'];
let monthsAbbreviations = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];

let changeMonth = function(monthCalendar, direction){
  console.log("changeMonth()");
    let currentMonth = monthCalendar.querySelector('[data-month-active="true"]');
    currentMonth.classList.remove('month');
    currentMonth.className += 'inactive-month';
    currentMonth.dataset.monthActive = false;
    var targetMonth = direction ? currentMonth.nextElementSibling : currentMonth.previousElementSibling;
    targetMonth.dataset.monthActive = true;
    targetMonth.classList.remove('inactive-month');
    targetMonth.className += 'month';
    let monthNumber = targetMonth.dataset.month;
    changeMonthLabel(monthNumber);
    changeEventsMonth(targetMonth);

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

let changeMonthLabel = function(monthNumber){
  let statusBar = document.getElementById('calendar-status-bar');
  let statusBarBack = document.getElementById('calendar-back-button');
  let monthLabel = document.querySelector('div[class="current-month-label"]');
//  let statusBarLabel = document.getElementById('calendar-status-bar-label');
  for(var monthId = 0; monthId < months.length; monthId++){
    if((monthId+1) === parseInt(monthNumber)){
      //statusBarLabel.textContent = months[monthId];
      let label = document.querySelector('[class="calendar-navigation-bar-label"]');
      var nextButton = document.getElementById('calendar-next');
      var prevButton = document.getElementById('calendar-prev');
      var eventLabel = document.querySelector('[class="event-status-label"]');
      console.log(eventLabel);

      var tempText = 'events in '+months[monthId];
      monthLabel.textContent = months[monthId]; 
      eventLabel.textContent = tempText;
      prevButton.textContent = months[prevMonthOffset(monthId)];
      nextButton.textContent = months[nextMonthOffset(monthId)];
      label.textContent = months[monthId];
    }
  }
}

let initUiControls = function(){
  /*let monthCalendar = document.getElementById('calendar-widget');
  let buttonNextMonth = document.getElementById('next-month-btn');
  let buttonPrevMonth = document.getElementById('prev-month-btn');
  buttonNextMonth.onclick = function(){
    changeMonth(monthCalendar, true);
  }
  buttonPrevMonth.onclick = function(){
    changeMonth(monthCalendar, false);
  }*/
}

initUiControls();
