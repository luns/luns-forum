<?php if (!defined('APPLICATION')) exit();
include($this->FetchViewLocation('helper_functions', 'discussions', 'vanilla'));
?>
<div class="Box-centr">
<div class="TaggedHeading"><?php printf("Отмечено тэгом '%s'", htmlspecialchars($this->Tag)); ?></div>
<?php if ($this->DiscussionData->NumRows() > 0) { ?>
<table class="table table-condensed table-bordered">
   <?php include($this->FetchViewLocation('discussions')); ?>
</table>
<?php
   $PagerOptions = array('RecordCount' => $this->Data('CountDiscussions'), 'CurrentRecords' => $this->Data('Discussions')->NumRows());
   if ($this->Data('_PagerUrl')) {
      $PagerOptions['Url'] = $this->Data('_PagerUrl');
   }
   echo PagerModule::Write($PagerOptions);
} else {
   ?>
   <div class="Empty"><?php printf(T('No items tagged with %s.'), htmlspecialchars($this->Tag)); ?></div>
   /div>
   <?php
}
