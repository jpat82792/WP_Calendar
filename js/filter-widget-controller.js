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

  var baseFilterController = document.getElementById('base-filter-controller');
  baseFilterController.dataset.importance = importance;
  let events = document.getElementById('event-widget').children;

  for(var i = 0 ; i < events.length; i++){
    if(i !== 0){
      if(importance !== 'all'){
        if(!(events[i].dataset.eventImportance ===importance) ){
          events[i].classList.remove('inactive-event-importance');
          events[i].classList.add('inactive-event-importance');
        }
        else{
          events[i].classList.remove('inactive-event-importance');
        }
      }
      else{
        console.log('okay??');
        events[i].classList.remove('inactive-event-importance');
      }
    }
  }

  let days = document.getElementById('calendar-widget').querySelectorAll('[data-event]');
  var daysEvents = [];
  for(var i =0; i< days.length; i++){
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

  for(var i = 0 ; i < days.length;i++){
    let actualDay = days[i];
    var interestingImportance = false;
    var recommendedImportance = false;
    var requiredImportance = false;
    for(var j = 0 ; j < daysEvents[i].length; j++){
      console.log(i)
      console.log(daysEvents[i][j].category);
      console.log(baseFilterController.dataset.category);
      if(daysEvents[i][j].category === baseFilterController.dataset.category || baseFilterController.dataset.category === 'all'){
        console.log('Do I get here?');
        if(('required' === importance)){
          requiredImportance = true;
        }
        if ('recommended'===importance){
          recommendedImportance = true;
        }
        if('interesting' === importance){
          interestingImportance = true;
        }
        if('all' === importance){
          requiredImportance = true;
          recommendedImportance = true;
          interestingImportance = true;
        }
      }
    }
    updateImportances(interestingImportance, recommendedImportance, requiredImportance, days[i]);
  }  
}


let filterCategories = function(selectedCategory){
  console.log('filterCategories()');
  let categoryId = selectedCategory.dataset.categoryId
  filterCategoryEvents(categoryId, selectedCategory);
}

let updateImportances = function(interesting, recommended, required, day){
  console.log('updateImportances()');

  var dayData = day.dataset.event.split(',');
  var dayCategory = dayData[1];
  var dayImportance = dayData[2].split(')')[0];
  
  var baseFilterController = document.getElementById('base-filter-controller');
  var currentCategory = baseFilterController.dataset.category;
  currentImportance = baseFilterController.dataset.importance;

  let eventMarkerContainer = day.nextElementSibling;
  for(var i = 0 ; i < eventMarkerContainer.children.length ;i++ ){
    let temp = eventMarkerContainer.children[i];
    temp.classList.remove('unselected-event-marker');
  }


  if(!interesting){
    let marker = eventMarkerContainer.getElementsByClassName('event-marker interesting');
    if(marker[0] === undefined){

    }
    else{
      marker[0].classList.add('unselected-event-marker');
    }
  }

  if(!recommended){
    let marker = eventMarkerContainer.getElementsByClassName('event-marker recommended');
    if(marker[0] === undefined){

    }
    else{
      marker[0].classList.add('unselected-event-marker');
    }
  }

  if(!required ){
    let marker = eventMarkerContainer.getElementsByClassName('event-marker required');
    if(marker[0] === undefined){

    }
    else{
      marker[0].classList.add('unselected-event-marker');
    }
  }
}



let filterCategoryEvents = function(categoryId, element){
  console.log('filterCategoryEvents()');
  console.log(element.textContent);
  let baseFilterController = document.getElementById('base-filter-controller');
  baseFilterController.dataset.category = categoryId;
  let currentClassLabel = document.getElementById('category-filter-button');
  currentClassLabel.children[0].textContent = element.textContent;
  let events = document.getElementById('event-widget').children;
  for(var i = 0 ; i < events.length;i++){
    if(categoryId !== 'all'){
      if(events[i].dataset.categoryId === categoryId || i === 0){
        events[i].classList.remove('unselectable-event');
      }
      else{
        events[i].classList.remove('unselectable-event');
        events[i].className += ' unselectable-event';
      }
    }
    else{
      events[i].classList.remove('unselectable-event');
    }
  }
  //TODO: remove any unecessary event markers in month
  let days = document.getElementById('calendar-widget').querySelectorAll('[data-event]');
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
  for(var i = 0 ; i < days.length; i++){
    let actualDay = days[i];
    var interestingImportance = false;
    var recommendedImportance = false;
    var requiredImportance = false;
    for(var j = 0 ; j < daysEvents[i].length; j++){
      if(daysEvents[i][j].category === categoryId){
        if(baseFilterController.dataset.importance === "interesting"){
          interestingImportance = true;
        }
        else if(baseFilterController.dataset.importance === 'recommended'){
          recommendedImportance = true;
        }
        else if(baseFilterController.dataset.importance === 'required'){
          requiredImportance = true;
        }
        else if(baseFilterController.dataset.importance === 'all'){
          interestingImportance = true;
          recommendedImportance = true;
          requiredImportance = true;
        }
      }      
    }
    updateImportances(interestingImportance, recommendedImportance, requiredImportance, actualDay);
  }  
}
