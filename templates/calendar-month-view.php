<?php

function convert_number_to_month($int){
  switch($int){
    case 1:
      return 'January';
    case 2:
      return 'February';
    case 3:
      return 'March';
    case 4:
      return 'April';
    case 5:
      return 'May';
    case 6:
      return 'June';
    case 7:
      return 'July';
    case 8:
      return 'August';
    case 9:
      return 'September';
    case 10:
      return 'October';
    case 11:
      return 'November';
    case 12:
      return 'December';
    default:
      return '';
  }
}

function create_event_content($day, $month, $year, $event, &$event_widget_content){
  $event_id = $event->event_id;
  $formatted_day = (int) $day;
  $event_widget_content .= '<div class="inactive-event" data-event="'.$event_id.'" data-day="'.$day.'" data-month="'.$month.'" data-event-content="'.$event->event_description.'" data-year="'.$year.'" data-category="'.$event->category_title.'" data-category-id="'.$event->event_category_id.'" data-event-importance="'.$event->event_importance.'"><div class="day-number-label '.$event_importance_class.'">'.(string) $formatted_day.'</div><h3 class="event-month">'.convert_number_to_month($month).'</h3> <h4 class="event-category">'.$event->category_title.'</h4><h2 class="event-title">'.$event->event_title.'</h2><article class="event-description">'.$event->event_description.'</article></div>';
}

function add_events_to_event_viewer(&$results){
/*  $event_widget = '';
  $event_widget = '<div id="event-widget" style="display:none;"><h3 class="event-status-label">events label</h3>';*/
  foreach($results as $result){
    $date_data = explode('-', $result->event_start_date);
    $start_day = $date_data[2];
    $start_month = $date_data[1];
    $start_year = $date_data[0];
    $event_id = $result->event_id;
    $int_start_day = (int) $start_day;
    $event_importance_class;
    switch ($result->event_importance){
      case 'required':
        $event_importance_class = 'required-color';
        break;
      case 'recommended':
        $event_importance_class = 'recommended-color';
        break;
      case 'interesting':
        $event_importance_class = 'interesting-color';
        break;
      default:
        $event_importance_class = '';
    }

    $event = '<div class="inactive-event" data-event="'.$event_id.'" data-day="'.$start_day.'" data-month="'.$start_month.'" data-event-content="'.$result->event_description.'" data-year="'.$start_year.'" data-category="'.$result->category_title.'" data-category-id="'.$result->event_category_id.'" data-event-importance="'.$result->event_importance.'"><div class="day-number-label '.$event_importance_class.'">'.(string) $int_start_day.'</div><h3 class="event-month">'.convert_number_to_month($start_month).'</h3> <h4 class="event-category">'.$result->category_title.'</h4><h2 class="event-title">'.$result->event_title.'</h2><article class="event-description">'.$result->event_description.'</article></div>';
    $event_widget .= $event;
  }
//  $event_widget .= '</div>';  
  return $event_widget;
}

