<?  if (!defined('APPLICATION')) exit(); ?>

<?
echo $this->Form->Open();
echo $this->Form->Errors();
?>

<h1> Previous Poll Statistics </h1>

<div class="Info">
    <strong>WARNING:</strong> Creating a new poll will remove the previous poll.<br />
    The data from the previous poll will still be saved in the database. An archival poll viewer will be
        available in the next major release.

<?
$String += <<<EOT
    <ul>
        <li>
        </li>
    </ul>
EOT;
</div>
