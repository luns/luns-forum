<?php if (!defined('APPLICATION')) exit();
$Session = Gdn::Session();
$NewOrDraft = !isset($this->Comment) || property_exists($this->Comment, 'DraftID') ? TRUE : FALSE;
$Editing = isset($this->Comment);
?>
<div class="form-horizontal">
   <?php if (!$Editing) { ?>
   <div class="hidden">
      <ul class="nav nav-pills">
         <li class="active"><?php echo Anchor(T('Write Comment'), '#', ''); ?></li>
         <?php
         if (!$Editing)
            echo '<li>'.Anchor(T('Preview'), '#', '')."</li>\n";
         
         if ($NewOrDraft)
            echo '<li>'.Anchor(T('Save Draft'), '#', '')."</li>\n";
   
         $this->FireEvent('AfterCommentTabs');
         ?>
      </ul>
   </div>
   <?php
   } else {
      $this->Form->SetFormValue('Body', $this->Comment->Body);
   }
   echo $this->Form->Open();
   echo $this->Form->Errors();
   
   $CommentOptions = array('MultiLine' => TRUE);
   /*
    Caused non-root users to not be able to add comments. Must take categories
    into account. Look at CheckPermission for more information.
   if (!$Session->CheckPermission('Vanilla.Comment.Add')) {
      $CommentOptions['Disabled'] = 'disabled';
      $CommentOptions['Value'] = T('You do not have permission to write new comments.');
   }
   */
   $this->FireEvent('BeforeBodyField');
   echo Wrap($this->Form->TextBox('Body', $CommentOptions), 'div', array('class' => 'TextBoxWrapper'));
   $this->FireEvent('AfterBodyField');
   $this->FireEvent('BeforeFormButtons');
   echo "<div class=\"btn btn-toolbar\">\n";
   
   $ButtonOptions = array('class' => 'button medium green');
   /*
    Caused non-root users to not be able to add comments. Must take categories
    into account. Look at CheckPermission for more information.
   if (!Gdn::Session()->CheckPermission('Vanilla.Comment.Add'))
      $ButtonOptions['Disabled'] = 'disabled';
   */

   echo $this->Form->Button($Editing ? 'Save Comment' : 'Post Comment', $ButtonOptions);
   
   $CancelText = T('Back to Discussions');
   $CancelClass = 'btn btn-info';
   if (!$NewOrDraft) {
      $CancelText = T('Cancel');
      $CancelClass = 'btn btn-info';
   }

   echo Gdn_Theme::Link('forumroot', $CancelText, NULL, array(
       'class' => $CancelClass
   ));
   
   
   $this->FireEvent('AfterFormButtons');
   echo "</div>\n";
   echo $this->Form->Close();
   ?>
</div>