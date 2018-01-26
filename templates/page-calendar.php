<?php
/*  Template Name: omni-calendar*/
  get_header();
?>

<body>
  <?php
 // require_once(get_template_directory() . '/templates/navbar.php');
  require_once(plugin_dir_path(__FILE__) . 'filter-dropdown-view.php');
  require_once(plugin_dir_path(__FILE__) .'calendar-status-bar.php');
  require_once(plugin_dir_path(__FILE__).'calendar-year-view.php');
  require_once(plugin_dir_path(__FILE__).'calendar-month-view.php');

  construct_year_view_calendar(1,1);
  construct_month_view(1,1);  


?>
  <div id="calendar-navigation-bar" class="calendar-navigation-bar">
    <button class="calendar-navigation-bar-button calendar-prev clean-button" id="calendar-prev">prev week</button>
    <label class="calendar-navigation-bar-label">Today</label>
    <button class="calendar-navigation-bar-button calendar-next clean-button" id="calendar-next">next week</button>
  </div>
<?php
  wp_footer();
  ?>

</body>
