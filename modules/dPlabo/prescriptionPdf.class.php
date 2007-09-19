<?php

/**
 *  @package Mediboard
 *  @subpackage dPlabo
 *  @author  Alexis Granger
 *  @version $Revision: $
 */


// Classe de gestion des pdf spécifique aux prescriptions
class CPrescriptionPdf extends CMbPdf {
	/**
     * decalage pour l'affichage des codes-barres
	 */
	var $decalage;
		
	public function Footer() {
		$this->viewBarcode(20,260,5,null,15,true);
	}
		

	public function viewPraticien($pratView, $functionView, $groupView){
		return "Medecin: <br />".utf8_encode($pratView).
		                "<br />".utf8_encode($functionView).
		                "<br />".utf8_encode($groupView);
	}
		
		
    public function viewPatient($patientView, $patientAdresse, $patientCP, $patientVille, $patientTel){
		return "Patient: <br />".utf8_encode($patientView).
		                "<br />".utf8_encode($patientAdresse).
		                "<br />".utf8_encode($patientCP).
		                " ".utf8_encode($patientVille).
		                "<br />".utf8_encode($patientTel);
	}
		
		
	public function createTab($col1, $col2){
		$first_column_width = 105;
        $current_y_position = 50;
		$this->writeHTMLCell($first_column_width, 0, 0, $current_y_position, $col1, 0, 0, 0);
        $this->Cell(0); 
        $this->writeHTMLCell(0, 0, $first_column_width, $current_y_position, $col2, 0, 0, 0); 
	}
		
		
		
	/*
	 * viewBarcode
	 * @param int $x: position sur l'axe x
	 * @param int $y: position sur l'axe y
	 * @param int $h: hauteur des codes barres
	 * @param string $codage: type de codage, par default C128B
	 * @param int $decalage: decalage entre les 2 lignes de codes barres
	 * @param bool $traduction: affichage de la traduction des codes barres
	 */
	public function viewBarcode($x,$y,$h,$codage = "C128B", $decalage = 15, $traduction = true){
	  $this->decalage = 0;
	  if ($this->barcode) {
	    $this->Ln();
	    $compteur = 0;
	    while($compteur < 4){
	      if($traduction == true){
	        $this->writeHTMLCell(0, 0, $x + $this->decalage + 3, $y + 4, $this->barcode, 0, 0, 0);
            $this->writeHTMLCell(0, 0, $x + $this->decalage + 3, $y + $decalage + 4, $this->barcode, 0, 0, 0);      
		  }
          $this->writeBarcode($x + $this->decalage, $y, 180, $h, $codage, false, false, 2, $this->barcode);
	      $this->writeBarcode($x + $this->decalage, $y + $decalage, 180, $h, $codage, false, false, 2, $this->barcode);
	      $this->decalage += 50;
	      $compteur++;
	    }
	  }	
	}
}



?>