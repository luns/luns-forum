<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
<!DOCTYPE html>
<html lang="ru">


    <head>
        <?php $this->RenderAsset('Head'); ?>

        <link href="/forum/themes/luns-it/bootstrap/css/bootstrap.css" rel="stylesheet" >
        <link href="/forum/themes/luns-it/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
        <script src="/forum/themes/luns-it/bootstrap/js/bootstrap.js"></script>
        <script src="/forum/themes/luns-it/tinymce/tinymce.min.js"></script>



        <script>
            tinymce.init({
                selector: 'textarea',
                language: 'ru',
                language_url: '/languages/ru.js',
                plugins: "bbcode media advlist autolink link image lists charmap print preview autoresize code media emoticons paste table textcolor visualblocks",
                tools: "inserttable",
                image_advtab: true,
                forced_root_block: '',
                toolbar: "bold italic | link image media | emoticons | preview"
            });
        </script>

        <!-- add the jQWidgets framework -->
        <script type="text/javascript" src="/forum/themes/luns-it/jqwidgets/jqxcore.js"></script>
        <!-- add one or more widgets -->
        <script type="text/javascript" src="/forum/themes/luns-it/jqwidgets/jqxtooltip.js"></script>
        <!-- add one of the jQWidgets styles -->
        <link rel="stylesheet" href="/forum/themes/luns-it/jqwidgets/styles/jqx.base.css" type="text/css" />

        <script type="text/javascript">
            $(document).ready(function() {
                // Create jqxTooltip
                $("#elementID").jqxTooltip({content: 'This is a div element.'});
            });
        </script>

        <script type="text/javascript">
            $(".alert").alert;
        </script>

    </head>

    <?
    $Session = Gdn::Session();
    if ($Session->IsValid()) {

    } else {
        session_start();
        if ($_SESSION['test'] <> 1) {
            echo ('<div class="alert fade in">');
            echo ('<button type="button" class="close" data-dismiss="alert">&times;</button>');
            echo ('<h4 class="alert-heading">Вы не авторизованы. Не все возможности доступны.</h4>');
            echo ('<p>После авторизации на сайте вы сможете просматривать все темы, комментировать их, добавлять в закладки и получите другие возможности.</p>');
            echo ('<p>Для авторизации нажмите на кнопку ниже (также вы можете просто закрыть это окно и просматривать форум далее без входа).</p>');
            echo ('<p>');
            echo (Anchor(T('Sign In'), SignInUrl($this->_Sender->SelfUrl), 'btn btn-primary' . (SignInPopup() ? ' SignInPopup' : '')));
            echo ('</p>');
            echo ('</div>');

            session_start();
            $_SESSION['test'] = 1;
        }
    }
    ?>

    <!-- навигационная панель: начало -->
    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <a class="brand" style="margin: 0 0 0 5px;" href="<?php echo Url('/'); ?>"><span><?php echo Gdn_Theme::Logo(); ?></span></a>
            <ul class="nav">

                <?php
                echo ('<li class="divider-vertical"></li>');
                echo '<li>' . Anchor('<i class="icon-pencil"></i> Новая тема', '/post/discussion') . '</li>';
                ?>

                <li><a href="<?php echo Url('/'); ?>"><i class="icon-home"></i> На главную</a></li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-question-sign"></i> О форуме
                        <b class="caret"></b></a>
                    <ul class="dropdown-menu">

                        <li class="pull-left"><a href="<?php echo Url('/about'); ?>">Описание</a></li>
                        <li class="pull-left"><a href="<?php echo Url('/activity'); ?>">Активность</a></li>
                        <li class="pull-left"><a href="<?php echo Url('/discussion/4/То-До'); ?>">ToDo</a></li>
                    </ul>
                </li>

                <?php
                if ($Session->IsValid()) {
                    $Name = $Session->User->Name;
                    echo ('<li class="divider-vertical"></li>');
                    if ($this->User->Admin == 0) {
                        echo ('<li><a href=' . Url("/dashboard/settings") . '><i class="icon-wrench"></i> Админка</a></li>');
                    }
                    echo ('<li class="dropdown">');
                    echo ('<a href="#" class="dropdown-toggle" data-toggle="dropdown">');
                    echo ('<i class="icon-user"></i> ' . $Name);
                    echo ('<b class="caret"></b></a>');
                    echo ('<ul class="dropdown-menu">');
                    echo ('<li class="pull-left"><a href="' . Url("/messages/inbox") . '"><i class="icon-envelope"></i> Личные сообщения</a></li>');
                    echo ('<li class="pull-left"><a href="' . Url("/profile") . '"><i class="icon-info-sign"></i> Просмотр профиля</a></li>');
                    echo ('<li class="pull-left"><a href="/forum' . SignOutUrl() . '" ><i class="icon-remove-sign"></i> Выйти</a></li>');

                    echo ('</ul>');
                    echo '</li>';
                } else {
                    echo '<li>' . Anchor('<i class="icon-circle-arrow-right"></i>' . T('Sign In'), SignInUrl($this->_Sender->SelfUrl), '' . (SignInPopup() ? ' SignInPopup' : '')) . '</li>';
                }
                ?>
                <li>

                </li>
            </ul>

            <form class="navbar-search pull-right" method="get" action="/forum/search" id="45454">

                <div class="input-prepend">
                    <span class="add-on"><i class="icon-search"></i></span>
                    <input type="text" id="Form_Search" name="Search" class="prependedInput" style="margin: 0 20px 0 0;" placeholder="поиск..."" />
                </div>
            </form>
        </div>
    </div>

    <!-- навигационная панель: конец -->

    <body>

        <div class="container">
            <div class="row-fluid">

                <?php $this->RenderAsset('Content'); ?>

                <?php
                if ($Session->IsValid()) {
                    $this->RenderAsset('Panel');
                }
                ?>

            </div>





            <div class="row-fluid">



            </div>

        </div>
        <div id="Foot">
            <!-- HotLog -->
            <div id="HotLog_counter">
                <script type="text/javascript">
                    hotlog_r = "" + Math.random() + "&s=2268533&im=301&r=" +
                            escape(document.referrer) + "&pg=" + escape(window.location.href);
                    hotlog_r += "&j=" + (navigator.javaEnabled() ? "Y" : "N");
                    hotlog_r += "&wh=" + screen.width + "x" + screen.height + "&px=" +
                            (((navigator.appName.substring(0, 3) === "Mic")) ? screen.colorDepth : screen.pixelDepth);
                    hotlog_r += "&js=1.3";
                    document.write('<a href="http://click.hotlog.ru/?2268533" target="_blank"><img ' +
                            'src="http://hit41.hotlog.ru/cgi-bin/hotlog/count?' +
                            hotlog_r + '" border="0" width="88" height="31" title="HotLog: показано количество посетителей за сегодня, за вчера и всего" alt="HotLog"><\/a>');
                </script>

                <noscript>
                <a href="http://click.hotlog.ru/?2268533" target="_blank"><img
                        src="http://hit41.hotlog.ru/cgi-bin/hotlog/count?s=2268533&im=301" border="0"
                        width="88" height="31" title="HotLog: показано количество посетителей за сегодня, за вчера и всего" alt="HotLog"></a>
                </noscript>
            </div>
            <!-- /HotLog -->
            <?php
            $this->RenderAsset('Foot');
            ?>

            <?php $this->FireEvent('AfterBody'); ?>
        </div>
    </body>
</html>
