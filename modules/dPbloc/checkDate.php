<?php /* $Id: checkDate.php,v 1.4 2005/04/09 14:31:22 rhum1 Exp $ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision: 1.4 $
* @author Romain Ollivier
*/

//Initialisation du jour choisi
$add = 0;
// @todo : Utiliser la fonction set des variables de sessions
// Year
if(dPgetParam($_GET, "year", "") == "")
{
  if(!isset($_SESSION["year"]))
    $_SESSION["year"] = date("Y");
}
else
  $_SESSION["year"] = dPgetParam($_GET, "year", "");
// Day
if(dPgetParam($_GET, "day", "") == "")
{
  if(!isset($_SESSION["day"]))
    $_SESSION["day"] = date("j");
}
else
{
  $_SESSION["day"] = dPgetParam($_GET, "day", "");
}
// Month
if(dPgetParam($_GET, "month", "") == "")
{
  if(!isset($_SESSION["month"]))
    $_SESSION["month"] = date("n");
}
else
{
  $_SESSION["month"] = dPgetParam($_GET, "month", "");
  if($_SESSION["month"] == 13)
  {
    $_SESSION["month"] = 1;
	$add++;
  }
  if($_SESSION["month"] == 0)
  {
    $_SESSION["month"] = 12;
	$add--;
  }
  $numDaysOfMonth = date("t", mktime(0, 0, 0, $_SESSION["month"], 1 , $_SESSION["year"]));
  if($_SESSION["day"] > $numDaysOfMonth)
  {
    $_SESSION["day"] = $_SESSION["day"] - $numDaysOfMonth;
	$_SESSION["month"]++;
  }
  if($_SESSION["day"] < 1)
  {
    $numDaysOfMonth = date("t", mktime(0, 0, 0, $_SESSION["month"] - 1, 1 , $_SESSION["year"]));
	$_SESSION["day"] = $numDaysOfMonth + $_SESSION["day"];
	$_SESSION["month"]--;
  }
  if($_SESSION["month"] == 13)
  {
    $month = 1;
	$add++;
  }
  if($_SESSION["month"] == 0)
  {
    $month = 12;
	$add--;
  }
}

$_SESSION["year"] += $add;

?>