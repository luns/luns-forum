<?php
if (!defined('APPLICATION'))
    exit();
$Session = Gdn::Session();
$DiscussionName = Gdn_Format::Text($this->Discussion->Name);
if ($DiscussionName == '')
    $DiscussionName = T('Blank Discussion Topic');
?>

<? //хлебные крошки (навигационная цепочка) ?>
<ul class="breadcrumbs alt1">
    <li><a href="/forum/discussions">Главная</a></li>
    <li><? echo(Anchor($this->Discussion->Category, 'categories/' . $this->Discussion->CategoryUrlCode, '')); ?></li>
    <li><a href=""><? echo $DiscussionName; ?></a></li>
</ul>

<?
$this->EventArguments['DiscussionName'] = & $DiscussionName;
$this->FireEvent('BeforeDiscussionTitle');

if (!function_exists('WriteComment'))
    include($this->FetchViewLocation('helper_functions', 'discussion'));

$PageClass = '';
if ($this->Pager->FirstPage())
    $PageClass = 'FirstPage';
?>




<?php $this->FireEvent('BeforeDiscussion'); ?>

<table class="table table-condensed table-bordered">
    <?php echo $this->FetchView('comments'); ?>
</table>
<?php
$this->FireEvent('AfterDiscussion');
if ($this->Pager->LastPage()) {
    $LastCommentID = $this->AddDefinition('LastCommentID');
    if (!$LastCommentID || $this->Data['Discussion']->LastCommentID > $LastCommentID)
        $this->AddDefinition('LastCommentID', (int) $this->Data['Discussion']->LastCommentID);
    $this->AddDefinition('Vanilla_Comments_AutoRefresh', Gdn::Config('Vanilla.Comments.AutoRefresh', 0));
}

echo $this->Pager->ToString('more');

// Write out the comment form
if ($this->Discussion->Closed == '1') {
    ?>
    <div class="Foot Closed">
        <div class="Note Closed"><?php echo T('This discussion has been closed.'); ?></div>
        <?php echo Anchor(T('All Discussions'), 'discussions', 'TabLink'); ?>
    </div>
    <?php
} else if ($Session->IsValid() && $Session->CheckPermission('Vanilla.Comments.Add', TRUE, 'Category', $this->Discussion->PermissionCategoryID)) {
    echo $this->FetchView('comment', 'post');
} else if ($Session->IsValid()) {
    ?>
    <div class="Foot Closed">
        <div class="Note Closed"><?php echo T('Commenting not allowed.'); ?></div>
        <?php echo Anchor(T('All Discussions'), 'discussions', 'TabLink'); ?>
    </div>
    <?php
} else {
    ?>
    <div class="button">
        <?php
        echo Anchor(T('Add a Comment'), SignInUrl($this->SelfUrl . (strpos($this->SelfUrl, '?') ? '&' : '?') . 'post#Form_Body'), 'button medium green' . (SignInPopup() ? ' SignInPopup' : ''));
        ?>
    </div>
    <?php
}
