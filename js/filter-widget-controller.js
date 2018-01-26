console.log('filter-widget-controller');

let displayCategoryFilterDropdown = function(){
  console.log('displayCategoryFilterDropdown()');
  let categoryFilter = document.getElementById('category-filter');
  categoryFilter.className += ' active-filter-dropdown';
}

let dismissCategoryFilterDropdown = function(){
  console.log('dismissCategoryFilterDropdown()');
  let categoryFilter = document.getElementById('category-filter');
  categoryFilter.classList.remove('active-filter-dropdown');
}

let displayImportanceFilterDropdown = function(){
  console.log('displayImportanceFilterDropdown()');
  let importanceFilter = document.getElementById('importance-filter');
  importanceFilter.className += ' active-filter-dropdown';
}

let dismissImportanceFilterDropdown = function(){
  console.log('disdplayImportanceFilterDropdown()');
  let importanceFilter = document.getElementById('importance-filter');
  importanceFilter.classList.remove('active-filter-dropdown');
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
  let importanceList = document.getElementById('importance-filter').querySelector('[class="importance-container"]').children;
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
  let events = document.getElementById('event-widget').children;
  for(var i = 0 ; i < events.length; i++){
    if(i !== 0){
      if(!(events[i].dataset.eventImportance ===importance)){
        events[i].classList.remove('inactive-event-importance');
        events[i].className += ' inactive-event-importance';
      }
      else{
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
  console.log('dayEvents');
  console.log(daysEvents);
  for(var i = 0 ; i < days.length;i++){
    let actualDay = days[i];
    var interestingImportance = false;
    var recommendedImportance = false;
    var requiredImportance = false;
    if('required' === importance){
      requiredImportance = true;
    }
    if ('recommended'===importance){
      recommendedImportance = true;
    }
    if('interesting' === importance){
      interestingImportance = true;
    }
    updateImportances(interestingImportance, recommendedImportance, requiredImportance, days[i]);
  }  
}


let filterCategories = function(selectedCategory){
  console.log('filterCategories()');
  let categoryId = selectedCategory.dataset.categoryId
  filterCategoryEvents(categoryId);
}

let updateImportances = function(interesting, recommended, required, day){
  console.log('updateImportances()');
  console.log(day);
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
      marker[0].className += ' unselected-event-marker';
      console.log(marker);
    }
  }
  if(!recommended){
    let marker = eventMarkerContainer.getElementsByClassName('event-marker recommended');
    if(marker[0] === undefined){

    }
    else{
      marker[0].className += ' unselected-event-marker';
      console.log(marker);
    }
  }
  if(!required){
    let marker = eventMarkerContainer.getElementsByClassName('event-marker required');
    if(marker[0] === undefined){

    }
    else{
      marker[0].className += ' unselected-event-marker';
      console.log(marker);
    }
  }
  console.log('to end');
}

let filterCategoryEvents = function(categoryId){
  console.log('filterCategoryEvents()');
  let events = document.getElementById('event-widget').children;
  console.log(events);
  for(var i = 0 ; i < events.length;i++){
    
    if(events[i].dataset.categoryId === categoryId || i === 0){
      events[i].classList.remove('unselectable-event');
    }
    else{
      events[i].classList.remove('unselectable-event');
      events[i].className += ' unselectable-event';
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
  for(var i = 0 ; i < days.length;i++){
    let actualDay = days[i];
    var interestingImportance = false;
    var recommendedImportance = false;
    var requiredImportance = false;
    for(var j = 0 ; j < daysEvents[i].length; j++){
      //check if this event is still viable
      if(daysEvents[i][j].category === categoryId){
        if(daysEvents[i][j].importance === "interesting"){
          interestingImportance = true;
        }
        else if(daysEvents[i][j].importance === 'recommended'){
          recommendedImportance = true;
        }
        else if(daysEvents[i][j].importance === 'required'){
          requiredImportance = true;
        }
      }      
    }
    updateImportances(interestingImportance, recommendedImportance, requiredImportance, days[i]);
  }  
}
