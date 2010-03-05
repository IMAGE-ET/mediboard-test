<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

CAppUI::requireLibraryFile("jpgraph/src/mbjpgraph");
CAppUI::requireLibraryFile("jpgraph/src/jpgraph_line");
CAppUI::requireLibraryFile("jpgraph/src/jpgraph_plotband");

class CResultatsLaboGraph extends Graph {
  var $maxValues = 10;
  var $resultats = null;
  
  function CResultatsLaboGraph($patient, $examen, $resultats) {
    $resultats = array_reverse($resultats, true);
    if ($examen->type != "num") {
      JpGraphError::raise("Le type d'examen doit être numérique pour être affiché");
    }
    
    // Prepare values
    // Value plot
    $ydata = array();
    $xlabels = array();
    $min = $examen->min;
    $max = $examen->max;
    foreach ($resultats as $resultat) {
      $min = min($min, $resultat->resultat);
      $max = max($max, $resultat->resultat);
      $ydata[] = $resultat->resultat;
      $xlabels[] = $resultat->date ? mbTransformTime(null, $resultat->date, "%d/%m/%y") : "attendu";
    }
    
//    mbTrace($xlabels);
//    mbTrace($examen->getDBFields(), "exmamen");
//    mbTrace($ydata, "Ydata");
//    mbTrace($min, "Min");
//    mbTrace($max, "Max");
//    CApp::rip();

    // Setup the graph.
    $this->Graph(360, 250, "auto");
    $delta = 2;
    
    $this->SetScale("textlin", max(0, $min-$delta), $max+$delta);

    $this->SetMarginColor("lightblue");
    
    // Image setup
    $this->img->SetAntiAliasing(true);
    $this->img->SetMargin(40, 10, 30, 40);
    
    if ($examen->max) {
      $uband = new PlotBand(HORIZONTAL,BAND_RDIAG,$examen->max,"max","#ffbbbb");
      $uband->ShowFrame(true);
      $uband->SetDensity(92);
      $this->AddBand($uband);
    }

    if ($examen->min) {
      $lband = new PlotBand(HORIZONTAL,BAND_RDIAG,"min",$examen->min,"#ffbbbb");
      $lband->ShowFrame(true);
      $lband->SetDensity(92);
      $this->AddBand($lband); 
    }    
        
    // Legend setup
    $this->legend->Pos(0.02, 0.5, "right", "center");
    $this->legend->SetShadow("darkgray@0.5", 3);
    $this->legend->SetFont(FF_ARIAL,FS_NORMAL, 7);
    $this->legend->SetFillColor("white@0.3");
  
    // Title setup
    $this->title->SetFont(FF_ARIAL,FS_NORMAL,10);
    $this->title->SetColor("darkred");
    $this->title->Set("Résultats pour " . $examen->_view);
    $this->subtitle->SetFont(FF_ARIAL,FS_NORMAL,8);
    $this->subtitle->SetColor("darkgray");
    $this->subtitle->Set($patient->_view);
    
    // Setup X-axis labels
    $this->xgrid->Show(true);
    $this->xgrid->SetColor("lightgray", "lightgray:1.7");
    $this->xaxis->SetFont(FF_ARIAL, FS_NORMAL, 7);
    $this->xaxis->SetLabelAlign("right","top","right");
    $this->xaxis->SetLabelMargin(2);
    $this->xaxis->SetLabelAngle(45);    
    $this->xaxis->SetTickLabels($xlabels);
    
    // Setup Y-axis labels 
    $this->ygrid->Show(true, true);
    $this->ygrid->SetColor("lightgray", "lightgray:1.7");

    $this->yaxis->SetFont(FF_ARIAL,FS_NORMAL, 8);
    
    $this->yaxis->scale->ticks->SupressMinorTickMarks(false);
    $this->yaxis->title->SetFont(FF_ARIAL,FS_NORMAL, 7);
    $this->yaxis->title->SetColor("darkred");
    $this->yaxis->title->Set("valeurs en $examen->unite");
    $this->yaxis->SetLabelMargin(4);     
    $this->yaxis->SetTitleMargin(28);     

    
    
    $plot = new LinePlot($ydata);
    $plot->SetCenter();
    $plot->mark->SetType(MARK_FILLEDCIRCLE);
    $plot->mark->SetFillColor("gray");
    $this->Add($plot);
    
  }
}


?>