function set_calendar_month($month, $year, $current_month, &$results, &$recurring_events, &$event_widget_content){
  $calendar;
  
  $calendar = '<table cellpadding="0" cellspacing="0" class="calendar-month">';
	/* table headings */
	$headings = array('S','M','T','W','TH','F','S');
	$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';

	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();

	/* row for week one */
	$calendar.= '<tr class="calendar-row">';

	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
		$calendar.= '<td class="calendar-day-np"> </td>';
		$days_in_this_week++;
	endfor;

	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++):
		$calendar.= '<td class="calendar-day">';
		$calendar.= '<div class="unselected-day"></div>';
		/** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
    $earliest_event = $results[0];
    $list_of_arrays = explode('-', $earliest_event->event_start_date);
    $today_the_day = false;
    $event_day = $list_of_arrays[2];
    $event_month = $list_of_arrays[1];
    $event_year = $list_of_arrays[0];
    //Event is today, alter day number
		  $calendar.= '<div class="day-number">'.$list_day.'</div>';
    if((int)$event_day == $list_day && (int) $event_month ==$month && (int)$event_year==$year){
      $today_the_day = true;
    }
    else{
      
    }
    $possible_importances = ["required", "recommended", "interesting", ""];
    $possible_importances_present = array("required" => false, "recommended"=> false, "interesting" => false);
    if(!$today_the_day){
     $event_id_array = check_recurring_events($list_day, $month, $year, $recurring_events, $possible_importances, $possible_importances_present);
      $events_string = '';
      //TODO: event_widget_content needs to happen here
      if(count($event_id_array)>0){
        $number_of_events = count($event_id_array);
        $i = 0;
        $events_string .= 'data-event="';
        foreach($event_id_array as $current_event){
          create_event_content($list_day, $month, $year, $current_event,$event_widget_content);
          if(++$i == $number_of_events){
            $events_string .= '('.$current_event->event_id .','. $current_event->event_category_id.','.$current_event->event_importance.')';
          }
          else{
            $events_string .= '('.$current_event->event_id .','. $current_event->event_category_id.','.$current_event->event_importance.')'.', ';
          }
        }
        $events_string .= '"';
      }
      else{
      }
      $calendar .= '<p '.$events_string.'>'.show_event($possible_importances_present).'</p>';
    }
    else{
      $check_for_more_events = true;

      //TODO: add two variables to store state of checks on if more events are in holding
      //The other variable should store if the next date in array is not today.
      $count = 0;
      $events_string .= 'data-event="';
      $unchecked_recurrence = true;
      $store_event_date = $earliest_event->event_start_date;
      while($check_for_more_events){
    //TODO: add event to $holding_array. Check to see if an event should be triggered based on recurrence. 
        $earliest_event =  $results[0];
        if($earliest_event->event_id !== NULL){
          create_event_content($list_day, $month, $year, $earliest_event, $event_widget_content);
          $events_string .= '('.$earliest_event->event_id.','.$earliest_event->event_category_id.','.$earliest_event->event_importance.')';
        }


        check_importance($possible_importances, $possible_importances_present, $earliest_event);
        if($earliest_event->recursion !== NULL){
          initial_reccurring_event_preparation($earliest_event, $list_day);
          $recurring_events[] = $earliest_event;
        }
        array_shift($results);
        $earliest_event =  $results[0];
        $list_of_arrays = explode('-', $earliest_event->event_start_date);
        $event_day = $list_of_arrays[2];
        $event_month = $list_of_arrays[1];
        $event_year = $list_of_arrays[0];

        if((int)$event_day==$list_day && (int)$event_month==$month && (int)$event_year==$year){
          $check_for_more_events = true;

          $list_of_arrays = explode('-', $earliest_event->event_start_date);
          $event_day = $list_of_arrays[2];
          $event_month = $list_of_arrays[1];
          $event_year = $list_of_arrays[0];
          if((int)$event_day==$list_day && (int)$event_month==$month && (int)$event_year==$year){
          $events_string .= ', ';
          }
          else{
          $events_string .= ', ';
          }
        }
        else{
          $check_for_more_events = false;
        }
      }
      //TODO: iterate through recurring events to see if any event needs to be marked again
     $event_id_array = check_recurring_events($list_day, $month, $year, $recurring_events, $possible_importances, $possible_importances_present);
      if(count($event_id_array) > 0){
        $number_of_events = count($event_id_array);
        $i = 0;

        foreach($event_id_array as $current_event){
          if($current_event->event_start_date === $store_event_date){
          }
          else{
            create_event_content($list_day, $month, $year, $current_event, $event_widget_content);
            if(++$i == $number_of_events){
            $events_string .= '('.$current_event->event_id .','. $current_event->event_category_id.','.$current_event->event_importance.')'.', ';
            }
            else{
            $events_string .= '('.$current_event->event_id .','. $current_event->event_category_id.','.$current_event->event_importance.')'.', ';
            }
          }
        }

      }
      else{
      }
        $events_string .= '"';
      $calendar .= '<p '.$events_string.'>'.show_event($possible_importances_present).'</p>';
    }
			
		$calendar.= '</td>';
		if($running_day == 6):
			$calendar.= '</tr>';
			if(($day_counter+1) != $days_in_month):
				$calendar.= '<tr class="calendar-row">';
			endif;
			$running_day = -1;
			$days_in_this_week = 0;
		endif;
		$days_in_this_week++; $running_day++; $day_counter++;
	endfor;

	/* finish the rest of the days in the week */
	if($days_in_this_week < 8):
		for($x = 1; $x <= (8 - $days_in_this_week); $x++):
			$calendar.= '<td class="calendar-day-np"> </td>';
		endfor;
	endif;

	/* final row */
	$calendar.= '</tr>';

	/* end the table */
	$calendar.= '</table>';
	
	/* all done, return result */
  if(!$current_month){
	  //$calendar = '<table cellpadding="0" cellspacing="0" class="calendar inactive-month">';
	  echo('<div class="inactive-month" data-month-active="false" data-month="'.esc_attr($month).'" data-year="'.esc_attr($year).'" >'.$calendar.'</div>');
  }
  else{
  	echo('<div class="month"  data-month-active="true" data-month="'.esc_attr($month).'" data-year="'.esc_attr($year).'">'.$calendar.'</div>');

  }
}
function construct_month_view($future_range, $past_range){
  $current_year = date('Y');
  $current_month = date('m'); 
  $plain_text_current_month = date('M');
  $numeric_current_year = (int) $current_year;
  //render month overview
  $results = get_all_events();
  //renders event sections
  $results_for_events = get_all_events();
  $recurring_events = array();
  $numeric_current_month = (int) $current_month; 
  $events_string='';

  //This string will contain all events for display in event feed
  $event_widget_content = '';
//  set_calendar_status_bar($numeric_current_month);
  echo('<div id="calendar-widget" style="display:none;">');
  for($year = $past_range; $year >= 1; $year--){
    
    $past_year = $numeric_current_year - $year;
    if($year == $future_range){
      for($month = $numeric_current_month; $month <= 12; $month++){
        set_calendar_month($month, $past_year, false, $results, $recurring_event, $event_widget_content);
      }
    }
    else{
      for($month = $current_numeric_month; $month <= 12; $month++){
        set_calendar_month($month, $past_year, false, $results, $recurring_event, $event_widget_content);
      }
    }
  }
  //Current year and future
  for($year = 0; $year <= $future_range; $year++){
    $loop_year = $year + $numeric_current_year;
    if($year !== $future_range){
      for($month = 1; $month <= 12; $month++){
        if($month == $numeric_current_month && $year==0){
          set_calendar_month($month, $loop_year, true, $results, $recurring_event, $event_widget_content);
        }
        else{
          set_calendar_month($month, $loop_year, false, $results, $recurring_event, $event_widget_content);
        }
      }
    }
    else{
      for($month = 1; $month <= $numeric_current_month; $month++){
        if($month == $numeric_current_month && $year==0){
          echo("<h1>Current Month</h1>");
        }
        set_calendar_month($month, $loop_year, false, $results, $recurring_event, $event_widget_content);
      }
    }
  }
echo('</div>');

  echo('<div id="event-widget" style="display:none;"><h3 class="event-status-label">events label</h3>');
  echo($event_widget_content);
  echo('</div>');
  //echo(add_events_to_event_viewer($results_for_events));
}
?>
