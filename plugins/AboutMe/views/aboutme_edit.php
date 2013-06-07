<?php if (!defined('APPLICATION')) exit(); ?>
<?php
// Initialize the Form
echo $this->Form->Open();
echo $this->Form->Errors();

// Define Lists

	$RelationshipData = $this->RelationshipData = array(
		'p' => T('PickOne'),
		's' => T('Single'),
		'm' => T('Married'),
		'i' => T('InRelationship'),
		'd' => T('Divorced'),
		'w' => T('Widowed'),
	);

// Display the form
?>
<h2>Basic Information</h2>
<ul>
	 <li>
      <?php
         echo $this->Form->Label('Real Name', 'Real Name');
         echo $this->Form->TextBox('RealName');
      ?>
   </li>
     
</ul>
	<?php
// Close the form
				echo $this->Form->Close('Save'); 


