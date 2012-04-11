<?php /* $Id edit_bvr.php $ */

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$factureconsult_id  = CValue::getOrSession("factureconsult_id");
$edit_bvr           = CValue::get("edit_bvr");
$edit_justificatif  = CValue::get("edit_justificatif");

$group = CGroups::loadCurrent();

$facture = new CFactureConsult();
$facture->load($factureconsult_id);
$facture->loadRefs();
$pm = 0;
$pt = 0;
foreach($facture->_ref_consults as $consult){
  foreach($consult->_ref_actes_tarmed as $acte){
    $pt += $acte->_ref_tarmed->tp_tl * $acte->_ref_tarmed->f_tl * $acte->quantite;
    $pm += $acte->_ref_tarmed->tp_al * $acte->_ref_tarmed->f_al * $acte->quantite;
  }
}
//mbLog($facture->_montant_factures);
$user = CMediusers::get();
$praticien = $facture->loadRefsDerConsultation()->loadRefPraticien();

$group->adresse = str_replace(CHR(13).CHR(10),' ', $group->adresse);
$facture->_ref_patient->adresse = str_replace(CHR(13).CHR(10),' ', $facture->_ref_patient->adresse);

// Création du PDF
$pdf = new CMbPdf('P', 'mm');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

if($edit_bvr){
	
  //Création de la page de la facture
  $pdf->AddPage();
  
  $font = "verab";
  $pdf->setFont($font, '', 12);
  $pdf->WriteHTML("<h4>Facture du patient</h4>");
  
  $colonne1 = 10;
  $colonne2 = 120;
  $font = "vera";
  $pdf->setFont($font, '', 6);
  $pdf->setXY($colonne1, 17);  
  $pdf->Write("", "Cette page est pour vos archives");
  $pdf->setXY($colonne1, $pdf->GetY()+4);
  $pdf->Write("", "Veuillez envoyer le justificatif de remboursement");
  $pdf->setXY($colonne1, $pdf->GetY()+3);
  $pdf->Write("", "annexé à votre caisse maladie ou à l'assurance");
  
  $pdf->setFont($font, '', 8);
  
  //Auteur de la facture
  $auteur = array(
    "50" => "Auteur facture",
    $user->_view,
    "$group->_view",
    substr($group->adresse, 0, 30),
    substr($group->adresse, 30),
    $group->cp." ".$group->ville,
    "80" => "Four. de prestations",
    "Dr. ".$praticien->_view,
    "$group->_view",
    substr($group->adresse, 0, 30),
    substr($group->adresse, 30),
    $group->cp." ".$group->ville,
  );
  
  $nom_dest = "";
  if($facture->_ref_patient->assure_nom){
  	$nom_dest =  $facture->_ref_patient->_assure_civilite." ".$facture->_ref_patient->assure_nom." ".$facture->_ref_patient->assure_prenom;
  }
  else{ $nom_dest = $facture->_ref_patient->_view; }
  
  //Destinataire de la facture
  $patient = array(
    "50" => "Destinataire",
    $nom_dest,
    $facture->_ref_patient->adresse,
    $facture->_ref_patient->cp." ".$facture->_ref_patient->ville,
    "80" => "Patient",
    $facture->_ref_patient->_view,
    $facture->_ref_patient->adresse,
    $facture->_ref_patient->cp." ".$facture->_ref_patient->ville
  );
  
  $tab = array($colonne1 => $auteur, $colonne2 => $patient);
  $x = $y = 0;
  foreach($tab as $k => $v){
  	$colonne = $k;
	  foreach($v as $key => $value){
	  	if($key == "50" || $key == "80"){
	  		$y = $key;
	  		$x=0;
	  	}
	    $pdf->setXY($colonne, $y+$x);
	    $pdf->Cell(30, "", $value);
	    if($key == "50" || $key == "80"){
	      $x+=5;
	    }
	    else{$x +=3;}
	  }
  }
  
  //Données de la facture
  $pdf->Line($colonne1, 122, $colonne1+40, 122);
  $pdf->setXY($colonne1, 120);
  $pdf->Write("", "Données de la facture");
  $pdf->setXY($colonne1, $pdf->GetY()+5);
  $pdf->Write("", "   Date facture:   ".mbTransformTime(null, null, "%d %B %Y"));
  $pdf->setXY($colonne1, $pdf->GetY()+3);
  $pdf->Write("", "      N° facture:   ".$facture->_id);
  $pdf->setXY($colonne1, $pdf->GetY()+3);
  $pdf->Write("", "Traitement du:   ".mbTransformTime(null, $facture->ouverture, "%d %B %Y"));
  $pdf->setXY($colonne1, $pdf->GetY()+3);
  $pdf->Write("", "Traitement du:   ".mbTransformTime(null, $facture->cloture, "%d %B %Y"));
    
  //Tarif
  $acompte = $facture->_montant_avec_remise - $facture->_du_patient_restant;
  $tarif = array(
    "Tarif"         => "CHF",
    "Medical:"      => $pm - $facture->remise,
    "Technique:"    => "$pt",
    "Montant total:" => "$facture->_montant_avec_remise",
    "Acompte:"      => "$acompte",
    "Montant dû:"   => "$facture->_du_patient_restant"
  );
  
  $pdf->Line($colonne2, 122, $colonne2+50, 122);
  $pdf->Line($colonne2, 131, $colonne2+50, 131);
  
  $x = 0;
  foreach($tarif as $key => $value){
  	$pdf->setXY($colonne2, 120+$x);
    $pdf->Cell(25, "", $key, null, null, "R");
    $pdf->Cell(22, "", $value, null, null, "R");
    if($key == "Tarif" || $key == "Technique:"){
    	$x+=5;
    	if($key == "Technique:"){
    		$font = "verab";
        $pdf->setFont($font, '', 8);
    	}
    }
    else{
    	$x+=3;
    }
  }
  
	//le 01 sera fixe car il correspond à un "Codes des genres de justificatifs (BC)" ici :01 = BVR en CHF
	$genre = "01";
	$montant = sprintf('%010d', $facture->_du_patient_restant*100);
	$cle = $facture->getNoControle($genre.$montant);
	
	$reference = $praticien->reference;
	$adherent = $praticien->adherent;
	
  $reference2 = str_replace(' ','', $praticien->reference);
  $reference2 = str_replace('-','', $reference2);
  $adherent2 = str_replace(' ','',$praticien->adherent);
  $adherent2 = str_replace('-','',$adherent2);
  
	$bvr = $genre.$montant.$cle.">".$reference2."+ ".$adherent2.">";
	
	// Dimensions du bvr
	$largeur_bvr = 210;
	$hauteur_bvr = 106;
	$haut_doc = 297-106;
	//Une ligne <=> 4,24 mm
  $h_ligne = 106/25;
  //Une colonne <=> 2,53mm
  $l_colonne = 210/83;
  
  //Police par Défault du BVR
  $font = "vera";
	
	//Application du fond rose
	$pdf->SetFillColor(255, 239, 234);
	$pdf->Rect(0, $haut_doc,210, $haut_doc+19*$h_ligne, 'DF');
	
  $pdf->setFont($font, '', 8);
  $pdf->Text($l_colonne, $h_ligne*0.75+$haut_doc   , "Empfangsschein/Récépissé/Ricevuta ");
  $pdf->Text(26*$l_colonne, $h_ligne*0.75+$haut_doc  , "Einzahlung Giro");
  $pdf->Text(44*$l_colonne, $h_ligne*0.75+$haut_doc , "Versement Virement");
  $pdf->Text(67*$l_colonne, $h_ligne*0.75+$haut_doc , "Versamento Girata");
  
	//Les textes en petit et en orange
  $pdf->setFont($font, '', 6);
  $pdf->SetTextColor(255, 140, 0);
	$pdf->Text(49*$l_colonne, $h_ligne*3+$haut_doc , "Keine Mitteilungen anbringen");
	$pdf->Text(49*$l_colonne, $h_ligne*4+$haut_doc , "Pas de communications");
	$pdf->Text(49*$l_colonne, $h_ligne*5+$haut_doc , "Non aggiungete comunicazioni");
  $pdf->Text($l_colonne, $h_ligne*14+$haut_doc , "Einbezahlt von/ Versé par/ Versato da");
  $pdf->Text(49*$l_colonne, $h_ligne*10.5+$haut_doc , "Einbezahlt von/ Versé par/ Versato da");
	
	//Les traits noirs du BVR:
	$pdf->Line(0, $haut_doc, $largeur_bvr, $haut_doc);
	$pdf->Line(0, $h_ligne+$haut_doc, $largeur_bvr, $h_ligne+$haut_doc);
	$pdf->Line(48*$l_colonne, $h_ligne+$haut_doc, 48*$l_colonne, 19*$h_ligne+$haut_doc);
	$pdf->Line(24*$l_colonne, $haut_doc, 24*$l_colonne, $h_ligne+$haut_doc);
	$pdf->Line(48*$l_colonne, 7*$h_ligne+$haut_doc, $largeur_bvr, 7*$h_ligne+$haut_doc);
	$pdf->Line(70*$l_colonne, $h_ligne+$haut_doc, 70*$l_colonne, 7*$h_ligne+$haut_doc);
	
	//Boucle utilisée pour dupliquer les Partie1 et 2 avec un décalage de colonnes
	for($i = 0; $i<=1; $i++){
		$decalage = $i*24*$l_colonne;
		
	  $pdf->setFont($font, '', 6);
		$pdf->SetTextColor(255, 140, 0);
		$pdf->Text($l_colonne + $decalage, $h_ligne*1.75+$haut_doc , "Einzahlung für/Versement pour/Versamento per");
	  $pdf->Text($l_colonne + $decalage, $h_ligne*10.75+$haut_doc , "Konto/Compte/Conto");
		
	  //Adresse du patient
		$pdf->SetTextColor(0);
    $pdf->setFont($font, '', 8);
		$pdf->Text($l_colonne + $decalage, $h_ligne*3+$haut_doc , $facture->_ref_patient->_view);
    //Si le texte dépasse la largeur de la colonne => retour à la ligne
	  $longeur = strlen($facture->_ref_patient->adresse);
	  for($j=0; $j < $longeur/30; $j++){
	    $report = substr($facture->_ref_patient->adresse, 0+30*$j, 30);
	    $pdf->Text($l_colonne + $decalage, $h_ligne*(4+$j)+$haut_doc , $report);
	  }		
		$pdf->Text($l_colonne + $decalage, $h_ligne*(4+$j)+$haut_doc , $facture->_ref_patient->ville);
		
		//Encadrement des montants
	  $pdf->SetDrawColor(255, 140, 0);
		$pdf->Line($l_colonne + $decalage , 12*$h_ligne+$haut_doc, 16*$l_colonne + $decalage, 12*$h_ligne+$haut_doc);
		$pdf->Line(18*$l_colonne + $decalage, 12*$h_ligne+$haut_doc, 23*$l_colonne + $decalage, 12*$h_ligne+$haut_doc);
		$pdf->Line($l_colonne + $decalage, 13.25*$h_ligne+$haut_doc, 16*$l_colonne + $decalage, 13.25*$h_ligne+$haut_doc);
		$pdf->Line(18*$l_colonne + $decalage, 13.25*$h_ligne+$haut_doc, 23*$l_colonne + $decalage, 13.25*$h_ligne+$haut_doc);
	  $pdf->Text(16.75*$l_colonne + $decalage, $h_ligne*13.25+$haut_doc   , ".");
		$pdf->Line($l_colonne + $decalage, 12*$h_ligne+$haut_doc, $l_colonne + $decalage, 13.25*$h_ligne+$haut_doc);
		$pdf->Line(16*$l_colonne + $decalage, 12*$h_ligne+$haut_doc, 16*$l_colonne + $decalage, 13.25*$h_ligne+$haut_doc);
		$pdf->Line(18*$l_colonne + $decalage, 12*$h_ligne+$haut_doc, 18*$l_colonne + $decalage, 13.25*$h_ligne+$haut_doc);
		$pdf->Line(23*$l_colonne + $decalage, 12*$h_ligne+$haut_doc, 23*$l_colonne + $decalage, 13.25*$h_ligne+$haut_doc);
	
		//Numéro adhérent, CHF, Montant1 et Montant2
	  $pdf->setFont($font, '', 10);
	  $pdf->Text($l_colonne*11 + $decalage, $h_ligne*10.75+$haut_doc , $adherent);
	  $pdf->Text($l_colonne + $decalage, $h_ligne*11.5+$haut_doc , "CHF");
		$pdf->Text($l_colonne*(19-strlen($facture->_du_patient_restant)) + $decalage, $h_ligne*13+$haut_doc , sprintf("%d", $facture->_du_patient_restant));
		
		$cents = ceil(($facture->_du_patient_restant - sprintf("%d", $facture->_du_patient_restant))*100);
		if($cents<10){			
			$cents = "0".$cents;
		}
		$pdf->Text($l_colonne*19 + $decalage, $h_ligne*13+$haut_doc , $cents);
	}
	
  $pdf->Text(28*$l_colonne, $h_ligne*18+$haut_doc , "609");
  
  //Encadrement et écriture de la référence
  $pdf->Line(49*$l_colonne, 7.5*$h_ligne+$haut_doc, 49*$l_colonne, 9.5*$h_ligne+$haut_doc);
  $pdf->Line(49*$l_colonne, 7.5*$h_ligne+$haut_doc, 82*$l_colonne, 7.5*$h_ligne+$haut_doc);
  $pdf->Line(49*$l_colonne, 9.5*$h_ligne+$haut_doc, 82*$l_colonne, 9.5*$h_ligne+$haut_doc);
  $pdf->Line(82*$l_colonne, 7.5*$h_ligne+$haut_doc, 82*$l_colonne, 9.5*$h_ligne+$haut_doc);
  $pdf->setFont($font, '', 11);
  $pdf->Text(50*$l_colonne, $h_ligne*8.75+$haut_doc , $reference);
  
  //Les 2 cercles Circle
  $pdf->Circle((83-6.5)*$l_colonne, 4*$h_ligne+$haut_doc, 3.5*$l_colonne);
  $pdf->Circle(7*$l_colonne, 22.5*$h_ligne+$haut_doc, 3.5*$l_colonne);
  // Son petit texte
  $pdf->setFont($font, '', 6);
  $pdf->Text(13*$l_colonne, $h_ligne*21+$haut_doc , "Die Annahmestelle");
  $pdf->Text(13*$l_colonne, $h_ligne*21.5+$haut_doc , "L'office de dépôt");
  $pdf->Text(13*$l_colonne, $h_ligne*22+$haut_doc , "L'ufficio d'accettazione");
  
  
  //Adresse de l'emeteur de la facture
  $pdf->setFont($font, '', 8);
  $pdf->Text($l_colonne, $h_ligne*15+$haut_doc , $reference);
  $pdf->Text($l_colonne, $h_ligne*16+$haut_doc , $group->_view);
  $pdf->Text(49*$l_colonne, $h_ligne*12+$haut_doc , $group->_view);
  $longeur = strlen($group->adresse);
  //Si le texte dépasse la largeur de la colonne => retour à la ligne
  for($j=0; $j < $longeur/30; $j++){
    $report = substr($group->adresse, 0+30*$j, 30);
    $pdf->Text($l_colonne, $h_ligne*(17+$j)+$haut_doc , $report);
    $pdf->Text(49*$l_colonne, $h_ligne*(13+$j)+$haut_doc , $report);
  }
  $pdf->Text($l_colonne, $h_ligne*(17+$j)+$haut_doc , $group->cp.$group->ville);
  $pdf->Text(49*$l_colonne, $h_ligne*(13+$j)+$haut_doc , $group->cp.$group->ville);
  
	//Fond blanc pour la Partie4
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->Rect(24*$l_colonne, 19*$h_ligne+$haut_doc, 210,25*$h_ligne+$haut_doc,'DF');
	
	//Ecriture du code bvr généré modulo10 récursif
	$font = "ocrbb";
	$pdf->setFont($font, '', 12);
	
  $w = (80- strlen($bvr)) *$l_colonne; 
	$pdf->Text($w, $h_ligne*21+$haut_doc, $bvr);
}

if($edit_justificatif){
	include("justificatif.php");
}

$pdf->Output('Facture'.$facture->_view.'.pdf', "I");

?>