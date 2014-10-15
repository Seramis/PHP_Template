<?php

require_once("../src/tpl.php");

$oTpl = new Tpl('tpl/example.tpl');
$oTpl->name = 'John';

echo $oTpl->fetch();
