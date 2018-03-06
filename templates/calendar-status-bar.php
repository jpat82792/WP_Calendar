<?php
function set_calendar_status_bar($current_state){
  $full_months = array('january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september','october','november','december');
  $abbreviated_months = array('jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec');
  //TODO: add option in admin to change whether or not the month is abbreviated or full text
  $full_months_length = count($full_months);
  $current_month_text;
  for($month_id = 0; $month_id < $full_months_length; $month_id++){
      error_log((string) ($month_id+1));
    if($current_state == ($month_id+1)){
      $current_month_text = $full_months[$month_id];
    }
  }
?>
<div id="calendar-status-bar">
  <button id="calendar-back-button"><span class="fa fa-angle-left"></span></button><button class="clean-button" id="prev-month-btn">"<"</button><label id="calendar-status-bar-label"><?php  echo($current_month_text); ?></label><button class="clean-button" id="next-month-btn">">"</button>
</div>
<?php
}
?>
