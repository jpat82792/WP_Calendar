<div class="calendar-filters-bar">
  <!-- contains filter menus as well as event drill in drop down -->

  <div id="category-filter" class="dropdown-category-filter default-dropdown">
    <button id="close-category-filter" class="clean-button">X</button>
    <div class="category-container">

<?php 
  $calendar_inst = new JPCalendar();
  $categories = $calendar_inst->get_all_event_categories();
  foreach($categories as $category){
    ?>
      <div data-category-id="<?php echo(esc_attr($category->category_id)); ?>">
        <?php echo(esc_html($category->category_title)); ?>
      </div>
    <?php
  }
?>
    </div>
  </div>
  <div id="importance-filter" class="dropdown-importance-filter default-dropdown">
    <button id="close-importance-filter" class="clean-button">X</button>
    <div class="importance-container">
      <div data-importance="required">required</div>
      <div data-importance="recommended">recommended</div>
      <div data-importance="interesting">interesting</div>
    </div>
  </div>
  <div id="event-details-dropdown" class="dropdown-event-details default-dropdown">
    <button id="close-event-details-dropdown" class="clean-button">X</button>
    <div class="event-details-container">
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
      All classes
    </label>
  </div>
  <div class="event-marker-container status-marker" id="importance-filter-button">
    <div class="event-marker required"></div>
    <div class="event-marker recommended"></div>
    <div class="event-marker interesting"></div>
  </div>
</div>
