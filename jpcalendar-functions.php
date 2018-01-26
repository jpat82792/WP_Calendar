<?php

/*
Plugin Name: jpcalendar
Description: Wordpress Calendar
*/
if(! defined('ABSPATH')){
  exit;
}

class JPCalendar{
  //$plugin_name = "JPCalendar";

  public function uninstall_JPCalendar(){
   /* global $wpdb;
    $events_table_name = $wpdb->prefix .'jpcalendar_events';
    $categories_table_name = $wpdb->prefix . 'jpcalendar_categories';
    $uninstall_events = "DROP TABLE IF EXISTS $events_table_name";
    $uninstall_categories = "DROP TABLE IF EXISTS $categories_table_name";
    dbDelta($uninstall_events);
    dbDelta($uninstall_categories);*/
  }

  public function __construct(){
    register_activation_hook(__FILE__, array('JPCalendar', 'setup_database'));
    register_deactivation_hook(__FILE__, array('JPCalendar', 'uninstall_JPCalendar'));
    //add_filter('page_template', array($this, 'display_calendar'));
    add_action('wp_enqueue_scripts', array($this, 'load_calendar_javascript'), 0);
    add_action('wp_enqueue_scripts', array($this, 'load_calendar_css'), 0);
    add_action('admin_menu', array($this, 'set_admin_interface'), 0);
    add_action('admin_post_nopriv_create_edit_event', array($this, 'create_edit_event'));
    add_action('admin_post_create_edit_event', array($this, 'create_edit_event'));
    add_action('admin_post_nopriv_delete_event', array($this, 'delete_event'));
    add_action('admin_post_delete_event', array($this, 'delete_event'));
    add_action('admin_post_nopriv_delete_category', array($this, 'delete_category'));
    add_action('admin_post_delete_category', array($this, 'delete_category'));
    add_action('admin_enqueue_scripts', array($this, 'load_admin_ui_javascript'));
    add_action('admin_enqueue_scripts', array($this, 'load_admin_ui_css'));
    add_action('admin_post_nopriv_create_edit_category_form', array($this, 'create_edit_category'));
    add_action('admin_post_create_edit_category_form', array($this, 'create_edit_category'));
	  if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {

		  // 4.6 and older
		  add_filter(
			  'page_attributes_dropdown_pages_args',
			  array( $this, 'register_project_templates' )
		  );

	  } else {
		  // Add a filter to the wp 4.7 version attributes metabox
		  add_filter(
			  'theme_page_templates', array( $this, 'add_new_template' )
		  );
	  }

	  // Add a filter to the save post to inject out template into the page cache
	  add_filter(
		  'wp_insert_post_data', 
		  array( $this, 'register_project_templates' ) 
	  );

	  // Add a filter to the template include to determine if the page has our 
	  // template assigned and return it's path
	  add_filter(
		  'template_include', 
		  array( $this, 'view_project_template') 
	  );

	  // Add your templates to this array.
	  $this->templates = array(
		  'templates/page-calendar.php' => 'calendar'
	  );
  }


  public function register_project_templates( $atts ) {

	  // Create the key used for the themes cache
	  $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

	  // Retrieve the cache list. 
	  // If it doesn't exist, or it's empty prepare an array
	  $templates = wp_get_theme()->get_page_templates();
	  if ( empty( $templates ) ) {
		  $templates = array();
	  } 

	  // New cache, therefore remove the old one
	  wp_cache_delete( $cache_key , 'themes');

	  // Now add our template to the list of templates by merging our templates
	  // with the existing templates array from the cache.
	  $templates = array_merge( $templates, $this->templates );

	  // Add the modified cache to allow WordPress to pick it up for listing
	  // available templates
	  wp_cache_add( $cache_key, $templates, 'themes', 1800 );

	  return $atts;

  }
  public function view_project_template( $template ) {
	
	  // Get global post
	  global $post;

	  // Return template if post is empty
	  if ( ! $post ) {
		  return $template;
	  }

	  // Return default template if we don't have a custom one defined
	  if ( !isset( $this->templates[get_post_meta( 
		  $post->ID, '_wp_page_template', true 
	  )] ) ) {
		  return $template;
	  } 

	  $file = plugin_dir_path(__FILE__). get_post_meta( 
		  $post->ID, '_wp_page_template', true
	  );

	  // Just to be safe, we check if the file exist first
	  if ( file_exists( $file ) ) {
		  return $file;
	  } else {
		  echo $file;
	  }

	  // Return template
	  return $template;

  }


