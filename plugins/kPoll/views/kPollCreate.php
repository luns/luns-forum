<?  if (!defined('APPLICATION')) exit(); ?>

<?
echo $this->Form->Open();
echo $this->Form->Errors();
?>

<h1> Create New Poll </h1>

<div class="Info">
    <strong>WARNING:</strong> Creating a new poll will replace any active polls.<br />
    <ul>
        <li>
        <?
                echo $this->Form->Label('Poll Title', 'Poll Title');
                echo $this->Form->TextBox('PollTitle');
        ?>
        </li>
        <li>
        <?
                echo $this->Form->Label('Answer #1', 'Answer #1');
                echo $this->Form->TextBox('PollAnswer[]');
        ?>
        </li>
        <li>
        <?
                echo $this->Form->Label('Answer #2', 'Answer #2');
                echo $this->Form->TextBox('PollAnswer[]');
        ?>
        </li>
        <li>
        <?
                echo $this->Form->Label('Answer #3', 'Answer #3');
                echo $this->Form->TextBox('PollAnswer[]');
        ?>
        </li>
        <li>
        <?
                echo $this->Form->Label('Answer #4', 'Answer #4');
                echo $this->Form->TextBox('PollAnswer[]');
        ?>
        </li>
        <li>
        <?
                echo $this->Form->Label('Answer #5', 'Answer #5');
                echo $this->Form->TextBox('PollAnswer[]');
        ?>
        </li>
    </ul>
        <? echo $this->Form->Close('Save'); ?>
