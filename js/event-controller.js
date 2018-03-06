console.log('event-controller.js');

let changeEventsLabel = function(keyword, eventLabel){
  switch (keyword) {
    case 'week':
      eventLabel.textContent = 'upcoming events this week';
      break;
    case 'month':
      eventLabel.textContent = 'upcoming events this month';
      break;
    default:
      eventLabel.textContent = 'events';
  }
}

let changeEventsMonth = function(month){
  console.log('changeEventsMonth()');
  let eventContainer = document.getElementById('event-widget');  
  let currentYear = month.dataset.year;
  let currentMonth = month.dataset.month;
  let events = eventContainer.children;
  for(var i = 0; i < events.length; i++ ){
    if (i !== 0){
      let temp = events[i];
      if((parseInt(currentMonth) === parseInt(temp.dataset.month)) && (parseInt(currentYear) === parseInt(temp.dataset.year))){
        temp.classList.remove('active-event');
        temp.classList.remove('inactive-event');
        temp.classList.add('active-event');
      }
      else{
        temp.classList.remove('active-event');
        temp.classList.remove('inactive-event');
        temp.classList.add('inactive-event');
      }
    }
    else{
    }
  }
}

let changeEventsWeek = function(week, element){
  console.log("changeEventsWeek()");
  console.log(element);
  let eventContainer = document.getElementById('event-widget');
  var calendarContainer = document.getElementById('calendar-widget');
  var currentMonth = calendarContainer.querySelector('[class="month"]');
  var currentWeek = currentMonth.querySelector('[data-current-week="true"]');
  var currentWeekEvents = currentWeek.querySelectorAll('[data-event]');
  let events = week.querySelectorAll('[data-event]');
  changeEventsLabel('week', eventContainer.children[0]);
  let allEvents = eventContainer.children;
  var atSelectedDay = true;
  for(var i = 0 ; i < allEvents.length; i++){
    if(i !== 0){
      allEvents[i].classList.remove('active-event');
      allEvents[i].classList.add('inactive-event');
    }
  }
  var selectedDay = false
  for(var i = 0 ; i < currentWeekEvents.length; i++){
    if(currentWeekEvents[i] === element.children[2]){
      selectedDay = true;
    }
    if(selectedDay){
      var eventArrayContainer = currentWeekEvents[i].dataset.event;
      var eventArray = eventArrayContainer.split(',');
      for(var j = 0; j < eventArray.length;j++){
      var eventId = eventArray[j].split('(')[1];
      var eventBlock = eventContainer.querySelector('[data-event="'+eventId+'"]');
      if(eventBlock != null){
        eventBlock.classList.add('active-event');
      }
      }
    }
  }
}

let dismissSelectedEvent = function(eventDropdown, dayContainer, className){
  eventDropdown.classList.remove('active-filter-dropdown');
  dayContainer.classList.remove(className);
}

let setDayLabelBackground = function(importance){
  console.log('setDayLabelBackground()');
  switch(importance){
    case 'required':
      return 'required-color';
    case 'recommended':
      return 'recommended-color';
    case 'interesting':
      return 'interesting-color';
    default:
      return '';
  }
}

var setEventDay = function(dayNumber, dayLabelClass){
  var eventDayContainer = document.getElementById('dropdown-event-day');
  eventDayContainer.textContent = dayNumber;
  eventDayContainer.classList.add(dayLabelClass);
}

var setEventMonth = function(month){
  let eventMonthContainer = document.getElementById('dropdown-event-month');
  eventMonthContainer.textContent = month;
}

var setEventYear = function(year){
    let eventYearContainer = document.getElementById('dropdown-event-year');
  eventYearContainer.textContent = year;
}

var setEventCategory = function(eventCategory){
  let eventCategoryContainer = document.getElementById('dropdown-event-category');
  eventCategoryContainer.textContent = eventCategory;
}

var setEventTitle = function(eventTitle){
  let eventTitleContainer = document.getElementById('dropdown-event-title');
  eventTitleContainer.textContent = eventTitle;
}

var setEventDescription = function(eventContent){
  let eventDescriptionContainer = document.getElementById('dropdown-event-content');
  eventDescriptionContainer.textContent = eventContent;
}

var setEventModal = function(month, day, dayClass, year, category, title, description){
  setEventMonth(month);
  setEventDay(day, dayClass);
  setEventYear(year);
  setEventCategory(category);
  setEventTitle(title);
  setEventDescription(description);
}

var showEventModal = function(eventDropdown){
  //display event dropdown
  eventDropdown.classList.remove('active-filter-dropdown');
  eventDropdown.className += ' active-filter-dropdown';
}
var setEventModalDismissButton = function(eventDropdown, eventDayContainer, dayLabelClass){
  let dismissEventButton = document.getElementById('close-event-details-dropdown');
  
  dismissEventButton.onclick = function(){
    dismissSelectedEvent(eventDropdown, eventDayContainer, dayLabelClass);
  }
}

let selectEvent = function(event){
  //fill with event data from event parameter
  let month = event.querySelector('[class="event-month"]').textContent;
  let dayNumber = event.querySelector('div').textContent;
  let year = event.dataset.year;
  let eventCategory = event.dataset.category;
  let eventTitle = event.querySelector('[class="event-title"]').textContent;
  let eventContent = event.dataset.eventContent;
  let eventImportance = event.dataset.eventImportance;
  var dayLabelClass = setDayLabelBackground(eventImportance);
  let eventDropdown = document.getElementById('event-details-dropdown');
  let eventDayContainer = document.getElementById('dropdown-event-day');
  setEventModal(month, dayNumber, dayLabelClass, year, eventCategory, eventTitle, eventContent);
  setEventModalDismissButton(eventDropdown, eventDayContainer, dayLabelClass);
  showEventModal(eventDropdown);
}

let initEventUi = function(){
  let events = document.getElementById('event-widget').children;
  for(var i = 0; i < events.length; i++){
    if(i !== 0){
      events[i].onclick = function(){selectEvent(this);};
    }
  }
}

initEventUi();
