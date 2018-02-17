console.log('filter-widget-controller');

let displayCategoryFilterDropdown = function(){
  console.log('displayCategoryFilterDropdown()');
  let categoryFilter = document.getElementById('category-filter');
  categoryFilter.className += ' active-filter-dropdown';
  var calendarBar = document.getElementsByClassName('calendar-filters-bar')[0];
  calendarBar.classList.add('active-filter-bar');
}

let dismissCategoryFilterDropdown = function(){
  console.log('dismissCategoryFilterDropdown()');
  let categoryFilter = document.getElementById('category-filter');
  categoryFilter.classList.remove('active-filter-dropdown');
  var calendarBar = document.getElementsByClassName('calendar-filters-bar')[0];
  calendarBar.classList.remove('active-filter-bar');
}

let displayImportanceFilterDropdown = function(){
  console.log('displayImportanceFilterDropdown()');
  let importanceFilter = document.getElementById('importance-filter');
  importanceFilter.className += ' active-filter-dropdown';
  var calendarBar = document.getElementsByClassName('calendar-filters-bar')[0];
  calendarBar.classList.add('active-filter-bar');
}

let dismissImportanceFilterDropdown = function(){
  console.log('disdplayImportanceFilterDropdown()');
  let importanceFilter = document.getElementById('importance-filter');
  importanceFilter.classList.remove('active-filter-dropdown');
  var calendarBar = document.getElementsByClassName('calendar-filters-bar')[0];
  calendarBar.classList.remove('active-filter-bar');
}

let initFilterWidget = function(){
  console.log('initFilterWidget()');
  let filterWidget = document.getElementById('category-filter-button');
  let importanceFilterButton = document.getElementById('importance-filter-button');
  importanceFilterButton.onclick = function(){displayImportanceFilterDropdown();};
  filterWidget.onclick = function(){displayCategoryFilterDropdown();};
  let categoryFilterWidgetDismissButton = document.getElementById('close-category-filter');
  let importanceFilterWidgetDismissButton = document.getElementById('close-importance-filter');
  categoryFilterWidgetDismissButton.onclick = function(){dismissCategoryFilterDropdown();};
  importanceFilterWidgetDismissButton.onclick = function(){dismissImportanceFilterDropdown();};
  let categoryList = document.getElementById('category-filter').querySelector('[class="category-container"]').children;
  for(var i = 0 ; i < categoryList.length; i++){
    categoryList[i].onclick = function(){filterCategories(this);};
  }
  let importanceList =document.getElementById('importance-filter').querySelectorAll('[data-importance]');
  console.log("importanceList");
  console.log(importanceList);
  for(var i = 0 ; i < importanceList.length; i++){
    importanceList[i].onclick = function(){filterImportances(this)}
  }
  
}
initFilterWidget();

let filterImportances = function(selectedImportance){
  console.log('filterImportances()');
  let importance = selectedImportance.dataset.importance;
  filterImportanceEvents(importance);
}



let filterImportanceEvents = function(importance){
  console.log('filterEventImportances()');
  updateBaseFilterController('importance', importance);
  let events = document.getElementById('event-widget').children;
  setEvents('inactive-event-importance', 'eventImportance', importance);
  let days = document.getElementById('calendar-widget').querySelectorAll('[data-event]');
  var daysEvents = getDaysEvents(days);
  checkDayForImportances(days, daysEvents);
  dismissImportanceFilterDropdown();
}

var getDaysEvents = function(days){
  var daysEvents = [];
  //This loop will get all the event objects per day
  for (var i = 0 ; i < days.length; i++){
    daysEvents[i] = days[i].dataset.event.split(', ');
    for(var j = 0 ; j < daysEvents[i].length; j++){
      var temp = daysEvents[i][j];
      temp = temp.replace('(', '');
      temp = temp.replace(')', '');
      temp = temp.split(',');
      daysEvents[i][j] = {};
      daysEvents[i][j].event = temp[0];
      daysEvents[i][j].category = temp[1];
      daysEvents[i][j].importance = temp[2];
    }
  }
  return daysEvents;
}

var checkDayForImportances = function(days, daysEvents){
  console.log("checkDayForImportances()");
  let baseFilterController = document.getElementById('base-filter-controller');
  var importance = baseFilterController.dataset.importance;
  var categoryId = baseFilterController.dataset.category;

  for(var i = 0 ; i < days.length; i++){
    let actualDay = days[i];
    var interestingImportance = false;
    var recommendedImportance = false;
    var requiredImportance = false;
    for(var j = 0 ; j < daysEvents[i].length; j++){
      if(daysEvents[i][j].category === categoryId || categoryId === 'all'){
        if(importance === "interesting"){
          interestingImportance = true;
        }
        else if(importance === 'recommended'){
          recommendedImportance = true;
        }
        else if(importance === 'required'){
          requiredImportance = true;
        }
        else if(importance === 'all'){
          interestingImportance = true;
          recommendedImportance = true;
          requiredImportance = true;
        }
      }      
    }
    updateImportances(interestingImportance, recommendedImportance, requiredImportance, actualDay);
  }  
}

let filterCategories = function(selectedCategory){
  console.log('filterCategories()');
  let categoryId = selectedCategory.dataset.categoryId
  filterCategoryEvents(categoryId, selectedCategory);
}

var resetImportances = function(eventMarker){
  for(var i = 0 ; i < eventMarker.children.length; i++){
    var temp = eventMarker.children[i];
    temp.classList.remove('unselected-event-marker');
  }
}
var checkSingleImportance = function(eventMarker, classString){
  var marker = eventMarker.getElementsByClassName(classString);
  if(marker[0] === undefined){}
  else{
    marker[0].classList.add('unselected-event-marker');
  }
}
let updateImportances = function(interesting, recommended, required, day){
  console.log('updateImportances()');
  let eventMarkerContainer = day.nextElementSibling;
  resetImportances(eventMarkerContainer);
  if(!interesting){
    checkSingleImportance(eventMarkerContainer, 'event-marker interesting');
  }
  if(!recommended){
    checkSingleImportance(eventMarkerContainer, 'event-marker recommended');
  }
  if(!required ){
    checkSingleImportance(eventMarkerContainer, 'event-marker required');
  }
}

var changeCategoryLabel = function(text){
  let currentClassLabel = document.getElementById('category-filter-button');
  currentClassLabel.children[0].textContent = text;
}

var updateBaseFilterController = function(attribute, value){
  let baseFilterController = document.getElementById('base-filter-controller');
  baseFilterController.dataset[attribute] = value;
}

var setEvents = function(toggleClass, attribute, value){
  let events = document.getElementById('event-widget').children;
  for(var i = 0 ; i < events.length;i++){
    if(value !== 'all'){
      if(events[i].dataset[attribute] === value || i === 0){
        events[i].classList.remove(toggleClass);
      }
      else{
        events[i].classList.remove(toggleClass);
        events[i].classList.add(toggleClass);
      }
    }
    else{
      events[i].classList.remove(toggleClass);
    }
  }
}

let filterCategoryEvents = function(categoryId, element){
  console.log('filterCategoryEvents()');
  updateBaseFilterController('category', categoryId);
  changeCategoryLabel(element.textContent);
  setEvents('unselectable-event', 'categoryId', categoryId);
  let days = document.getElementById('calendar-widget').querySelectorAll('[data-event]');
  var daysEvents = getDaysEvents(days);
  checkDayForImportances(days, daysEvents);
  dismissCategoryFilterDropdown();
}
