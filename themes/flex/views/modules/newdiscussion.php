<?php

if (!defined('APPLICATION'))
    exit();
echo Anchor(T('Start a New Discussion'), '/post/discussion'.(array_key_exists('CategoryID', $Data) ? '/'.$Data['CategoryID'] : ''), 'btn btn-block btn-primary new');

?>