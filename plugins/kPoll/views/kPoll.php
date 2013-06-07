<?  if (!defined('APPLICATION')) exit(); ?>

<?
echo $this->Form->Open();
echo $this->Form->Errors();
?>

<h1> kPoll Settings </h1>

<ul>
    <li>
        <? echo $this->Form->Button('Новое голосование'); ?>
        <? echo $this->Form->Button('Очистить активное голосование'); ?>
    </li>
</ul>
    
<h1 id='kPollHeading'> Current/Archived Poll Results </h1>
<br /><br />
<?
$currAnswer = null;
$currPoll = null;
foreach($this->kArchivePoll as $k) {
    if($k->Title != $currPoll)
        echo ($currPoll != null ? "</div><div class='kPollArchive'>" : "<div class='kPollArchive'>");
    echo ($k->Title == $currPoll ? "" : "<h1>$k->Title</h1>");
    echo ($k->Answer == $currAnswer ? ", $k->Имя" : "<br /><span class='kPollAnswer'>$k->Ответ: </span><br >$k->Name"); 
    $currPoll = $k->Title;
    $currAnswer = $k->Answer;
}
?>
