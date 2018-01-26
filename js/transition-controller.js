console.log("transition-controller.js");

let showCalendarNavigation = function(){
  console.log('showCalendarNavigation()');
  let calendarNavigationBar = document.getElementById('calendar-navigation-bar');
  calendarNavigationBar.classList.remove('display-none');
}

let changeCalendarNavigationWeek = function(){
  let prevButton = document.getElementById('calendar-prev');
  let nextButton = document.getElementById('calendar-next');
  prevButton.textContent = 'prev week';
  nextButton.textContent = 'next week';
  let label = document.querySelector('[class="calendar-navigation-bar-label"]');
  label.textContent = 'today';
}

let changeCalendarNavigationMonth = function(){
  let prevButton = document.getElementById('calendar-prev');
  let nextButton = document.getElementById('calendar-next');
  prevButton.textContent = 'prev month';
  nextButton.textContent = 'next month';
}

let hideCalendarNavigation = function(){
  console.log('hideCalendarNavigation()');
  let calendarNavigationBar = document.getElementById('calendar-navigation-bar');
  calendarNavigationBar.classList.remove('display-none');
  calendarNavigationBar.className += ' display-none';
  
}

let selectMonth = function(element, monthComponent, yearComponent, eventComponent){
  console.log('selectMonth');
changeCalendarNavigationMonth();
  showCalendarNavigation();
  let statusBarBack = document.getElementById('calendar-back-button');
  statusBarBack.classList.remove('display-none');
  changeEventsLabel('month', eventComponent.children[0]);
  let month = element.dataset.month;
  let year = element.dataset.year;
  let selectedMonth = monthComponent.querySelector('[data-month="'+month+'"][data-year="'+year+'"]');
  let activeMonth = monthComponent.querySelector('[data-month-active="true"]');
  changeMonthLabel(month);
  activeMonth.dataset.monthActive = false;
  activeMonth.classList.remove('month');
  activeMonth.className += 'inactive-month';
  selectedMonth.dataset.monthActive = true;
  selectedMonth.classList.remove('inactive-month');
  selectedMonth.className += 'month';
  monthComponent.style = "display:block;";
  eventComponent.style="display:block;";
  yearComponent.style="display:none;"
  //Get events of month
  var relevantEvents = [];
  let eventsThisMonth = monthComponent.querySelector('[class="month"]').querySelectorAll('[data-event]');
  let availableEvents = document.getElementById('event-widget').children;
  changeEventsMonth(selectedMonth);
}

let nextPrevWeek = function(button, monthComponent){
  console.log('nextprevweek');
  if(button.id === 'calendar-next'){
    let month =  monthComponent.querySelector('[class="month"]');
    let weeks = month.children[0].children[0].children;
    var currentWeek;
    for(var i =0; i < weeks.length; i++){
      if(i !== 0 && weeks[i].dataset.currentWeek === 'true'){
        console.log('found the nextprevweek');
        currentWeek = i;
      }
      if(i !== 0){
        weeks[i].style = 'display:none;';
      }
    }
    if((currentWeek+1) !== weeks.length){
      weeks[currentWeek+1].style = '';
      weeks[currentWeek+1].dataset.currentWeek = true;     
      weeks[currentWeek].dataset.currentWeek = '';    
    }else{
      //TODO if at the last week of the month, skip to next month here
      //TODO: change next button label
    }
  }
  else{
    let month =  monthComponent.querySelector('[class="month"]');
    let weeks = month.children[0].children[0].children;
    var currentWeek;
    for(var i = 0; i < weeks.length; i++){
      if(i !== 0 && weeks[i].dataset.currentWeek === 'true'){
        console.log('found the nextprevweek');
        currentWeek = i;
      }
      if(i !==0){
        weeks[i].style = 'display:none;';
      }
    }
    if((currentWeek-1) !== -1){
      weeks[currentWeek-1].style = '';
      weeks[currentWeek-1].dataset.currentWeek = true;      
      weeks[currentWeek].dataset.currentWeek = '';    
    }else{
      //TODO if at the last week of the month, skip to prev month here
      //TODO: change previous button label
    }
  }
}

