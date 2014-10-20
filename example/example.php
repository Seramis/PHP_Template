<?php

require_once("../src/tpl.php");

$oTpl = new Tpl('tpl/example.tpl');
$oTpl->name = 'John <b>"some html"</b>';

echo $oTpl->fetch();
