<?php 

$file = 'document.xml';
libxml_use_internal_errors(true);
$myDocument = new DomDocument();
if (!$myDocument->load($file)) 
  die('Error Loading Document');
if (!$myDocument->schemaValidate("evenementPatient/msgEvenementsPatients105.xsd")) {
  $errors = libxml_get_last_error();
  echo('Error Parsing Document');
  echo $errors->message;
} else {
	echo "validate";
}

?>