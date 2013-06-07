<?php if (!defined('APPLICATION')) exit(); ?>
<h1><?php echo T($this->Data['Title']); ?></h1>
<div class="Info">
   <?php echo T($this->Data['PluginDescription']); ?>
</div>
<h3><?php echo T('Settings'); ?></h3>
<?php
   echo $this->Form->Open();
   echo $this->Form->Errors();
?>
<ul>
   <li><?php
      echo $this->Form->Label('Text to Display', 'Plugin.PM.Text');
      echo $this->Form->Textbox('Plugin.PM.Text');
   ?></li>
       <div>Use <span style="font-style:italic">{user}</span> to substitute in a username in the text</div>
       <b>Example:</b> Send {user} a Message => "Send Admin a Message"
</ul>
<br/>
<?php
   echo $this->Form->Close('Save');
?>