	public function add_new_template( $posts_templates ) {
		$posts_templates = array_merge( $posts_templates, $this->templates );
		return $posts_templates;
	}

  public function setup_database(){
    global $wpdb;
    $jpcalendar_version = '0.0.3';
    $charset_collate = $wpdb->get_charset_collate();
    $events_table_name = $wpdb->prefix . 'jpcalendar_events';
    $categories_table_name = $wpdb->prefix . 'jpcalendar_categories';
    
    $create_events_table = "CREATE TABLE $events_table_name 
      (
      event_id mediumint(9) NOT NULL AUTO_INCREMENT,
      event_category_id mediumint(9) DEFAULT NULL,
      event_start_date DATE NOT NULL, 
      event_end_date DATE DEFAULT NULL, 
      event_title TEXT NOT NULL , 
      event_description TEXT, 
      recursion TEXT DEFAULT NULL,
      event_start_time TIME NOT NULL,
      event_end_time TIME DEFAULT NULL,
      event_importance TEXT NOT NULL,
      PRIMARY KEY (event_id),
      FOREIGN KEY (event_category_id)
        REFERENCES $categories_table_name (category_id)
        ON DELETE SET NULL ON UPDATE CASCADE
      ) $charset_collate;";
    $create_categories_table = "CREATE TABLE $categories_table_name
      (
      category_id mediumint(9) NOT NULL AUTO_INCREMENT,
      category_title TEXT NOT NULL,
      category_description TEXT DEFAULT NULL,
      PRIMARY KEY (category_id)
      ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($create_categories_table);
    dbDelta($create_events_table);
    add_option('jpcalendar-version', $jpcalendar_version);
    //TODO add add_option to store database version
  }

  public function get_all_event_categories(){
    global $wpdb;
    $categories_table_name = $wpdb->prefix . 'jpcalendar_categories';
    $categories = $wpdb->get_results("SELECT * FROM $categories_table_name");
    return $categories;
  }

  public function get_all_events(){
    global $wpdb;
    $categories = get_all_event_categories();
    $event_table_name =$wpdb->prefix . 'jpcalendar_events';
    $query = "SELECT group_concat(*) FROM $event_table_name GROUP BY event_category_id;";
    $results = $wpdb->get_results($query);
    return $results;
  }

  public function admin_get_events($limit, $offset){
    global $wpdb;
    $sanitized_limit = (int) $limit;
    $sanitized_offset = (int) $offset * $sanitized_limit;
    $event_table_name = $wpdb->prefix . 'jpcalendar_events';
    $events = "SELECT * FROM $event_table_name ORDER BY event_start_date ASC LIMIT $sanitized_limit OFFSET $sanitized_offset;";
    $results = $wpdb->get_results($events);
    return $results;
  }

  public function admin_get_categories($limit, $offset){
    global $wpdb;
    $sanitized_limit = (int) $limit;
    $sanitized_offset = (int) $offset * $sanitized_limit;
    $categories_table_name = $wpdb->prefix . 'jpcalendar_categories';
    $categories = "SELECT * FROM $categories_table_name ORDER BY category_id ASC LIMIT $sanitized_limit OFFSET $sanitized_offset;";
    $results = $wpdb->get_results($categories);
    return $results;
  }

  public function admin_get_category($id){
    global $wpdb;
    $sanitized_id = (int) $id;
    $category;
    if(is_int($sanitized_id)){
      $category_table_name = $wpdb->prefix.'jpcalendar_categories';
      $category_query = "SELECT * FROM $category_table_name WHERE category_id=$sanitized_id";
      $category = $wpdb->get_results($category_query);
    }
    else{
      $category = NULL;
    }
    return $category;
  }

  public function admin_get_event($id){
    global $wpdb;
    $sanitized_id = (int) $id;
    $event;
    if(is_int($sanitized_id)){
      $event_table_name = $wpdb->prefix.'jpcalendar_events';
      $event_query = "SELECT * FROM $event_table_name WHERE event_id=$sanitized_id";
      $event = $wpdb->get_results($event_query);
    }
    else{
      $event = NULL;
    }
    return $event;
  }

  public function load_admin_ui_javascript(){
    wp_register_script('admin_ui_js', plugins_url('jpcalendar/js/admin-ui-controller.js'), array(), false, true);
    wp_enqueue_script('admin_ui_js');
  }

  public function load_admin_ui_css(){
    wp_register_style('admin_css', plugins_url('jpcalendar/css/admin.css'), array(), false, false);
    wp_enqueue_style('admin_css');
  }

  public function delete_event(){
    global $wpdb;
    $event_id = $_POST['event_id'];
    error_log((string)$event_id);
    $event_table_name = $wpdb->prefix.'jpcalendar_events';
    $result = $wpdb->delete($event_table_name, array('event_id'=>$event_id), array('%d'));
    wp_redirect(admin_url('admin.php?page=calendar&tab=eventlist'));
  }

  public function delete_category(){
    global $wpdb;
    $category_id = $_POST['category_id'];
    $category_table_name = $wpdb->prefix.'jpcalendar_categories';
    $result = $wpdb->delete($category_table_name, array('category_id'=>$category_id), array('%d'));
    wp_redirect(admin_url('admin.php?page=calendar&tab=category_list'));
  }
  
  public function create_edit_event(){
  ///TODO: add variable to store recurrence check.
  //TODO: if true, follow existing flow, if false, create a flow that sets those to null
  //TODO: if event end time is not selected, set to null
    global $wpdb;

    $importance = $_POST['importance'];
    $category_id = $_POST['category'];
    $event_title = $_POST['event_title'];
    $event_id = ((int) $_POST['event_id']) === 0 ? NULL : (int) $_POST['event_id'];
    $event_description = $_POST['event_description'];
    $event_start_time_hour= $_POST['start_time_hour'];
    $event_end_time_hour = $_POST['end_time_hour'];
    $event_start_time_minute = $_POST['start_time_minute'];
    $event_end_time_minute = $_POST['end_time_minute'];
    $start_time =$event_start_time_hour . ':' . $event_start_time_minute;
    $end_time = $event_end_time_hour . ':' . $event_end_time_minute;
    $event_start_date = $_POST['date'];
    $event_recurrence_check = boolval($_POST['recurrence']);
    $event_end_date= $_POST['end_date'];
    $event_recurrence = $_POST['recurrence_type'];
    $placeholder_array = array('%s','%s','%s','%s','%s','%s','%s','%s');
    if($event_recurrence_check){
      $event = array('event_title'=>$event_title, 'event_description'=>$event_description,'event_start_time'=> $start_time, 'event_end_time'=>$end_time, 'recursion'=>$event_recurrence, 'event_category_id'=>$category_id, 'event_start_date'=>$event_start_date, 'event_end_date' => $event_end_date, 'event_importance'=>$importance);
    }
    else{
      $event = array('event_title'=>$event_title, 'event_description'=>$event_description,'event_start_time'=> $start_time, 'event_end_time'=>$end_time, 'recursion'=>null, 'event_category_id'=>$category_id, 'event_start_date'=>$event_start_date, 'event_end_date' => $event_end_date, 'event_importance'=>$importance);
    }
    $events_table_name = $wpdb->prefix . 'jpcalendar_events';
    if(!is_int($event_id)){
      $wpdb->insert($events_table_name, $event, $placeholder_array);
      wp_redirect(admin_url('admin.php?page=calendar&tab=create_event'));
    }
    else{
      $wpdb->update($events_table_name, $event, array('event_id'=>$event_id), $placeholder_array);
      wp_redirect(admin_url('admin.php?page=calendar&tab=create_event'));
    }
  }

  public function create_edit_category(){
    global $wpdb;

    $category_id = ((int) $_POST['category_id']) === 0 ? NULL : (int) $_POST['category_id'];
    $category_title = $_POST['category_title'];
    $category_description = $_POST['category_description'];
    $categories_table_name = $wpdb->prefix . 'jpcalendar_categories';
    if(!is_int($category_id)){  
    $wpdb->insert($categories_table_name, array('category_title'=>$category_title, 'category_description'=>$category_description), array('%s','%s'));
    wp_redirect(admin_url('admin.php?page=calendar&tab=create_category'));
    }
    else{
      $wpdb->update($categories_table_name, array('category_title'=>$category_title, 'category_description'=>$category_description), array('category_id'=> $category_id), array('%s','%s'));
    wp_redirect(admin_url('admin.php?page=calendar&tab=create_category'));
    }
  }

  /*Admin template*/
  public function set_admin_template(){ 
  $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'create_event';
  echo($active_tab);
?>
  <div class="wrap">
    <h1>
      Calendar Plugin
    </h1> 
    <h2 class="nav-wrapper">
      <a href="?page=calendar&tab=create_event" class="nav-tab">Create Event</a>
      <a href="?page=calendar&tab=eventlist" class="nav-tab">Event List</a>
      <a href="?page=calendar&tab=create_category" class="nav-tab">Create Category</a>
      <a href="?page=calendar&tab=category_list&count=30&page_index=0" class="nav-tab">Category List</a>
      <a href="?page=calendar&tab=settings" class="nav-tab">Settings</a>
    </h2>
    <?php
      if($active_tab == 'settings'){
        ?>
        <h1 style="display:block; float:left;">settings</h1>
        <form method="POST" action="options.php"></form>
        <?php
      }
      if($active_tab == 'create_event'){
        $event_id = isset( $_GET[ 'event_id' ] ) ? $_GET[ 'event_id' ] : NULL;
        
        $current_event = $this->admin_get_event($event_id);
        $recurs_daily = $current_event[0]->recursion == 'daily' ? 'selected': '';
        $recurs_weekly = $current_event[0]->recursion == 'weekly' ? 'selected': '';
        $recurs_monthly = $current_event[0]->recursion == 'monthly' ? 'selected': '';
        $recurring_event = $current_event[0]->recursion !== NULL ? 'checked': '';
        $importance_required = $current_event[0]->event_importance == 'required' ? 'selected': '';
        $importance_recommended = $current_event[0]->event_importance == 'recommended' ? 'selected': '';
        $importance_interesting = $current_event[0]->event_importance == 'interesting' ? 'selected': '';
        $event_start_time = explode(':', $current_event[0]->event_start_time);
        $event_end_time = explode(':', $current_event[0]->event_end_time);
        $event_start_hour = (int)$event_start_time[0];
        $event_start_minute = (int)$event_start_time[1];
        $event_end_hour = (int)$event_end_time[0];
        $event_end_minute = (int)$event_end_time[1];
        $hour_options = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23];
        $minute_options = [0,5,10,15,20,25,30,35,40,45,50,55];

        
        ?>
        <h1 style="display:block; float:left;">event</h1>
        <div style="width:100%;">
          <form method="POST" action="<?php echo(admin_url('admin-post.php')); ?>">
            <!-- Value of first input must match add_action hook admin_post_your_function_here -->
            <div>
            <input type="hidden" name="action" value="create_edit_event"/>
            <input type="hidden" name="event_id" value="<?php echo($event_id); ?>"
            <label>Categories</label>
            <select name="category">
              <?php
                  $categories = $this->get_all_event_categories();
                  foreach($categories as $category){
                  echo('<option value="'.esc_attr($category->category_id).'">'.esc_html($category->category_title).'</option>');
                  }
              ?>
            </select>
            </div>
            <div>
              <select name="importance">
                <option value="required" <?php echo(esc_attr($importance_required)); ?>>required</option>
                <option value="recommended" <?php echo(esc_attr($importance_recommended)); ?>>recommended</option>
                <option value="interesting" <?php echo(esc_attr($importance_interesting)); ?>>interesting</option>
              </select>            
            </div>
            <div>
            <label for="name">Event Title:</label>
            <input type="text" name="event_title" id="name" value="<?php echo(esc_attr($current_event[0]->event_title)); ?>">
            </div>
            <div>
            <label for="email">Event Description:</label>
            <textarea type="text" name="event_description" id="event_description"><?php echo(esc_html($current_event[0]->event_description));?></textarea>
            </div>
            <div>
              <select id="jpcalendar-start-time-picker-hour" name="start_time_hour">
                <option value="">Select hour</option>
                <?php
                foreach($hour_options as $hour_option){
                  $status = '';
                  if($hour_option == $event_start_hour){
                    $status = 'selected';
                  }
                  echo('<option value="'.esc_attr($hour_option).'" '.esc_attr($status).'>'.esc_html($hour_option).'</option>');
                }
                ?>
              </select>
              <select id="jpcalendar-start-time-picker-minute" name="start_time_minute">
                <option value="">Select minute</option>
  <?php
                foreach($minute_options as $minute_option){
                  $status = '';
                  if($minute_option == $event_start_minute){
                    $status = 'selected';
                  }
                  echo('<option value="'.esc_attr($minute_option).'" '.esc_attr($status).'>'.esc_html($minute_option).'</option>');
                }
  ?>
              </select>
            </div>
            <div>
              <select id="jpcalendar-end-time-picker-hour" name="end_time_hour">
                <option value="">Select hour</option>
                <?php
                foreach($hour_options as $hour_option){
                  $status = '';
                  if($hour_option == $event_end_hour){
                    $status = 'selected';
                  }
                  echo('<option value="'.esc_attr($hour_option).'" '.esc_attr($status).'>'.esc_html($hour_option).'</option>');
                }
                ?>
              </select>
              <select id="jpcalendar-end-time-picker-minute" name="end_time_minute">
                <option value="">Select minute</option>
  <?php
                foreach($minute_options as $minute_option){
                  $status = '';
                  if($minute_option == $event_end_minute){
                    $status = 'selected';
                  }
                  echo('<option value="'.esc_attr($minute_option).'" '.esc_attr($status).'>'.esc_html($minute_option).'</option>');
                }
  ?>
              </select>
            </div>
            <div>
              <input type="date" name="date" value="<?php echo(esc_attr($current_event[0]->event_start_date)); ?>"/>
            </div>
            <div>
              <label>Is this a recurring event?</label>
              <input type="checkbox" name="recurrence" id="recurrence_check" <?php echo(esc_attr($recurring_event)); ?>/>
              <select class="inactive-recurrence" name="recurrence_type">
                <option value="daily" <?php echo(esc_attr($recurs_daily)); ?>>daily</option>
                <option value="weekly" <?php echo(esc_attr($recurs_weekly)); ?>>weekly</option>
                <option value="monthly" <?php echo(esc_attr($recurs_monthly)); ?>>monthly</option>
              </select>
              <input class="inactive-recurrence" type="date" name="end_date" value="<?php echo(esc_attr($current_event[0]->event_end_date)); ?>"/>
            </div>
            <div>
            <input type="submit" name="submit" value="Submit">
            </div>

          </form>
        </div>
        <?php
      }
      if($active_tab == 'create_category'){
        $category_id = isset( $_GET[ 'category_id' ] ) ? $_GET[ 'category_id' ] : NULL;
        $category = $this->admin_get_category($category_id);
        $category_id = is_int($current_event[0]->category_id) ? $category[0]->category_id : NULL;
        //echo(print_r($category[0], true));
        ?>
        <h1 style="display:block; float:left;">category</h1>
        <form method="POST" action="<?php echo(admin_url('admin-post.php')); ?>">
          <!-- Value of first input must match add_action hook admin_post_your_function_here -->
          <div>
          <input type="hidden" name="action" value="create_edit_category_form"/>
          <input type="hidden" name="category_id" value="<?php echo($category[0]->category_id); ?>">
          <label for="name">Category Title:</label>
          <input type="text" name="category_title" id="name" value="<?php echo($category[0]->category_title); ?>" />
          </div>
          <div>
          <label for="email">Category Description:</label>
          <textarea type="text" name="category_description" id="event_description"><?php echo(esc_html($category[0]->category_description)); ?></textarea>
          </div>
          <div>
          <input type="submit" name="submit" value="Submit">
          </div>
        </form>
        <?php
      }
    if ($active_tab == 'category_list'){
    $event_count = isset( $_GET['countjpcalendar'] ) ? $_GET['countjpcalendar'] : 30;
    $page_number = isset( $_GET['pagejpcalendar'] ) ? $_GET['pagejpcalendar'] : 0;
    $categories = $this->admin_get_categories($event_count, $page_number);
    ?>
      <h1 style="display:block; float=left;">Category List</h1>
      <div style="float:left; width:100%;">
        <?php 
        foreach($categories as $category){      
        ?>
        <a href="?page=calendar&tab=create_category&category_id=<?php echo($category->category_id); ?>">
        <h3 style="float:left; width:10%; display:inline-block;"><?php echo($category->category_title); ?></h3>
        </a>
        <form action="<?php echo(admin_url('admin-post.php')); ?>" method="POST">
          <input type="hidden" name="action" value="delete_category"/>
          <input type="hidden" name="category_id" value="<?php echo($category->category_id); ?>">
          <button type="submit" style="display:block;">Delete events</button>
        </form>
      </div>
    <?php 
      }
    }
    if($active_tab == 'eventlist'){
      $event_count = isset( $_GET['countjpcalendar'] ) ? $_GET['countjpcalendar'] : 30;
      $page_number = isset( $_GET['pagejpcalendar'] ) ? $_GET['pagejpcalendar'] : 0;
      $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'create_event';
      $next_page_number = ((int) $page_number)+1;
      $previous_page_number = ((int) $page_number) - 1;
    ?>
      
      <h1 style="display:block; float:left;">Event List</h1> 
      <div> 
      <?php 
        $events = $this->admin_get_events($event_count, $page_number); 
        foreach($events as $event){
          ?>
        <div style="float:left; width:100%;">
          <a href="?page=calendar&tab=create_event&event_id=<?php echo($event->event_id); ?>">
          <h3 style="float:left; width:10%; display:inline-block;">
            <?php echo($event->event_title); ?>
          </h3>
          <form action="<?php echo(admin_url('admin-post.php')); ?>" method="POST">
            <input type="hidden" name="action" value="delete_event"/>
            <input type="hidden" name="event_id" value="<?php echo($event->event_id); ?>">
            <button type="submit" style="display:block;">Delete events</button>
          </form>
        </div>
      <?php
        }      
      ?>
      </div>
      <a style="float:left; width:100%;" href="?page=calendar&tab=eventlist&countjpcalendar=1&pagejpcalendar=<?php echo($previous_page_number); ?>">Back</a>
      <a style="float:left; width:100%;" href="?page=calendar&tab=eventlist&countjpcalendar=1&pagejpcalendar=<?php echo($next_page_number); ?>">Next</a>
    <?php
    }
    ?>
      
  </div>
<?php
  }

