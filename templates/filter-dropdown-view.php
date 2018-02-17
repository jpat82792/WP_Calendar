<div class="calendar-container">
  <div class="stick-calendar">
  <div class="calendar-filters-bar" id="base-filter-controller" data-category="all" data-importance="all" data-month="2">
    <!-- contains filter menus as well as event drill in drop down -->
<!--    <button id="calendar-back-button">
      <span class="fa fa-angle-left"></span>
    </button>-->
    <div id="category-filter" class="dropdown-category-filter default-dropdown">
      <button id="close-category-filter" class="clean-button rizzoli-button">
        <span class="fa fa-times"></span>
      </button>
      <div class="category-container">
        <div  class="class-selection-option" data-category-id="all">
          All Classes
        </div>
  <?php 
    $calendar_inst = new JPCalendar();
    $categories = $calendar_inst->get_all_event_categories();
    foreach($categories as $category){
      ?>
        <div class="class-selection-option" data-category-id="<?php echo(esc_attr($category->category_id)); ?>">
          <?php echo(esc_html($category->category_title)); ?>
        </div>
      <?php
    }
  ?>
      </div>
    </div>
    <div id="importance-filter" class="dropdown-importance-filter default-dropdown">
      <button id="close-importance-filter" class="clean-button rizzoli-button"><span class="fa fa-times"></span></button>
      <div class="importance-container">
        <div class="importance-menu-item"  data-importance="all">All</div>
        <div class="required importance-filter-widget-circle"></div>
        <div class="importance-menu-item" data-importance="required">
          Discussion
        </div>
        <div class="recommended importance-filter-widget-circle"></div>
        <div class="importance-menu-item" data-importance="recommended">
          Exams
        </div>
        <div class="interesting importance-filter-widget-circle"></div>
        <div class="importance-menu-item" data-importance="interesting">
          Other
        </div>
      </div>
    </div>

    <div id="event-details-dropdown" class="dropdown-event-details default-dropdown">

      <div class="event-details-container">
        <button id="close-event-details-dropdown" class="clean-button rizzoli-button"><span class="fa fa-times"></span></button>
      <div id="dropdown-event-day" class="day-number-label"></div>
        <h3 id="dropdown-event-month" class="event-month"></h3>
        <h3 id="dropdown-event-year"></h3>
        <h4 id="dropdown-event-category" class="event-category"></h4>
        <h2 id="dropdown-event-title" class="event-title"></h2>
        <article id="dropdown-event-content" class="event-content"></article>
      </div>
    </div>

    <div class="category-filter-status-container" id="category-filter-button">
      <label class="category-filter-status">
        All Classes
      </label>
    </div>
    <div class="event-marker-container status-marker" id="importance-filter-button">
      <div class="event-marker required"></div>
      <div class="event-marker recommended"></div>
      <div class="event-marker interesting"></div>
    </div>
  <div class="current-month-label">
    <?php $current_date = date('F');
      echo($current_date);
    ?>
  </div>
  </div>

