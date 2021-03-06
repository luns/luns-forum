﻿<?php if (!defined('APPLICATION')) exit(); ?>
<h1><?php
   if (is_object($this->User))
      echo T('Edit User');
   else
      echo T('Add User');
?></h1>
<?php
echo $this->Form->Open(array('class' => 'User'));
echo $this->Form->Errors();
?>
<ul>
   <li>
      <?php
         echo $this->Form->Label('Username', 'Name');
         echo $this->Form->TextBox('Name');
      ?>
   </li>
   <li>
      <?php
         echo $this->Form->Label('Password', 'Password');
         echo $this->Form->Input('Password', 'password');
      ?>
      <div class="InputButtons">
         <?php
            echo Anchor(T('Generate Password'), '#', 'GeneratePassword Button SmallButton');
            echo Anchor(T('Reveal Password'), '#', 'RevealPassword Button SmallButton');
         ?>
      </div>
   </li>
   <li>
      <?php
         echo $this->Form->Label('Email', 'Email');
         echo $this->Form->TextBox('Email');
      ?>
   </li>
   <li>
      <?php
         echo $this->Form->CheckBox('ShowEmail', T('Email visible to other users'));
      ?>
   </li>
   <li>
      <strong><?php echo T('Check all roles that apply to this user:'); ?></strong>
      <?php echo $this->Form->CheckBoxList("RoleID", array_flip($this->RoleData), array_flip($this->UserRoleData)); ?>
   </li>
</ul>
<h3><?php echo T('Настоящие имя\фамилия'); ?></h3>
<ul>
	<li>
		<?php
		if( C('Plugins.FirstLastNames.NickName') )
			echo $this->Form->Label('Nickname', 'FirstName');
		else
			echo $this->Form->Label('Имя', 'FirstName');
			
		echo $this->Form->TextBox('FirstName');
		?>
	</li>
	<?php if( !C('Plugins.FirstLastNames.NickName') ) { ?>
	<li>
		<?php
		echo $this->Form->Label('Фамилия', 'LastName');
		echo $this->Form->TextBox('LastName');
		?>
	</li>
	<?php } ?>
</ul>
<?php echo $this->Form->Close('Save');