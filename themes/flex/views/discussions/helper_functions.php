<?php
if (!defined('APPLICATION'))
    exit();

function WriteDiscussion($Discussion, &$Sender, &$Session, $Alt2) {
    static $Alt = FALSE;
    $CssClass = '';
    $CssClass .= $Discussion->Bookmarked == '1' ? '' : '';
    $CssClass .= $Alt ? ' Alt ' : '';
    $Alt = !$Alt;
    $CssClass .= $Discussion->Announce == '1' ? '' : '';
    $CssClass .= $Discussion->Dismissed == '1' ? '' : '';
    $CssClass .= $Discussion->InsertUserID == $Session->UserID ? '' : '';
    $CssClass .= ($Discussion->CountUnreadComments > 0 && $Session->IsValid()) ? '' : '';
    $DiscussionUrl = '/discussion/' . $Discussion->DiscussionID . '/' . Gdn_Format::Url($Discussion->Name) . ($Discussion->CountCommentWatch > 0 && C('Vanilla.Comments.AutoOffset') && $Session->UserID > 0 ? '/#Item_' . $Discussion->CountCommentWatch : '');
    $Sender->EventArguments['DiscussionUrl'] = &$DiscussionUrl;
    $Sender->EventArguments['Discussion'] = &$Discussion;
    $Sender->EventArguments['CssClass'] = &$CssClass;
    $First = UserBuilder($Discussion, 'First');
    $Last = UserBuilder($Discussion, 'Last');

    $Sender->FireEvent('BeforeDiscussionName');

    $DiscussionName = $Discussion->Name;
    if ($DiscussionName == '')
        $DiscussionName = T('Blank Discussion Topic');

    $Sender->EventArguments['DiscussionName'] = &$DiscussionName;

    static $FirstDiscussion = TRUE;
    if (!$FirstDiscussion)
        $Sender->FireEvent('BetweenDiscussion');
    else
        $FirstDiscussion = FALSE;
    ?>

    <?php
    if (!property_exists($Sender, 'CanEditDiscussions'))
        $Sender->CanEditDiscussions = GetValue('PermsDiscussionsEdit', CategoryModel::Categories($Discussion->CategoryID)) && C('Vanilla.AdminCheckboxes.Use');;

    $Sender->FireEvent('BeforeDiscussionContent');
    echo ('<tr class="">');
    echo ('<td>');
    echo Anchor($DiscussionName, $DiscussionUrl, 'Title');
    //WriteOptions($Discussion, $Sender, $Session);
    ?>

    <!-- <?php echo Anchor($DiscussionName, $DiscussionUrl, 'Title'); ?> -->
    <?php $Sender->FireEvent('AfterDiscussionTitle'); ?>
    <div class="Meta">
        <?php $Sender->FireEvent('BeforeDiscussionMeta'); ?>
        <?php if ($Discussion->Announce == '1') { ?>
            <span class="label label-success"><?php echo 'объявление'; ?></span>
        <?php } ?>
        <?php if ($Discussion->Closed == '1') { ?>
            <span class="label label-inverse"><?php echo 'тема закрыта'; ?></span>
        <?php } ?>
        <span class="CommentCount">комментариев: <span class="label"> <?php printf(Plural($Discussion->CountComments, '%s', '%s'), $Discussion->CountComments); ?></span></span>
        <?php
        if ($Session->IsValid() && $Discussion->CountUnreadComments > 0)
            echo ' <span>новых: </span><span class="label label-warning">' . Plural($Discussion->CountUnreadComments, '%s', '%s') . '</span>';

        $Sender->FireEvent('AfterCountMeta');

        if ($Discussion->LastCommentID != '') {

            echo '<span class=""> последнее сообщение: ' . sprintf(T('%1$s'), UserAnchor($Last, 'label')) . '</span>';

            echo '<span class=""> ' . Gdn_Format::Date($Discussion->LastDate) . ' </span>';
        } else {
            echo ' автор: <span class="">' . sprintf(T('%1$s'), UserAnchor($First, 'label')) . ' </span>';
            echo '<span class=""> ' . Gdn_Format::Date($Discussion->FirstDate);

            if ($Source = GetValue('Source', $Discussion)) {
                echo ' ' . sprintf(T('via %s'), T($Source . ' Source', $Source));
            }

            echo '</span>';
        }

        if (C('Vanilla.Categories.Use') && $Discussion->CategoryUrlCode != '')
            echo 'категория: ' . Wrap(Anchor($Discussion->Category, '/categories/' . rawurlencode($Discussion->CategoryUrlCode), 'label'));
        echo('   ');
        WriteOptions($Discussion, $Sender, $Session);
        echo('</div> ');
        echo ('</td');
        echo ('</tr>');
        $Sender->FireEvent('DiscussionMeta');
        $Sender->FireEvent('DiscussionMetaLuns');
        ?>



        <?php
    }

    function WriteFilterTabs(&$Sender) {
        $Session = Gdn::Session();
        $Title = property_exists($Sender, 'Category') ? GetValue('Name', $Sender->Category, '') : '';
        if ($Title == '')
            $Title = T('All Discussions');

        $Bookmarked = T('My Bookmarks');
        $MyDiscussions = T('My Discussions');
        $MyDrafts = T('My Drafts');
        $CountBookmarks = 0;
        $CountDiscussions = 0;
        $CountDrafts = 0;
        if ($Session->IsValid()) {
            $CountBookmarks = $Session->User->CountBookmarks;
            $CountDiscussions = $Session->User->CountDiscussions;
            $CountDrafts = $Session->User->CountDrafts;
        }
        if ($CountBookmarks === NULL) {
            $Bookmarked .= '<span class="Popin" rel="' . Url('/discussions/UserBookmarkCount') . '">-</span>';
        } elseif (is_numeric($CountBookmarks) && $CountBookmarks > 0)
            $Bookmarked .= '<span>: ' . $CountBookmarks . '</span>';

        if (is_numeric($CountDiscussions) && $CountDiscussions > 0)
            $MyDiscussions .= '<span>: ' . $CountDiscussions . '</span>';

        if (is_numeric($CountDrafts) && $CountDrafts > 0)
            $MyDrafts .= '<span>: ' . $CountDrafts . '</span>';
        ?>

        <ul class="tabs left">
            <?php $Sender->FireEvent('BeforeDiscussionTabs'); ?>
            <li<?php echo strtolower($Sender->ControllerName) == 'discussionscontroller' && strtolower($Sender->RequestMethod) == 'index' ? ' class="active"' : ''; ?>><?php echo Anchor(T('All Discussions'), 'discussions', ''); ?></li>
            <?php $Sender->FireEvent('AfterAllDiscussionsTab'); ?>

            <?php
            if (C('Vanilla.Categories.ShowTabs')) {
                $CssClass = '';
                if (strtolower($Sender->ControllerName) == 'categoriescontroller' && strtolower($Sender->RequestMethod) == 'all') {
                    $CssClass = 'Active';
                }

                //echo "<li class=\"$CssClass\">".Anchor(T('Categories'), '/categories/all', 'TabLink').'</li>';
                echo "<li>" . Anchor(T('Categories'), '/categories/all', 'TabLink') . '</li>';
            }
            ?>
            <?php if ($CountBookmarks > 0 || $Sender->RequestMethod == 'bookmarked') { ?>
                <li<?php echo $Sender->RequestMethod == 'bookmarked' ? ' class="active"' : ''; ?>><?php echo Anchor($Bookmarked, '/discussions/bookmarked', ''); ?></li>
                <?php
                $Sender->FireEvent('AfterBookmarksTab');
            }
            if ($CountDiscussions > 0 || $Sender->RequestMethod == 'mine') {
                ?>
                <li<?php echo $Sender->RequestMethod == 'mine' ? ' class="active"' : ''; ?>><?php echo Anchor($MyDiscussions, '/discussions/mine', ''); ?></li>
                <?php
            }
            if ($CountDrafts > 0 || $Sender->ControllerName == 'draftscontroller') {
                ?>
                <li<?php echo $Sender->ControllerName == 'draftscontroller' ? ' class="active"' : ''; ?>><?php echo Anchor($MyDrafts, '/drafts', ''); ?></li>
                <?php
            }
            $Sender->FireEvent('AfterDiscussionTabs');
            ?>
        </ul>
        <?php
        $Breadcrumbs = Gdn::Controller()->Data('Breadcrumbs');
        if ($Breadcrumbs) {
            echo '<div class="SubTab Breadcrumbs">';
            $First = TRUE;
            foreach ($Breadcrumbs as $Breadcrumb) {
                if ($First) {
                    $Class = 'Breadcrumb FirstCrumb';
                    $First = FALSE;
                } else {
                    $Class = 'Breadcrumb';
                    echo '<span class="Crumb"> &raquo; </span>';
                }

                echo '<span class="' . $Class . '">', Anchor(Gdn_Format::Text($Breadcrumb['Name']), $Breadcrumb['Url']), '</span>';
            }
            $Sender->FireEvent('AfterBreadcrumbs');
            echo '</div>';
        }
        if (!property_exists($Sender, 'CanEditDiscussions'))
            $Sender->CanEditDiscussions = $Session->CheckPermission('Vanilla.Discussions.Edit', TRUE, 'Category', 'any') && C('Vanilla.AdminCheckboxes.Use');

        if ($Sender->CanEditDiscussions) {
            ?>
            <span class="AdminCheck">
                <input type="checkbox" name="Toggle" />
            </span>
        <?php } ?>

        <?php
    }

    /**
     * Render options that the user has for this discussion.
     */
    function WriteOptions($Discussion, &$Sender, &$Session) {
        if ($Session->IsValid() && $Sender->ShowOptions) {

            $Sender->Options = '';


            // Dismiss an announcement
            if (C('Vanilla.Discussions.Dismiss', 1) && $Discussion->Announce == '1' && $Discussion->Dismissed != '1')
                $Sender->Options .= '<li>' . Anchor(T('Dismiss'), 'vanilla/discussion/dismissannouncement/' . $Discussion->DiscussionID . '/' . $Session->TransientKey(), 'DismissAnnouncement') . '</li>';

            // Edit discussion
            if ($Discussion->FirstUserID == $Session->UserID || $Session->CheckPermission('Vanilla.Discussions.Edit', TRUE, 'Category', $Discussion->PermissionCategoryID))
                $Sender->Options .= '<li>' . Anchor('<i class="icon-edit"></i> ' . T(''), 'vanilla/post/editdiscussion/' . $Discussion->DiscussionID, 'EditDiscussion') . '</li>';

            // Announce discussion
            if ($Session->CheckPermission('Vanilla.Discussions.Announce', TRUE, 'Category', $Discussion->PermissionCategoryID))
                $Sender->Options .= '<li>' . Anchor(T($Discussion->Announce == '1' ? '' : ''), 'vanilla/discussion/announce/' . $Discussion->DiscussionID . '/' . $Session->TransientKey() . '?Target=' . urlencode($Sender->SelfUrl), 'AnnounceDiscussion') . '</li>';

            // Sink discussion
            if ($Session->CheckPermission('Vanilla.Discussions.Sink', TRUE, 'Category', $Discussion->PermissionCategoryID))
                $Sender->Options .= '<li>' . Anchor('<i class="icon-folder-close-alt"></i> ' . T($Discussion->Sink == '1' ? '' : ''), 'vanilla/discussion/sink/' . $Discussion->DiscussionID . '/' . $Session->TransientKey() . '?Target=' . urlencode($Sender->SelfUrl), 'SinkDiscussion') . '</li>';

            // Close discussion
            if ($Session->CheckPermission('Vanilla.Discussions.Close', TRUE, 'Category', $Discussion->PermissionCategoryID))
                $Sender->Options .= '<li>' . Anchor('<i class="' . T($Discussion->Closed == '1' ? 'icon-lock' : 'icon-unlock') . '"></i> ' . T($Discussion->Closed == '1' ? '' : ''), 'vanilla/discussion/close/' . $Discussion->DiscussionID . '/' . $Session->TransientKey() . '?Target=' . urlencode($Sender->SelfUrl), 'CloseDiscussion') . '</li>';

            // Delete discussion
            if ($Session->CheckPermission('Vanilla.Discussions.Delete', TRUE, 'Category', $Discussion->PermissionCategoryID))
                $Sender->Options .= '<li>' . Anchor('<i class="icon-remove-circle"></i> ' . T(''), 'vanilla/discussion/delete/' . $Discussion->DiscussionID . '/' . $Session->TransientKey() . '?Target=' . urlencode($Sender->SelfUrl), 'DeleteDiscussion') . '</li>';

            // Bookmark link
            $Title = T($Discussion->Bookmarked == '1' ? 'Unbookmark' : 'Bookmark');

            $Sender->Options .= '<li>' . Anchor('<i class="' . ($Discussion->Bookmarked == '1' ? 'icon-star' : 'icon-star-empty') . '"></i> ' . ($Discussion->Bookmarked == '1' ? '' : '') . '', '/vanilla/discussion/bookmark/' . $Discussion->DiscussionID . '/' . $Session->TransientKey() . '?Target=' . urlencode($Sender->SelfUrl), '' . ($Discussion->Bookmarked == '1' ? '' : ''), array('title' => $Title)
                    ) . '</li>';

            // Allow plugins to add options
            $Sender->FireEvent('DiscussionOptions');

            if ($Sender->Options != '') {
                ?>


                <span>
                    <ul class="options">
                        <?php echo $Sender->Options; ?>
                    </ul>
                </span>
                <?php
            }
            // Admin check.
            if ($Sender->CanEditDiscussions) {
                if (!property_exists($Sender, 'CheckedDiscussions')) {
                    $Sender->CheckedDiscussions = (array) $Session->GetAttribute('CheckedDiscussions', array());
                    if (!is_array($Sender->CheckedDiscussions))
                        $Sender->CheckedDiscussions = array();
                }

                $ItemSelected = in_array($Discussion->DiscussionID, $Sender->CheckedDiscussions);
                echo '<span class="AdminCheck"><input type="checkbox" name="DiscussionID[]" value="' . $Discussion->DiscussionID . '"' . ($ItemSelected ? ' checked="checked"' : '') . ' /></span>';
            }
        }
    }

