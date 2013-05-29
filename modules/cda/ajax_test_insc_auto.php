<?php 

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$csv = new CCSVFile("modules/cda/resources/insc/Echantillon_de_test_INSC.csv", CCSVFile::PROFILE_EXCEL);
$csv->readLine();
$csv->readLine();
$resultat = array("correct" => 0,
                  "incorrect" => 0,
                  "total" => 0);

while ($line = $csv->readLine()) {
  list(
    $firstName,
    $birthDate,
    $nir,
    $nirKey,
    $insc_csv,
    $insc_csv_Key,
    ) = $line;

  $insc = CPatient::calculInsc($nir, $nirKey, $firstName, $birthDate);
  if ($insc === $insc_csv.$insc_csv_Key) {
    $resultat["correct"]++;
  }
  else {
    $resultat["incorrect"]++;
  }
  $resultat["total"]++;
}

$smarty = new CSmartyDP();
$smarty->assign("result", $resultat);
$smarty->display("inc_test_insc_auto.tpl");


/*
$test_a = "\xc3\x80 \xc3\x81 \xc3\x82 \xc3\x83 \xc3\x84 \xc3\x85 \xc3\x86 \xc3\xa0 \xc3\xa1 "; //À Á Â Ã Ä Å Æ à á â ã ä å æ
$test_a .= "\xc3\xa2 \xc3\xa3 \xc3\xa4 \xc3\xa5 \xc3\xa6";
$test_e = "\xc3\x88 \xc3\x89 \xc3\x8a \xc3\x8b \xc3\xa8 \xc3\xa9 \xc3\xaa \xc3\xab"; //È É Ê Ë è é ê ë
$test_d = "\xc3\x90 \xc3\xb0"; //Ð ð
$test_o = "\xc5\x92 \xc3\x92 \xc3\x93 \xc3\x94 \xc3\x95 \xc3\x96 \xc3\x98 \xc5\x93 \xc3\xb2 "; //? Ò Ó Ô Õ Ö Ø ? ò ó ô õ ö ø
$test_o .= "\xc3\xb3 \xc3\xb4 \xc3\xb5 \xc3\xb6 \xc3\xb8";
$test_y = "\xc3\x9d \xc5\xb8 \xc3\xbd \xc3\xbf"; // Ý ? ý ÿ
$test_c = "\xc3\x87 \xc3\xa7"; //Ç ç
$test_i = "\xc3\x8c \xc3\x8c \xc3\x8e \xc3\x8f \xc3\xac \xc3\xad \xc3\xae \xc3\xaf"; //Ì Í Î Ï ì í î ï
$test_n = "\xc3\x91 \xc3\xb1"; //Ñ ñ
$test_u = "\xc3\x99 \xc3\x9a \xc3\x9b \xc3\x9c \xc3\xb9 \xc3\xba \xc3\xbb \xc3\xbc"; //Ù Ú Û Ü ù ú û ü
$test_b = "\xc3\x9f"; //ß
$test_s = "\xc5\xa0 \xc5\xa1"; //? ?
$test_z = "\xc5\xbd \xc5\xbe"; //Z z avec accent inversé
$test  = "(! « # $ % & ? ( ) * + , - . / : ; < = > ? @ [ \\ ] ^ _ ? { | } ~ NBSP ¡ ¢ £ ¤ ¥ ¦ §";
$test .= " ¨ (c) ª « ¬ - (r) ¯ ° ± ² ³ ´ ? ¶ · ¸ ¹ º « 1/4 1/2 3/4 ¿ × Þ ÷ þ";
$test  = utf8_encode($test_default);
$tab_string = array($test_a, $test_e, $test_d, $test_o, $test_y, $test_c, $test_i, $test_n, $test_u, $test_b, $test, $test_s, $test_z);

foreach ($tab_string as $_string) {
  $result_test = formatString($_string);
  echo $_string."<br/>";

  echo $result_test."<br/><br/>";
}*/
