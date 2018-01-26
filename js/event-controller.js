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
  for(var i = 0;i < events.length; i++ ){
    if (i !== 0){
      let temp = events[i];
      if((parseInt(currentMonth) === parseInt(temp.dataset.month)) && (parseInt(currentYear) === parseInt(temp.dataset.year))){
        temp.classList.remove('active-event');
        temp.classList.remove('inactive-event');
        temp.className += ' active-event';
      }
      else{
        temp.classList.remove('active-event');
        temp.classList.remove('inactive-event');
        temp.className += ' inactive-event';
      }
    }
    else{
      changeEventsLabel('month', events[i]);
    }
  }
}

let changeEventsWeek = function(week, element){
  console.log("changeEventsWeek()");
  let eventContainer = document.getElementById('event-widget');
  let events = week.querySelectorAll('[data-event]');
  changeEventsLabel('week', eventContainer.children[0]);
  let allEvents = eventContainer.children;
  var atSelectedDay = false;
  for(var i = 0 ; i < allEvents.length; i++){
    if(i !== 0){
      allEvents[i].className = 'inactive-event';
    }
  }
  for(var i = 0 ; i < events.length; i++){
    let temp = events[i];
    if(element === events[i].parentNode){
      console.log('Found the day');
      atSelectedDay = true;
    }
    else{
      console.log('nope, no day');
    }
    if(week === events[i].parentNode || atSelectedDay){
      let workArray = temp.dataset.event.split(', ');
      console.log("workArray");
      console.log(workArray);
      for(var j = 0; j < workArray.length; j++){
        let eventInfo = eventContainer.querySelector('[data-event="'+workArray[j]+'"]');
        if(eventInfo !== null){
          eventInfo.classList.remove('active-event');
          eventInfo.className += ' active-event';
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

let selectEvent = function(event){
  //fill with event data from event parameter
  let month = event.querySelector('[class="event-month"]').textContent;
  let dayNumber = event.querySelector('div').textContent;
  let year = event.dataset.year;
  let eventCategory = event.dataset.category;
  let eventTitle = event.querySelector('[class="event-title"]').textContent;
  let eventContent = event.dataset.eventContent;
  let eventImportance = event.dataset.eventImportance;
  let dayLabelClass = setDayLabelBackground(eventImportance);
  


  let eventDropdown = document.getElementById('event-details-dropdown');
  let eventDayContainer = document.getElementById('dropdown-event-day');
  let eventMonthContainer = document.getElementById('dropdown-event-month');
  let eventYearContainer = document.getElementById('dropdown-event-year');
  let eventCategoryContainer = document.getElementById('dropdown-event-category');
  let eventTitleContainer = document.getElementById('dropdown-event-title');
  let eventDescriptionContainer = document.getElementById('dropdown-event-content');
  let dismissEventButton = document.getElementById('close-event-details-dropdown');
  
  dismissEventButton.onclick = function(){
    dismissSelectedEvent(eventDropdown, eventDayContainer,dayLabelClass);
  }
  eventDayContainer.textContent = dayNumber;
  eventDayContainer.className += ' '+dayLabelClass;
  eventMonthContainer.textContent = month;
  eventYearContainer.textContent = year;
  eventCategoryContainer.textContent = eventCategory;
  eventTitleContainer.textContent = eventTitle;
  eventDescriptionContainer.textContent = eventContent;
  //display event dropdown
  eventDropdown.classList.remove('active-filter-dropdown');
  eventDropdown.className += ' active-filter-dropdown';
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
