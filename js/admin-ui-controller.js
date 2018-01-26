console.log('admin helper functions');
//TODO: add a toggle between military time and standard time
let setTimePickers = function(){
  let startTimePickerHour = document.getElementById('jpcalendar-start-time-picker-hour');
  let endTimePickerHour = document.getElementById('jpcalendar-end-time-picker-hour');
  let startTimePickerMinute = document.getElementById('jpcalendar-start-time-picker-minute');
  let endTimePickerMinute = document.getElementById('jpcalendar-end-time-picker-minute');
  let hourOptions = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23];
  let minuteOptions = [0,5,10,15,20,25,30,35,40,45,50,55];

}

let showRecurrenceOptions = function(element){
  let parentElement = element.parentNode;
  if(!element.checked){
    let childrenElements = parentElement.querySelectorAll('[class="active-recurrence"]');
    for(var i = 0 ; i < childrenElements.length; i++){
      childrenElements[i].classList.remove('active-recurrence');
      childrenElements[i].className += 'inactive-recurrence';
    }
  }
  else{
    let childrenElements = parentElement.querySelectorAll('[class="inactive-recurrence"]');
    for(var i = 0 ; i < childrenElements.length; i++){
      childrenElements[i].classList.remove('inactive-recurrence');
      childrenElements[i].className += 'active-recurrence';
    }
  }
}

let initAdmin = function(){
  console.log('initAdmin()') ;
  setTimePickers();
  let recurrenceToggle = document.getElementById('recurrence_check');
  showRecurrenceOptions(recurrenceToggle);
  let recurrenceCheckbox = document.getElementById('recurrence_check');
  recurrenceCheckbox.onclick = function(){showRecurrenceOptions(this);};
}

initAdmin();
