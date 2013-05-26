<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$data     = CValue::post("data");
$filename = CValue::post("filename", "data");

$data = stripslashes($data);

// @todo Inclure la CSS de MB
$data = "
<html>
  <head>
    <title>$filename</title>
    <style type=\"text/css\">
    
      ".file_get_contents("style/mediboard/htmlarea.css")."
      
      table.tbl th,
      table.tbl td {
        padding: 0.5pt; 
      }
      
      .not-printable {
        display: none; 
      }
    </style>
  </head>
  <body>$data</body>
</html>";

$file = new CFile;
$file->file_name = $filename;

$convert = new CHtmlToPDF();
@$convert->generatePDF($data, 1, "a4", "landscape", $file);

