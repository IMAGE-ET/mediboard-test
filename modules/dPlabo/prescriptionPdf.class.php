<?php

/**
 *  @package Mediboard
 *  @subpackage dPlabo
 *  @author  Alexis Granger
 *  @version $Revision: $
 */


// Classe de gestion des pdf spécifique aux prescriptions
class CPrescriptionPdf extends CMbPdf {

	var $decalage = null;
	var $praticien = null;
	var $patient = null;
	var $sexe = null;
	var $naissance = null;
	var $prelevement = null;
	
	public function setBarcode($bc="", $praticien = "", $patient = "", $sexe = "", $naissance = "", $prelev = "") {
		$this->barcode = $bc;
		$this->praticien = $praticien;
		$this->patient = $patient;
		$this->prelev = $prelev;
		$this->sexe = $sexe;
		$this->naissance = $naissance;
	}
		
		
	public function Footer() {
		$this->viewBarcode(15,240,5,null,30,true);
		$this->SetFontSize(12);
		//mbTrace($this->getX(),"get x");
		//mbTrace($this->getY(),"get y");
		
		$this->setXY(40,286);
		$this->writeHTML("ETIQUETTE A COLLER SUR LE TUBE AVANT LE PRELEVEMENT");
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
	public function viewBarcode($x,$y,$h,$codage = "C128B", $decalage = 30, $traduction = true){
	  
	  $this->decalage = 0;
	  if ($this->barcode) {
	    $this->Ln();
	    $compteur = 0;
	    while($compteur < 4){
	      if($traduction == true){
	      	$this->SetFontSize(7);
	      	$this->writeHTMLCell(0, 0, $x + $this->decalage, $y - 15, utf8_encode("Dr ".$this->praticien), 0, 0, 0);
	        $this->writeHTMLCell(0, 0, $x + $this->decalage, $y - 12, utf8_encode($this->patient), 0, 0, 0);
	        $this->writeHTMLCell(0, 0, $x + $this->decalage, $y - 9, strtoupper($this->sexe)." ".$this->naissance, 0, 0, 0);
	        $this->writeHTMLCell(0, 0, $x + $this->decalage, $y - 6, utf8_encode($this->prelev), 0, 0, 0);
	        
	        $this->writeHTMLCell(0, 0, $x + $this->decalage, $y - 15 + $decalage, utf8_encode("Dr ".$this->praticien), 0, 0, 0);
	        $this->writeHTMLCell(0, 0, $x + $this->decalage, $y - 12 + $decalage, utf8_encode($this->patient), 0, 0, 0);
	        $this->writeHTMLCell(0, 0, $x + $this->decalage, $y - 9  + $decalage, strtoupper($this->sexe)." ".$this->naissance, 0, 0, 0);
	        $this->writeHTMLCell(0, 0, $x + $this->decalage, $y - 6  + $decalage, utf8_encode($this->prelev), 0, 0, 0);
	        
	        $this->SetFontSize(10);
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