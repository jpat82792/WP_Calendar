<?php
  function get_all_events(){
    global $wpdb;
   // $categories = get_all_event_categories();
    $category_table_name = $wpdb->prefix . 'jpcalendar_categories';
    $event_table_name =$wpdb->prefix . 'jpcalendar_events';
    $query = "SELECT events.*, categories.* FROM $event_table_name AS events INNER JOIN $category_table_name AS categories ON events.event_category_id=categories.category_id ORDER BY events.event_start_date, events.event_start_time ASC";
    $results = $wpdb->get_results($query);
    return $results;
  }

//Check event importance
function check_importance($importances, &$importances_status, $event){
  foreach($importances as $important){
    if($event->event_importance === $important){
      $importances_status[$important] = true;
    }
  }
}

function initial_reccurring_event_preparation(&$event, $day_number){
  switch($event->recursion){
    case 'weekly':
      $event->days_since = -1;
      break;
    case 'daily':
      break;
    case 'monthly':
      $event->day_recur_number = $day_number;
      $event->days_past = 0;
      break;
  }
}

function does_event_marker_exist(&$event_string, $character){
  if(strpos($event_string, $character) !== false){
    
  }
  else{

    $event_string = $event_string.$character;
  }
}

//TODO: add event arg here and pass eventID as data-attr
//Determine which type of marker to render
function get_event_marker(&$event_string, $importance_key){
  switch ($importance_key){
    case 'required':
      does_event_marker_exist($event_string, '<div class="event-marker required"></div>');
      break;
    case 'recommended':
      does_event_marker_exist($event_string, '<div class="event-marker recommended"></div>');
      break;
    case 'interesting':
      does_event_marker_exist($event_string, '<div class="event-marker interesting"></div>');
      break;
    default:
      does_event_marker_exist($event_string, '<div class="event-marker uncategorized"></div>');
  }
}
//TODO: pass event arg to get event marker.
function check_recurring_events($day, $month, $year, &$recurring_events, $possible_importances, &$possible_importances_present){
  $events_array = array();
  if(is_array($recurring_events)){
  foreach($recurring_events as $key => $recurring_event){
    $event_start_date = $recurring_event->event_start_date;
    $list_early_arrays = explode('-', $event_start_date);
    $start_day = (int) $list_early_arrays[2];
    $start_month = (int) $list_early_arrays[1];
    $start_year = (int) $list_early_arrays[0];
    $event_end_date = $recurring_event->event_end_date;
    $list_of_arrays = explode('-', $event_end_date);
    $event_day = (int) $list_of_arrays[2];
    $event_month = (int) $list_of_arrays[1];
    $event_year = (int) $list_of_arrays[0];
    if($recurring_event->recursion == 'daily'){
      if(($event_day == $day && $event_month == $month && $event_year == $year) && !($start_day == $day && $start_month ==$month && $start_year == $year)){
        unset($recurring_events[$key]);
      }
      else{
        check_importance($possible_importances, $possible_importances_present, $recurring_event);
        $events_array[] = $recurring_event;
      }
    }
    else if($recurring_event->recursion == 'weekly'){
      if($recurring_event->days_since == 6){
        check_importance($possible_importances, $possible_importances_present, $recurring_event);
        $recurring_event->days_since = 0;
        $events_array[] = $recurring_event;
      }
      else{
        $recurring_event->days_since++;
      }
      if($event_day == $day && $event_month == $month && $event_year == $year){
        unset($recurring_events[$key]);
      }            
    }
    else if($recurring_event->recursion == 'monthly'){
      if(($recurring_event->day_recur_number) == $day && !($start_day == $day && $start_month ==$month && $start_year == $year)){
        check_importance($possible_importances, $possible_importances_present, $recurring_event);
        $events_array[] = $recurring_event;
      }
      if($event_day == $day && $event_month == $month && $event_year == $year){
        unset($recurring_events[$key]);
      }    
    }
  }
  }
  return $events_array;
}
function collect_events($current_events){
  $event_id_string = '';
  foreach($current_events as $event){
    $event_id_string .= '('.$event->event_id.','.$event->event_category_id.','.$earliest_event->event_importance.')';
  }
}
//Render event
function show_event($importances_status){
  $event_string = '<div class="event-marker-container">';
  foreach($importances_status as $status => $value){
    if($value == 1){
      get_event_marker($event_string, $status);
    }
  }
  $event_string .= '</div>';
  return $event_string;
}

