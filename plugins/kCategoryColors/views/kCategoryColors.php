<?  if (!defined('APPLICATION')) exit(); ?>

<?
echo $this->Form->Open();
echo $this->Form->Errors();
?>

<h1> Manage Category Colors </h1>

<h2>Before Color Changes will be visible, please follow the instructions below: </h2>
<code>1) Create the Folder Structure: [VanillaRoot]/themes/[YOURTHEME]/views/discussions
2) Move the file "helper_functions.php" from [kColorCategories]/helper_functions.php to above directory
</code>
<br /><br />

<h2> Enter the Category Colors as HEX CODES WITHOUT THE LEADING '#'. </h2>

<div class="Info">
    <ul>

    <? foreach($this->CategoryInfo as $kc) { ?>
        <li>
        <?
            echo $this->Form->Label($kc->Name, $kc->Name);
            echo $this->Form->TextBox('CategoryColor[]', (isset($kc->CategoryColor) ? array('value' => $kc->CategoryColor) : ""));
            echo $this->Form->Hidden('CategoryID[]', array('value' => $kc->CategoryID));
            
        ?>
        </li>

    <? } ?>
        </li>
    </ul>
        <? echo $this->Form->Close('Save'); ?>
