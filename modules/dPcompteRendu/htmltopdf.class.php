<?php

/**
 * @package Mediboard
 * @subpackage dPcompteRendu
 * @version $Revision: $
 * @author
 */

CAppUI::requireLibraryFile("dompdf/dompdf_config.inc");
CAppUI::requireLibraryFile("dompdf/include/dompdf.cls");

class CHtmlToPDF {

  var $nbpages = null;
  var $dompdf  = null;
  var $pdffile = null;

  function __construct() {
    $this->dompdf = new DOMPDF;
  }

  function generate_pdf($content, $stream, $format, $orientation, $path) {
    $this->dompdf->set_paper($format, $orientation);
    $this->dompdf->set_protocol(isset($_SERVER["HTTPS"]) ? $protocol = "https://" : $protocol = "http://");
    $this->dompdf->set_host($_SERVER["SERVER_NAME"]);
    $this->dompdf->load_html($content);
    $this->dompdf->render();

    if($stream) {
      $this->dompdf->stream("temp.pdf", array("Attachment" => 0));
    }
    else {
      file_put_contents($path, $this->dompdf->output());
      $this->nbpages = $this->dompdf->get_canvas()->get_page_count();
    }
  }
	
	// Expressions régulières provenant de FCKEditor
	// cf http://docs.cksource.com/FCKeditor_2.x/Developers_Guide/Configuration/Configuration_Options/CleanWordKeepsStructure
	function cleanWord($str) {
		$str = preg_replace("/<o:p>\s*<\/o:p>/", '', $str);
		$str = preg_replace("/<o:p>.*?<\/o:p>/", '&nbsp;', $str);
		
		// Remove mso-xxx styles.
    $str = preg_replace('/\s*mso-[^:]+:[^;"]+;?/i', '', $str);

		// Remove margin styles.
	  $str = preg_replace("/\s*MARGIN: 0cm 0cm 0pt\s*;/i", '', $str);
	  $str = preg_replace('/\s*MARGIN: 0cm 0cm 0pt\s*"/i', "\"", $str);

	  $str = preg_replace('/\s*TEXT-INDENT: 0cm\s*;/i', '', $str);
	  $str = preg_replace('/\s*TEXT-INDENT: 0cm\s*"/i', "\"", $str);

		$str = preg_replace('/\s*tab-stops:[^;"]*;?/i', '', $str);
    $str = preg_replace('/\s*tab-stops:[^"]*/i', '', $str);

    // Remove empty styles.
	  $str = preg_replace('/\s*style="\s*"/i', '', $str);
	  $str = preg_replace('/<SPAN\s*[^>]*>\s* \s*<\/SPAN>/i', ' ', $str);
	  $str = preg_replace('/<SPAN\s*[^>]*><\/SPAN>/i', '', $str);

		// Remove Lang attributes
	  $str = preg_replace('/<(\w[^>]*) lang=([^ |>]*)([^>]*)/i', '<$1$3', $str) ;
	  $str = preg_replace('/<SPAN\s*>(.*?)<\/SPAN>/i', '$1', $str);
	  $str = preg_replace('/<FONT\s*>(.*?)<\/FONT>/i', '$1', $str);

		// The original <Hn> tag send from Word is something like this: <Hn style="margin-top:0px;margin-bottom:0px">
    //$str = preg_replace('/<H(\d)([^>]*)>/i', '<h$1>', $str);
		
		// Word likes to insert extra <font> tags, when using MSIE. (Wierd).
	  $str = preg_replace('/<(H\d)><FONT[^>]*>(.*?)<\/FONT><\/\1>/i', '<$1>$2<\/$1>', $str);
	  $str = preg_replace('/<(H\d)><EM>(.*?)<\/EM><\/\1>/i', '<$1>$2<\/$1>', $str);
		
		return $str;
	}
}

?>