<?php if (!defined('APPLICATION')) exit(); ?>
<h1><?= T('Прикрепить голосование'); ?></h1>
    <div class="poll-field-input"> 
    <?= $this->Form->Open();?>
    <?= $this->Form->Label(T('Вопрос голосования'), 'Pollname');?>
    <div class="cleaner"></div>
    <?= $this->Form->TextBox('title');?>
    <div class="cleaner"></div>
    <br />
    <?= $this->Form->Button(T('Прикрепить'));?>
    <?= $this->Form->Close();?>
    </div>
    <br />
    <?= T('Голосование будет прикреплено к теме:')?> <b><? echo($this->Discussion->Name)?></b>