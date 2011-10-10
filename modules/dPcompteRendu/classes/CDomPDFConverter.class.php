<?php

/**
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author
 */

class CDomPDFConverter extends CHtmlToPDFConverter {
  
  var $dompdf = null;
  
  function prepare($format, $orientation) {
    CAppUI::requireModuleFile("dPcompteRendu", "dompdf_config");
    CAppUI::requireLibraryFile("dompdf/dompdf_config.inc");
    
    $this->dompdf = new dompdf();
    $this->dompdf->set_paper($format, $orientation);
    $this->dompdf->set_protocol(isset($_SERVER["HTTPS"]) ? "https://" : "http://");
    $this->dompdf->set_host($_SERVER["SERVER_NAME"]);
  }
  
  function render() {
    $this->dompdf->load_html($this->html);
    $this->dompdf->render();
    $this->result = $this->dompdf->output();
  }
  
}
?>