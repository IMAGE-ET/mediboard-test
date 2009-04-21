<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

<head>

<title>Ingres connectivity test case</title>

<style type="text/css">

body {
  font-family: sans-serif;
}

table {
  width: 100%;
}

th {
  background: #ccc;
  border: 1px solid #000;
}

td {
  border: 1px solid #000;
}

p {
}

</style>

</head>

<body>

<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

// Check the ingres_connect function
if (!function_exists( "ingres_connect" )) {
  echo "Initialement, la fonction ingres_connect() n'est pas trouvée : on load dynamiquement la librairie<br />";
  dl("ingres.so");
}
if (!function_exists( "ingres_connect" )) {
  echo "La fonction ingres_connect n'est toutjours pas trouvée<br />";
}

// Test with results
$firstnames = array("Pierre", "Thomas", "Jean", "Lise", "Marie", "Anne");
$lastnames = array("Dupont", "Lafitte", "Caillet", "Bataille", "Lebihan", "Barthélémy", "Garreau");
$nbrows = 5;

$base = "CPtransit";
$user = "system";
$pass = "";
$table = "patient";

function do_query($query, $message) {
  global $link;
  
  echo "<hr/>";
  echo "<pre>$query</pre>";
  ingres_query($query, $link) or die("Query failed: " . ingres_error($link));
  echo "<p>$message</p>";
}

// Connecting, selecting database
$link = ingres_connect($base, $user, $pass)
    or die("Could not connect user '$user' : " . ingres_error($link));
echo "Connected successfully to '$base'...";

$query ="UPDATE t_dossier SET anndos=NULL WHERE numdos='900001'";
do_query($query, "Non nullable");
ingres_commit($link);
die;


// Select from a table that exists in all Ingres databases
$query = "SELECT * FROM iitables " .
    "\nWHERE table_name = '$table' " .
    "\nAND table_owner = '$user'";
do_query($query, "Table '$table' found...");

// Drop table if exists
if (ingres_num_rows($link)) {
  $query = "DROP TABLE $table";
  do_query($query, "Table '$table' droped!");
}

// Create table
$query = "CREATE TABLE $table ( " .
    "\nid BIGINT NOT NULL ," .
    "\nname VARCHAR (50) NOT NULL ," .
    "\nPRIMARY KEY (id))";
do_query($query, "Table '$table' created!");

// Insert rows
for ($index = 0; $index < $nbrows; $index++) {
  $firstname = $firstnames[rand(0, count($firstnames)-1)];
  $lastname = $lastnames[rand(0, count($lastnames)-1)];
  $name = "$firstname $lastname";
  $id = "7" . str_pad($index, 6, "0", STR_PAD_LEFT);

  $query = "INSERT INTO $table (id ,name) " .
      "\nVALUES ($id , '$name')";
  do_query($query, "Added '$name' into table!");
}

// Delete first row
$index = rand(0, $nbrows-1);
$id = "7" . str_pad($index, 6, "0", STR_PAD_LEFT);

$query = "DELETE FROM $table " .
      "\nWHERE id = '$id'";
do_query($query, "Droped row with id '$id'!");


// Select from a table that exists in all Ingres databases
$query = "SELECT * FROM $table";
do_query($query, "Retrieved content from table '$table'...");

// Echoes table content
echo "\n<table>";
echo "\n\t<caption>Final content of table '$table'</caption>";
echo "\n\t<tr>";

// Table colomns
for ($index = 0; $index < ingres_num_fields($link); $index++) {
  $field = ingres_field_name($index+1, $link);
  echo "\n\t<th>$field</th>";
}
echo "\t\n</tr>";

// Table colomns
while ($object = ingres_fetch_object(INGRES_BOTH, $link)) {
    echo "\n\t<tr>";
    foreach (get_object_vars($object) as $value) {
      echo "\n\t\t<td>$value</td>";
      
    }
    echo "\n\t</tr>";
}
echo "\n</table>";

// Commit transaction
ingres_commit($link);

// Closing connection
ingres_close($link);
?>

</body>

</html>
 