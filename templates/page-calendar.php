<?php
/*  Template Name: omni-calendar*/
  get_header();
?>

<body>
  <?php
 // require_once(get_template_directory() . '/templates/navbar.php');
  $current_month = date('F');
  require_once(plugin_dir_path(__FILE__) . 'calendar-universal-functions.php');
  require_once(plugin_dir_path(__FILE__) . 'filter-dropdown-view.php');
  require_once(plugin_dir_path(__FILE__) .'calendar-status-bar.php');
  require_once(plugin_dir_path(__FILE__).'calendar-year-view.php');
  require_once(plugin_dir_path(__FILE__).'calendar-month-view.php');

 /* construct_year_view_calendar(1,1);*/
  construct_month_view(1,1);  

  $month_array = ['January','February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
  function determine_date_index($current_month){
    switch($current_month){
      case 'January':
        return 0;
      case 'February':
        return 1;
      case 'March':
        return 2;
      case 'April':
        return 3;
      case 'May':
        return 4;
      case 'June':
        return 5;
      case 'July':
        return 6;
      case 'August':
        return 7;
      case 'September':
        return 8;
      case 'October':
        return 9;
      case 'November':
        return 10;
      default:
        return 11;
    }
  }
    $current_month_index = determine_date_index($current_month);

  function next_month($current_month_offset){
    if($current_month_offset < 11){
      error_log('Not December');
      $offset = $current_month_offset+1;
      $temp = $month_array[$offset];
      return $offset;
    }
    else{
      return 0;
    }
  }
  function prev_month($current_month_offset){
    if($current_month_offset > 0){
      error_log('Not January');
      $temp = $current_month_offset-1;
      return $temp;
    }
    else{
      return 11;
    }
  }
  $next_month =  $month_array[next_month($current_month_index)];
  $prev_month = $month_array[prev_month($current_month_index)];

?>
    <div id="calendar-navigation-bar" class="calendar-navigation-bar">
      <button class="calendar-navigation-bar-button calendar-prev clean-button" id="calendar-prev"><?php echo($prev_month); ?></button>
      <label class="calendar-navigation-bar-label"><?php echo(date('F')); ?></label>
      <button class="calendar-navigation-bar-button calendar-next clean-button" id="calendar-next"><?php echo($next_month); ?></button>
    </div>
  </div>
<?php
  wp_footer();
  ?>

</body>
