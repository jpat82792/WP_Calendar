<?php



function create_event_content($day, $month, $year, $event, &$event_widget_content){
  $event_id = $event->event_id;
  $formatted_day = (int) $day;
  $current_month = (int) date('m', time());
  $current_year = (int) date('Y', time());

//  $current_month = 
  $class_name = ((((int)$month)==$current_month)&&(((int)$year)==$current_year)) ? 'active-event' : 'inactive-event';
  $event_widget_content .= '<div class="'.$class_name.'" data-event="'.$event_id.'" data-day="'.$day.'" data-month="'.$month.'" data-event-content="'.$event->event_description.'" data-year="'.$year.'" data-category="'.$event->category_title.'" data-category-id="'.$event->event_category_id.'" data-event-importance="'.$event->event_importance.'"><div class="day-number-label '.$event_importance_class.'">'.(string) $formatted_day.'</div><div class="day-importance-indicator '.$event->event_importance.'"></div><h3 class="event-month">'.convert_number_to_month($month).'</h3> <h4 class="event-category">'.$event->category_title.'</h4><h2 class="event-title">'.$event->event_title.'</h2><article class="event-description">'.$event->event_description.'</article></div>';
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

function set_current_event_date(&$day, &$month, &$year, $date){
  $date_array = explode('-', $date->event_start_date);
  $day = $date_array[2];
  $month = $date_array[1];
  $year = $date_array[0];
}

function set_event_meta_data($event, $comma){
  $temp;
  if($comma){
   $temp = '('.$event->event_id .','. $event->event_category_id.','.$event->event_importance.'), ';
  }
  else{
   $temp = '('.$event->event_id .','. $event->event_category_id.','.$event->event_importance.')';
  }
  return $temp;
}

function set_day_without_initial_event($list_day, $month, $year, $recurring_events, $possible_importances, $possible_importances_present, &$event_string, &$calendar, &$event_widget_content){
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
        $events_string .= set_event_meta_data($current_event, false);
      }
      else{
        $events_string .= set_event_meta_data($current_event, true);
      }
    }
    $events_string .= '"';
  }
  else{
  }
  $calendar .= '<p '.$events_string.'>'.show_event($possible_importances_present).'</p>';
}

function set_day_with_initial_event($list_day, $month, $year, $recurring_events, $possible_importances, $possible_importances_present, &$event_string, &$calendar, &$check_for_events, &$results, &$event_widget_content){
  while($check_for_events){
  //TODO: add event to $holding_array. Check to see if an event should be triggered based on recurrence. 
    $earliest_event =  $results[0];
    if($earliest_event->event_id !== NULL){
      create_event_content($list_day, $month, $year, $earliest_event, $event_widget_content);
      $events_string .= set_event_meta_data($earliest_event, false);
    }


    check_importance($possible_importances, $possible_importances_present, $earliest_event);
    if($earliest_event->recursion !== NULL){
      initial_reccurring_event_preparation($earliest_event, $list_day);
      $recurring_events[] = $earliest_event;
    }
    array_shift($results);
    $earliest_event =  $results[0];
    set_current_event_date($event_day, $event_month, $event_year, $earliest_event);
    if((int)$event_day==$list_day && (int)$event_month==$month && (int)$event_year==$year){
      $check_for_events = true;
      set_current_event_date($event_day, $event_month, $event_year, $earliest_event);
      if((int)$event_day==$list_day && (int)$event_month==$month && (int)$event_year==$year){
        $events_string .= ', ';
      }
      else{
        $events_string .= ', ';
      }
    }
    else{
      $check_for_events = false;
    }
  }
  //TODO: iterate through recurring events to see if any event needs to be marked again
  $event_id_array = check_recurring_events($list_day, $month, $year, $recurring_events, $possible_importances, $possible_importances_present);
  if(count($event_id_array)>0){
    $number_of_events = count($event_id_array);
    $i = 0;
    foreach($event_id_array as $current_event){
      create_event_content($list_day, $month, $year, $current_event,$event_widget_content);
      if(++$i == $number_of_events){
        $events_string .= set_event_meta_data($current_event, false);
      }
      else{
        $events_string .= set_event_meta_data($current_event, true);
      }
    }
    $events_string .= '';
  }
  else{
  }
  $events_string .= '"';
  $calendar .= '<p '.$events_string.'>'.show_event($possible_importances_present).'</p>';
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
    $today_the_day = false;
    $event_day;
    $event_month;
    $event_year;
    set_current_event_date($event_day, $event_month, $event_year, $earliest_event);
    //Event is today, alter day number
		  $calendar.= '<div class="day-number">'.$list_day.'</div>';
    if((int)$event_day == $list_day && (int) $event_month ==$month && (int)$event_year==$year){
      $today_the_day = true;
    }
    $possible_importances = ["required", "recommended", "interesting", ""];
    $possible_importances_present = array("required" => false, "recommended"=> false, "interesting" => false);
    if(!$today_the_day){
      set_day_without_initial_event($list_day, $month, $year, $recurring_events, $possible_importances, $possible_importances_present, $event_string, $calendar, $event_widget_content);
    }
    else{
      $check_for_more_events = true;
      $events_string .= 'data-event="';
      set_day_with_initial_event($list_day, $month, $year, $recurring_events, $possible_importances, $possible_importances_present, $event_string, $calendar, $check_for_more_events, $results, $event_widget_content);
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
  	echo('<div class="month" data-month-active="true" data-month="'.esc_attr($month).'" data-year="'.esc_attr($year).'">'.$calendar.'</div>');

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
  echo('<div id="calendar-widget" style="display:block;">');
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
  echo('</div>');
  echo('<div id="event-widget"><h3 class="event-status-label"> events in '.date('F').'</h3>');
  echo($event_widget_content);
  echo('</div>');

  //echo(add_events_to_event_viewer($results_for_events));
}
?>