  public function set_admin_interface(){
    add_menu_page('Calendar', 'calendar', 'manage_options','calendar', array($this,'set_admin_template'));
  }
  //Provides access to templates  
  public function display_calendar(){
    $page_template;
    if(is_page('calendar')){
      error_log('haunted by the ghost of you');
      $page_template = dirname(__FILE__) . '/templates/page-calendar.php';
    }
    return $page_template;
  }

  public function load_calendar_javascript(){
    if(is_page('calendar')){
      wp_register_script('event_js', plugins_url('jpcalendar/js/event-controller.js'), array(), false, true);
      wp_enqueue_script('event_js');
      wp_register_script('month_js', plugins_url('jpcalendar/js/month-controller.js'), array(), false, true);
      wp_enqueue_script('month_js');
      wp_register_script('overview_js', plugins_url('jpcalendar/js/overview-controller.js'), array(), false, true);
      wp_enqueue_script('overview_js');
      wp_register_script('filter_widget_js', plugins_url('jpcalendar/js/filter-widget-controller.js'), array(), false, true);
      wp_enqueue_script('filter_widget_js');
      wp_register_script('transition_js', plugins_url('jpcalendar/js/transition-controller.js'), array(), false, true);
      wp_enqueue_script('transition_js');
    }
  }

  public function load_calendar_css(){
    if(is_page('calendar')){
      wp_register_style('month_css', plugins_url('jpcalendar/css/calendar.css'));
      wp_enqueue_style('month_css');
    }
  }
}

return new JPCalendar();
?>
