<?php
$mvtStock = new CHPrimXMLEvenementMvtStock();
$mvtStock->load("tmp/evenementMvtStockSortie.xml");

mbTrace($mvtStock->saveXML());

$mvtStock->schemaValidate();
?>