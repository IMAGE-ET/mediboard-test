<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


global $can;
$can->needsEdit();

set_time_limit(40);


/* Basic connection to AS400 */
$options = array ();

$conn = i5_connect("AS400-ECAP-DTC","MEDIBOARD","MEDIBOARD", $options);
if (!$conn) {
	mbTrace(i5_error(), "Error during connection\n");
	trigger_error("I5 connection fails", E_USER_ERROR);
}
else {
  CAppUI::stepAjax("Connection success");
}

// bool i5_start_testfile("EASYCOM/TRACE", 1, true); 
 
/* Straight request execution */

$news = array(
  // Traitement des mouvements de séjour
  "SELECT * FROM ECAPFILE/TRSJ0 WHERE INDEX > 1234567 AND TRCIDC = '888'",
  "SELECT * FROM ECAPFILE/TRSJ0 WHERE INDEX = 1234567",
  "SELECT * FROM ECAPFILE/ECCIPF WHERE CICIDC = '888'",
  "SELECT * FROM ECAPFILE/ECPAPF WHERE PACIDC = '888' AND PACDIV = '01' AND PACSDV = '01' AND PADMED = '00063473'",
  "SELECT * FROM ECAPFILE/ECPRPF WHERE PRCIDC = '888' AND PRCDIV = '01' AND PRCSDV = '01' AND PRCPRT = '569'",
  "SELECT * FROM ECAPFILE/ECSPPF WHERE SPCSPE = '0'",
  "SELECT * FROM ECAPFILE/ECATPF WHERE ATCIDC = '888' AND ATCDIV = '01' AND ATCSDV = '01' AND ATNDOS = '09000001' AND ATDMED = '00063473'",

  // Traitement complet des mouvements d'interventions
  "SELECT * FROM ECAPFILE/TRINT WHERE INDEX > 1234567 AND TRCIDC = '888'",
  "SELECT * FROM ECAPFILE/TRINT WHERE INDEX = 1234567",
  "SELECT * FROM ECAPFILE/ECCIPF WHERE CICIDC = '888'",
  "SELECT * FROM ECAPFILE/ECPAPF WHERE PACIDC = '888' AND PACDIV = '01' AND PACSDV = '01' AND PADMED = '00073892'",
  "SELECT * FROM ECAPFILE/ECACPF WHERE ACCIDC = '888' AND ACCDIV = '01' AND ACCSDV = '01' AND ACCINT = '090000174'",
  "SELECT * FROM ECAPFILE/ECPRPF WHERE PRCIDC = '888' AND PRCDIV = '01' AND PRCSDV = '01' AND PRCPRT = '1'",
);

$queries = array(
// Traitement des mouvements de séjour
//  "SELECT COUNT(*) AS TOTAL FROM ECAPFILE/TRSJ0 WHERE ETAT NOT IN ('', 'OKOKOKOK') AND (B_CIDC = '888' OR A_CIDC = '888')",
//  "SELECT * FROM ECAPFILE/TRSJ0 WHERE INDEX = '900500439'",
//  "SELECT * FROM ECAPFILE/ECCIPF WHERE CICIDC = '888'",
//  "SELECT * FROM ECAPFILE/ECPAPF WHERE PACIDC = '888' AND PADMED = '00063473'",
//  "SELECT * FROM ECAPFILE/ECPRPF WHERE PRCIDC = '888' AND PRCPRT = '569'",
//  "SELECT * FROM ECAPFILE/ECSPPF WHERE SPCSPE = '0'",
//  "SELECT * FROM ECAPFILE/ECATPF WHERE ATCIDC = '888' AND ATNDOS = '09000001' AND ATDMED = '00063473'",
 

// Traitement complet des mouvements d'interventions
//  "SELECT COUNT(*) AS TOTAL FROM ECAPFILE/TRINT WHERE ETAT = '' AND (B_CIDC = '888' OR A_CIDC = '888')",
//  "SELECT * FROM ECAPFILE/TRINT WHERE INDEX = '900198539'",
//  "SELECT * FROM ECAPFILE/ECCIPF WHERE CICIDC = '888'",
//  "SELECT * FROM ECAPFILE/ECPAPF WHERE PACIDC = '888' AND PADMED = '00073892'",
//  "SELECT * FROM ECAPFILE/ECACPF WHERE ACCIDC = '888' AND ACCDIV = '01' AND ACCSDV = '01'  AND ACCINT = '090000174'",
//  "SELECT * FROM ECAPFILE/ECACPF WHERE ACCIDC = '888' AND ACCINT = '090000174'",

//  "SELECT * FROM ECAPFILE/ECPRPF WHERE PRCIDC = '888' AND PRCPRT = '1'",
//  "SELECT * FROM ECAPFILE/ECSPPF WHERE SPCSPE = '0'",
//  "SELECT * FROM ECAPFILE.ECPRPF WHERE PRCIDC = '888' AND PRCPRT = '845'",
//  "SELECT * FROM ECAPFILE.ECSPPF WHERE SPCSPE = '2'",   
);

for ($i = 0; $i < 10; $i++) {
	foreach($queries as $query) {
		$result = i5_query($query);
		if (!$result) {
			$error = i5_error();
			echo " Error during query\n";
			echo "<BR> Error number: ".$error["num"];
			echo "<BR> Error category: ".$error["cat"];
			echo "<BR> Error message: ".$error ["msg"];
			echo "<BR> Error description: ".$error ["desc"];
		}
		else {
		  mbDump($query, "Query");
		  mbDump(i5_fetch_assoc($result, I5_READ_NEXT ));
		} 
	}
}

/* Disconnect from AS/400 */
$ret = i5_close($conn);
if ($ret) {
  CAppUI::stepAjax("Connection success");
}
else {
	mbTrace(i5_error(), "Error during connection\n");
	trigger_error("I5 connection fails", E_USER_ERROR);
}

?>