let selectWeek = function(element){
  console.log('selectWeek()');
  console.log(element);
  changeCalendarNavigationWeek();
  let weekDays = element.parentNode.querySelectorAll('[class="selected-day"]');
  for(var i = 0; i < weekDays.length;i++){
    weekDays[i].classList.remove('selected-day');
    weekDays[i].className += 'unselected-day';
  }
  element.children[0].classList.remove('unselected-day');
  element.children[0].className += 'selected-day';
  let calendarMonthComponent = document.getElementById('calendar-widget');
  let nextWeekButton = document.getElementById('calendar-next');
  let prevWeekButton = document.getElementById('calendar-prev');
  nextWeekButton.removeAttribute('onclick');
  nextWeekButton.removeEventListener('click',changeMonth);
  prevWeekButton.removeAttribute('onclick');
  nextWeekButton.onclick = function(){nextPrevWeek(this, calendarMonthComponent);};
  prevWeekButton.onclick = function(){nextPrevWeek(this, calendarMonthComponent);};

  let firstParent = element.parentNode;
  firstParent.dataset.currentWeek = true;
  let parentElement = firstParent.parentNode;
  let parentChildren = parentElement.children;
  for(var child = 0; child < parentChildren.length; child++)
  {
    if(parentChildren[child] === firstParent){
      changeEventsWeek(firstParent, element);
    }
    else{
      if(child === 0){
      
      }
      else{
        parentChildren[child].style = "display:none;";
      }
    }
  }
  setBackToMonth(parentElement);
}
let backToMonth = function(parentElement, firstParent){
  console.log("backToMonth()");
changeCalendarNavigationMonth();
let nextWeekButton = document.getElementById('calendar-next');
  let prevWeekButton = document.getElementById('calendar-prev');
  nextWeekButton.onclick = function(){changeMonth(calendarMonthComponent, true);};
  prevWeekButton.onclick = function(){changeMonth(calendarMonthComponent, false);};
  //let eventComponent = document.getElementById('event-widget');
  console.log(parentElement.querySelectorAll('[class="selected-day"]'));
  let selectedDays = parentElement.querySelectorAll('[class="selected-day"]');
  
  for(var i = 0; i < selectedDays.length; i++){
    selectedDays[i].classList.remove('selected-day');
    selectedDays[i].className += 'unselected-day';
  }
  /*let navigationBar = document.getElementById('calendar-navigation-bar');
  navigationBar.classList.remove('active-navigation-bar');
  navigationBar.className += ' inactive-navigation-bar';*/
  let parentChildren = parentElement.children;
  for(var child = 0; child < parentChildren.length; child++){
    parentChildren[child].style = "";
  }
  let calendarOverviewComponent = document.getElementById('calendar-overview-widget');
  let calendarMonthComponent = document.getElementById('calendar-widget');
  let statusBarBack = document.getElementById('calendar-back-button');
  statusBarBack.onclick = function(){goBackToYear(calendarMonthComponent, calendarOverviewComponent);};
  changeEventsMonth(calendarMonthComponent.querySelector('[class="month"]'));
}

//TODO bind this during selectWeek
let setBackToMonth = function(parentElement){
  console.log('setBackToMonth');
  let calendarMonthComponent = document.getElementById('calendar-widget');
  let statusBarBack = document.getElementById('calendar-back-button');
  statusBarBack.onclick = function(){return false};
  statusBarBack.onclick = function(){backToMonth(parentElement);};
  /*let nextWeekButton = document.getElementById('calendar-next');
  let prevWeekButton = document.getElementById('calendar-prev');
  nextWeekButton.onclick = function(){changeMonth(calendarMonthComponent, true);};
  prevWeekButton.onclick = function(){changeMonth(calendarMonthComponent, false);};*/

}

let goBackToYear = function(monthComponent, yearComponent, eventComponent){
  monthComponent.style = "display:none;";
  yearComponent.style="display:block;" ;
  eventComponent.style="display:none;";
  let calendarNavigationBar = document.getElementById('calendar-navigation-bar');
  calendarNavigationBar.className += ' display-none';
  //TODO: Needs to scroll to whatever month was focused
  let activeMonth = monthComponent.querySelector('[data-month-active="true"]');
  let month = activeMonth.dataset.month;
  let year = activeMonth.dataset.year;
  let statusBarLabel = document.getElementById('calendar-status-bar-label');
  statusBarLabel.textContent = activeMonth.dataset.year;
  let activeMonthYearComponent = yearComponent.querySelector('[data-month="'+month+'"][data-year="'+year+'"]');  
  let activeMonthCoordinates = activeMonthYearComponent.getBoundingClientRect();
  window.scrollTo(activeMonthCoordinates.x, activeMonthCoordinates.y);
  let statusBarBack = document.getElementById('calendar-back-button');
  statusBarBack.classList.remove('display-none');
  statusBarBack.className += ' display-none';

}

let initTransitionController = function(){
  console.log('initTransitionController()');
  let calendarOverviewComponent = document.getElementById('calendar-overview-widget');
  let calendarMonthComponent = document.getElementById('calendar-widget');
  let eventComponent = document.getElementById('event-widget');
  for(var month = 0; month < calendarOverviewComponent.children.length; month++){
    let temp = calendarOverviewComponent.children[month];
    temp.onclick = function(){selectMonth(this, calendarMonthComponent, calendarOverviewComponent, eventComponent)};
  }
  let statusBarBack = document.getElementById('calendar-back-button');
  statusBarBack.onclick = function(){ 
    goBackToYear(calendarMonthComponent, calendarOverviewComponent, eventComponent);
  };
  statusBarBack.classList.remove('display-none');
  statusBarBack.className += ' display-none';
  let days = calendarMonthComponent.querySelectorAll('[class="calendar-day"]');
  for(var day = 0; day < days.length; day++){
    days[day].onclick = function(){
      selectWeek(this);
    }
  }
  let calendarNavigationBar = document.getElementById('calendar-navigation-bar');
  calendarNavigationBar.className += ' display-none';

  let nextWeekButton = document.getElementById('calendar-next');
  let prevWeekButton = document.getElementById('calendar-prev');
  hideCalendarNavigation();
  nextWeekButton.onclick = function(){changeMonth(calendarMonthComponent, true);};
  prevWeekButton.onclick = function(){changeMonth(calendarMonthComponent, false);};
}

initTransitionController();
