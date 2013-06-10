<?php
if (!defined('APPLICATION'))
    exit();

/**
 * $Object is either a Comment or the original Discussion.
 */
function WriteComment($Object, $Sender, $Session, $CurrentOffset) {
    $Alt = ($CurrentOffset % 2) != 0;

    $Author = UserBuilder($Object, 'Insert');
    $Type = property_exists($Object, 'CommentID') ? 'Comment' : 'Discussion';
    $Sender->EventArguments['Object'] = $Object;
    $Sender->EventArguments['Type'] = $Type;
    $Sender->EventArguments['Author'] = $Author;
    $CssClass = 'Box';
    $Permalink = GetValue('Url', $Object, FALSE);

    if (!property_exists($Sender, 'CanEditComments'))
        $Sender->CanEditComments = $Session->CheckPermission('Vanilla.Comments.Edit', TRUE, 'Category', 'any') && C('Vanilla.AdminCheckboxes.Use');


    if ($Type == 'Comment') {
        $Sender->EventArguments['Comment'] = $Object;
        $Id = 'Comment_' . $Object->CommentID;
        if ($Permalink === FALSE)
            $Permalink = '/discussion/comment/' . $Object->CommentID . '/#Comment_' . $Object->CommentID;
    } else {
        $Sender->EventArguments['Discussion'] = $Object;
        $CssClass .= ' FirstComment';
        $Id = 'Discussion_' . $Object->DiscussionID;
        if ($Permalink === FALSE)
            $Permalink = '/discussion/' . $Object->DiscussionID . '/' . Gdn_Format::Url($Object->Name) . '/p1';
    }
    $Sender->EventArguments['CssClass'] = &$CssClass;
    $Sender->Options = '';
    $CssClass .= $Object->InsertUserID == $Session->UserID ? ' Mine' : '';

    if ($Alt)
        $CssClass .= ' Alt';
    $Alt = !$Alt;


    $Sender->FireEvent('BeforeCommentDisplay');
    ?>
    <tr><td>
        
            <span class="Author">
                    <?php
                    echo UserPhoto($Author);
                    echo UserAnchor($Author);
                    ?>
                </span>
            <div class="Meta">
                    <?php $Sender->FireEvent('BeforeCommentMeta'); ?>
                
                <span class="">
                    <?php
                    echo Anchor(Gdn_Format::Date($Object->DateInserted), $Permalink, 'Permalink', array('name' => 'Item_' . ($CurrentOffset + 1), 'rel' => 'nofollow'));
                    ?>
                </span>
                <?php
                if ($Source = GetValue('Source', $Object)) {
                    echo sprintf(T('via %s'), T($Source . ' Source', $Source));
                }

                WriteOptionList($Object, $Sender, $Session);
                ?>
             <?php $Sender->FireEvent('AfterCommentMeta'); ?>
            </div>
            <div class="Message">
                <?php
                $Sender->FireEvent('BeforeCommentBody');
                $Object->FormatBody = Gdn_Format::To($Object->Body, $Object->Format);
                $Sender->FireEvent('AfterCommentFormat');
                $Object = $Sender->EventArguments['Object'];
                //для миниатюр
                //$RepStart = '<ul class="thumbnails"><li class="span4"><a href="#" class="thumbnail"><img';
                //$RepEnd = '</a></li></ul></img>';
                $TextMessage = $Object->FormatBody;
                //$TextMessage = str_replace('<img', $RepStart, $TextMessage);
                //$TextMessage = str_replace('</img>', $RepEnd, $TextMessage);
                echo $TextMessage;
                ?>
            </div>
    <?php $Sender->FireEvent('AfterCommentBody'); ?>
        
        </td></tr>
    <?php
    $Sender->FireEvent('AfterComment');
}

