<?php if (!defined('APPLICATION')) exit();
return;
$DiscussionView = $this->FetchViewLocation('discussion');
?>
<div class="Box">
        <ul class="nav nav-list">
   <li class="nav-header"><?php echo T('Bookmarked Discussions'); ?></li>
    <li class="divider"></li>
      <?php
      foreach ($this->Data->Result() as $Discussion) {
         include($DiscussionView);
      }
      if ($this->Data->NumRows() > 10) {
      ?>
      <li class="ShowAll"><?php echo Anchor(T('↳ Show All'), 'discussions/bookmarked'); ?></li>
      </ul>
    </div>
      <?php } ?>
   