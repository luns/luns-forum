<!DOCTYPE html>
<html lang="ru">
    <head>

        <script src="/forum/themes/flex/design/js/kickstart.js"></script> <!-- KICKSTART -->
        <link rel="stylesheet" href="/forum/themes/flex/design/style.css" media="all" /> <!-- KICKSTART -->
        <link rel="stylesheet" href="/forum/themes/flex/design/css/kickstart.css" media="all" /> <!-- KICKSTART -->
        <link rel="stylesheet" href="/forum/themes/flex/design/custom.css" media="all" /> <!-- KICKSTART -->
        <?php
        $Session = Gdn::Session();
        $this->RenderAsset('Head');
        ?>


    <body>
        <!-- навигационная панель: начало -->
        <div class="navbar">

            <a id="logo" class="hide-phone" href="<?php echo Url('/'); ?>"><span><?php echo Gdn_Theme::Logo(); ?></span></a>
            <ul>
                <li><a href="<?php echo Url('/'); ?>"><i class="icon-home"></i> На главную</a></li>
                <?php
                echo '<li class="first">' . Anchor('<i class="icon-pencil"></i> Новая тема', '/post/discussion') . '</li>';
                ?>


                <?php
                if ($Session->IsValid()) {
                    $Name = $Session->User->Name;
                    if ($this->User->Admin == 0) {
                        echo('<li><a href=' . Url("/dashboard/settings") . '><i class="icon-wrench"></i> Админка</a></li>');
                    }

                    echo('<li class="pull-left"><a href="' . Url("/messages/inbox") . '"><i class="icon-envelope"></i> Личные сообщения</a></li>');
                    echo('<li class="pull-left"><a href="' . Url("/profile") . '"><i class="icon-info-sign"></i> Просмотр профиля</a></li>');
                    echo('<li class="pull-left"><a href="/forum' . SignOutUrl() . '" ><i class="icon-remove-sign"></i> Выйти</a></li>');
                } else {
                    echo '<li>' . Anchor('<i class="icon-circle-arrow-right"></i>' . T('Sign In'), SignInUrl($this->_Sender->SelfUrl), '' . (SignInPopup() ? ' SignInPopup' : '')) . '</li>';
                }
                ?>

            </ul>

        </div>

        <!-- навигационная панель: конец -->


        <div class="grid flex">

            <?php $this->RenderAsset('Content'); ?>
            <?php
            if ($Session->IsValid()) {
                $this->RenderAsset('Panel');
            }
            ?>

            <div id="Foot">
                <?php
                $this->RenderAsset('Foot');
                $this->FireEvent('AfterBody');
                ?>
            </div>

        </div>

    </body>
</html>