function WriteOptionList($Object, $Sender, $Session) {
    $EditContentTimeout = C('Garden.EditContentTimeout', -1);
    $CategoryID = GetValue('CategoryID', $Object);
    if (!$CategoryID && property_exists($Sender, 'Discussion'))
        $CategoryID = GetValue('CategoryID', $Sender->Discussion);
    $PermissionCategoryID = GetValue('PermissionCategoryID', $Object, GetValue('PermissionCategoryID', $Sender->Discussion));

    $CanEdit = $EditContentTimeout == -1 || strtotime($Object->DateInserted) + $EditContentTimeout > time();
    $TimeLeft = '';
    if ($CanEdit && $EditContentTimeout > 0 && !$Session->CheckPermission('Vanilla.Discussions.Edit', TRUE, 'Category', $PermissionCategoryID)) {
        $TimeLeft = strtotime($Object->DateInserted) + $EditContentTimeout - time();
        $TimeLeft = $TimeLeft > 0 ? ' (' . Gdn_Format::Seconds($TimeLeft) . ')' : '';
    }

    $Sender->Options = '';
if ($Session->CheckPermission('Garden.Moderation.Manage')) {
                        $Sender->Options .= '<li>'  . Anchor(' <i class="icon icon-globe"></i> '.$Object->InsertIPAddress,'/user/browse?keywords='.$Object->InsertIPAddress) . '</li>';
                    }
    // Show discussion options if this is the discussion / first comment
    if ($Sender->EventArguments['Type'] == 'Discussion') {
        // Can the user edit the discussion?
        if (($CanEdit && $Session->UserID == $Object->InsertUserID) || $Session->CheckPermission('Vanilla.Discussions.Edit', TRUE, 'Category', $PermissionCategoryID))
            $Sender->Options .= '<li>' . Anchor(' <i class="icon icon-edit"></i> ' . T('Edit'), '/vanilla/post/editdiscussion/' . $Object->DiscussionID, 'EditDiscussion') . $TimeLeft . '</li>';

        // Can the user announce?
        if ($Session->CheckPermission('Vanilla.Discussions.Announce', TRUE, 'Category', $PermissionCategoryID))
            $Sender->Options .= '<li>' . Anchor(T($Sender->Discussion->Announce == '1' ? 'Unannounce' : 'Announce'), 'vanilla/discussion/announce/' . $Object->DiscussionID . '/' . $Session->TransientKey() . '?Target=' . urlencode($Sender->SelfUrl), 'AnnounceDiscussion') . '</li>';

        // Can the user sink?
        if ($Session->CheckPermission('Vanilla.Discussions.Sink', TRUE, 'Category', $PermissionCategoryID))
            $Sender->Options .= '<li>' . Anchor(T($Sender->Discussion->Sink == '1' ? 'Unsink' : 'Sink'), 'vanilla/discussion/sink/' . $Object->DiscussionID . '/' . $Session->TransientKey() . '?Target=' . urlencode($Sender->SelfUrl), 'SinkDiscussion') . '</li>';

        // Can the user close?
        if ($Session->CheckPermission('Vanilla.Discussions.Close', TRUE, 'Category', $PermissionCategoryID))
            $Sender->Options .= '<li>' . Anchor(T($Sender->Discussion->Closed == '1' ? 'Reopen' : 'Close'), 'vanilla/discussion/close/' . $Object->DiscussionID . '/' . $Session->TransientKey() . '?Target=' . urlencode($Sender->SelfUrl), 'CloseDiscussion') . '</li>';

        // Can the user delete?
        if ($Session->CheckPermission('Vanilla.Discussions.Delete', TRUE, 'Category', $PermissionCategoryID))
            $Sender->Options .= '<li>' . Anchor(' <i class="icon icon-remove"></i> ' . T('Delete Discussion'), 'vanilla/discussion/delete/' . $Object->DiscussionID . '/' . $Session->TransientKey(), 'DeleteDiscussion') . '</li>';
    } else {
        // And if this is just another comment in the discussion ...
        // Can the user edit the comment?
        if (($CanEdit && $Session->UserID == $Object->InsertUserID) || $Session->CheckPermission('Vanilla.Comments.Edit', TRUE, 'Category', $PermissionCategoryID))
            $Sender->Options .= '<li>' . Anchor(' <i class="icon icon-edit"></i> ' . T('Edit'), '/vanilla/post/editcomment/' . $Object->CommentID, 'EditComment') . $TimeLeft . '</li>';

        // Can the user delete the comment?
        if ($Session->CheckPermission('Vanilla.Comments.Delete', TRUE, 'Category', $PermissionCategoryID))
            $Sender->Options .= '<li>' . Anchor(' <i class="icon icon-remove"></i> ' . T('Delete'), 'vanilla/discussion/deletecomment/' . $Object->CommentID . '/' . $Session->TransientKey() . '/?Target=' . urlencode("/discussion/{$Object->DiscussionID}/x"), 'DeleteComment') . '</li>';
    }

    // Allow plugins to add options
    $Sender->FireEvent('CommentOptions');

    if ($Session->IsValid()){
    
    echo ('<div class="dropdown pull-right" >');
    echo (' <a class="dropdown-toggle" id="dLabel"  data-toggle="dropdown" data-target="#" href="/page.html">');
    //echo ('  <i class="icon-cog"></i>');
    echo ('<b class="caret"></b>');
    echo ('</a>');
    echo ('<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">');
    echo $Sender->Options;
    echo ('</ul>');
    echo ('</div>');

    }
    // echo $Sender->Options;
    //if ($Sender->CanEditComments) {
    //if ($Sender->EventArguments['Type'] == 'Comment') {
    //     $Id = $Object->CommentID;
    // echo '<div class="Options">';
    //     if (!property_exists($Sender, 'CheckedComments'))
    // $Sender->CheckedComments = $Session->GetAttribute('CheckedComments', array());
    //$ItemSelected = InSubArray($Id, $Sender->CheckedComments);
    //  echo '<span class="AdminCheck"><input type="checkbox" name="'.'Comment'.'ID[]" value="'.$Id.'"'.($ItemSelected?' checked="checked"':'').' /></span>';
    //  echo '</div>';
    // } else {
    //  echo '<div class="Options">';
    //  echo '<div class="AdminCheck"><input type="checkbox" name="Toggle"></div>';
    //  echo '</div>';
    //}
    // }
}
?>
