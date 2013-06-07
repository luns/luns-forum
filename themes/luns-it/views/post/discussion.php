<?php if (!defined('APPLICATION')) exit();
$Session = Gdn::Session();
$CancelUrl = '/vanilla/discussions';
if (C('Vanilla.Categories.Use') && is_object($this->Category))
   $CancelUrl = '/vanilla/discussions/0/'.$this->Category->CategoryID.'/'.Gdn_Format::Url($this->Category->Name);

?>
    <ul class="breadcrumb">
    <li><a href="/forum/discussions">Главная</a> <span class="divider">/</span></li>
    <li class="active">Новая тема</li>
    </ul>
<div class="Box-centr">
    <h4>
   <?php echo $this->Data('Title'); ?>
    </h4>
   <?php
      echo $this->Form->Open();
      echo $this->Form->Errors();
      $this->FireEvent('BeforeFormInputs');



			echo $this->Form->Label('Discussion Title', 'Name');
			echo Wrap($this->Form->TextBox('Name', array('maxlength' => 100, 'class' => 'input post')), 'div', array('class' => 'input post'));


      if ($this->ShowCategorySelector === TRUE) {

				echo '<div class="Category">';
				echo $this->Form->Label('Category', 'CategoryID'), ' ';
				echo $this->Form->CategoryDropDown('CategoryID', array('Value' => GetValue('CategoryID', $this->Category)));
				echo '</div>';

      }

      $this->FireEvent('BeforeBodyInput');

	      echo Wrap($this->Form->TextBox('Body', array( 'MultiLine' => TRUE)), 'div');


      $Options = '';
      // If the user has any of the following permissions (regardless of junction), show the options
      // Note: I need to validate that they have permission in the specified category on the back-end
      // TODO: hide these boxes depending on which category is selected in the dropdown above.
      if ($Session->CheckPermission('Vanilla.Discussions.Announce'))
         $Options .= $this->Form->CheckBox('Announce', T('Announce'), array('value' => '1'));

      if ($Session->CheckPermission('Vanilla.Discussions.Close'))
         $Options .= $this->Form->CheckBox('Closed', T('Close'), array('value' => '1'));

		$this->EventArguments['Options'] = &$Options;
		$this->FireEvent('DiscussionFormOptions');

      if ($Options != '') {

	         echo $Options;

      }


$this->FireEvent('BeforeFormButtons');
      echo '<div class="btn btn-toolbar post">';
      
      echo $this->Form->Button((property_exists($this, 'Discussion')) ? 'Save' : 'Post Discussion', array('class' => 'btn btn-primary'));
      if (!property_exists($this, 'Discussion') || !is_object($this->Discussion) || (property_exists($this, 'Draft') && is_object($this->Draft))) {
         echo $this->Form->Button('Save Draft', array('class' => 'btn'));
      }
      echo $this->Form->Button('Preview', array('class' => 'btn'));
      echo '<div class="btn btn-link">'.Anchor(T('Cancel'), $CancelUrl, 'Cancel').'</div>';
      $this->FireEvent('AfterFormButtons');

      echo '</div>';

      echo '</div>';

      echo $this->Form->Close();
   ?>

</div>
