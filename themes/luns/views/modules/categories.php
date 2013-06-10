<?php if (!defined('APPLICATION')) exit();
$CountDiscussions = 0;
$CategoryID = isset($this->_Sender->CategoryID) ? $this->_Sender->CategoryID : '';

if ($this->Data !== FALSE) {
   foreach ($this->Data->Result() as $Category) {
      $CountDiscussions = $CountDiscussions + $Category->CountDiscussions;
   }
   ?>
<div class="Box">
        <ul class="nav nav-list">
    <li class="nav-header"><?php echo T('Categories'); ?></li>
   <li class="divider"></li>
    <li<?php
      if (!is_numeric($CategoryID))
         echo ' class="active"';
         
      ?>><span><strong><?php echo Anchor(Gdn_Format::Text(T('All Discussions')), '/discussions'); ?></strong> <span class="label label-info"><?php echo number_format($CountDiscussions); ?></span></span></li>
<?php
   $MaxDepth = C('Vanilla.Categories.MaxDisplayDepth');
   $DoHeadings = C('Vanilla.Categories.DoHeadings');
   
   foreach ($this->Data->Result() as $Category) {
      if ($Category->CategoryID < 0 || $MaxDepth > 0 && $Category->Depth > $MaxDepth)
         continue;

      if ($DoHeadings && $Category->Depth == 1)
         $CssClass = 'Heading';
      else
         $CssClass = 'Depth'.$Category->Depth.($CategoryID == $Category->CategoryID ? ' Active' : '');
      
     // echo '<li class="'.$CssClass.'">';
      echo '<li>';

      if ($DoHeadings && $Category->Depth == 1) {
         echo Gdn_Format::Text($Category->Name);
      } else {
         echo Wrap(Anchor(($Category->Depth > 1 ? '<i class="icon-minus"></i> ' : '').Gdn_Format::Text($Category->Name).' <span class="label label-info">'.number_format($Category->CountAllDiscussions).'</span>', '/categories/'.rawurlencode($Category->UrlCode)), '')
            ;
      }
      echo "</li>";
   }
   
?>
   </ul>
</div>
   <?php
}