function set_year_view_month($month, $year, $current_month, $clear_row, &$results, &$recurring_events){
  $calendar;
	/* table headings */
	$headings = array('S','M','T','W','TH','F','S');
  if($clear_row){
    $calendar = '<table cellpadding="0" cellspacing="0" class="overview-calendar clear-row">';
  }
  else{
    $calendar = '<table cellpadding="0" cellspacing="0" class="overview-calendar ">';      
  }
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
		/* add in the day number */


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
      check_recurring_events($list_day, $month, $year, $recurring_events, $possible_importances, $possible_importances_present);
      $calendar .= show_event($possible_importances_present);//'<p class="event-day">'..'</p>';
     /*$event_id_array = check_recurring_events($list_day, $month, $year, $recurring_events, $possible_importances, $possible_importances_present);
      $events_string = '';
      if(count($event_id_array)>0){
        $number_of_events = count($event_id_array);
        $i = 0;
        $events_string .= 'data-event="';
        foreach($event_id_array as $current_event){
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
      $calendar .= '<p '.$events_string.'>'.show_event($possible_importances_present).'</p>';*/
    }
    else{
      $check_for_more_events = true;

      //TODO: add two variables to store state of checks on if more events are in holding
      //The other variable should store if the next date in array is not today.
      $count = 0;
      
      while($check_for_more_events){
    //TODO: add event to $holding_array. Check to see if an event should be triggered based on recurrence. 
        $earliest_event =  $results[0];
        $list_of_arrays = explode('-', $earliest_event->event_start_date);
        $event_day = $list_of_arrays[2];
        $event_month = $list_of_arrays[1];
        $event_year = $list_of_arrays[0];
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
        }
        else{
          $check_for_more_events = false;
        }
      }
      //TODO: iterate through recurring events to see if any event needs to be marked again
      check_recurring_events($list_day, $month, $year, $recurring_events, $possible_importances, $possible_importances_present);
      $calendar .= show_event($possible_importances_present);//'<p>'..'</p>';
      //TODO: Add switch to add events. There should be 4 possible for sondras
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
  $final_calendar;
	/* all done, return result */
  if(!$current_month){
    if($clear_row){
    	$final_calendar = '<div class="year-month clear-row" data-month-current="false" data-month="'.esc_attr($month).'" data-year="'.esc_attr($year).'" >'.$calendar.'</div>';
    }
    else{
    	$final_calendar = '<div class="year-month" data-month-current="false" data-month="'.esc_attr($month).'" data-year="'.esc_attr($year).'" >'.$calendar.'</div>';
    }

  }
  else{
    if($clear_row){
  	  $final_calendar = '<div class="year-month clear-row"  data-month-current="true" data-month="'.esc_attr($month).'" data-year="'.esc_attr($year).'">'.$calendar.'</div>';
    }
    else{
  	  $final_calendar = '<div class="year-month"  data-month-current="true" data-month="'.esc_attr($month).'" data-year="'.esc_attr($year).'">'.$calendar.'</div>';
    }

  }
  return $final_calendar;
}
//TODO: Need to figure out how to group months into viewable components that have the same number of months.
//
function construct_year_view_calendar($future_range, $past_range){
  $current_year = date('Y');
  $current_month = date('m'); 
  $recurring_events = array();
  $plain_text_current_month = date('M');
  $numeric_current_year = (int) 2017;
  $numeric_current_month = (int) 12; 
  $results = get_all_events();
  set_calendar_status_bar($numeric_current_month);
  echo('<div id="calendar-overview-widget">');
  $current_months = '';
  $current_months_count = 0;
  $year_view = '';
  $has_current_month = false;
  for($year = $past_range; $year >= 1; $year--){
    $past_year = $numeric_current_year - $year;

    //Determines if clear-row is needed

    if($year == $future_range){
      for($month = $numeric_current_month; $month <= 12; $month++){
        $clear_row =  ($current_months_count % 2 == 1 ? false : true);
        $current_months .= set_year_view_month($month, $past_year, false, $clear_row, $results, $recurring_events);
        $current_months_count++;
      }
    }
    else{
      for($month = $current_numeric_month; $month <= 12; $month++){
        $clear_row =  ($current_months_count % 2 == 1 ? false : true);
        $current_months .= set_year_view_month($month, $past_year, false, $clear_row, $results, $recurring_events);
        $current_months_count++;
      }
    }

  }
  //Current year and future
  for($year = 0; $year <= $future_range; $year++){
    $loop_year = $year + $numeric_current_year;
    //Determines if clear-row is needed
    $clear_row =  ($current_months_count % 2) ? false : true;

    if($year !== $future_range){
      for($month = 1; $month <= 12; $month++){
        $clear_row =  ($current_months_count % 2 == 1 ? false : true);
        if($month == $numeric_current_month && $year==0){
          $current_months .= set_year_view_month($month, $loop_year, true, $clear_row, $results, $recurring_events);
        }
        else{
          $current_months .= set_year_view_month($month, $loop_year, false, $clear_row, $results, $recurring_events);
        }
        $current_months_count++;
      }
    }
    else{
      for($month = 1; $month <= $numeric_current_month; $month++){
        if($month == $numeric_current_month && $year==0){
          echo("<h1>Current Month</h1>");
        }
        $clear_row =  ($current_months_count % 2 == 1 ? false : true);
        $current_months .= set_year_view_month($month, $loop_year, false, $clear_row, $results, $recurring_events);
        $current_months_count++;
      }
    }
  }
echo($current_months);
echo('</div>');
}
?>
