<?php /* $Id: inc_graph_audio_tonal.php 1738 2007-03-19 16:33:47Z maskas $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: 1738 $
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

require_once($AppUI->getLibraryFile("jpgraph/src/mbjpgraph"));
require_once($AppUI->getLibraryFile("jpgraph/src/jpgraph_plotband"));

class CResultatsLaboGraph extends Graph {
  var $maxValues = 10;
  
  var $resultats = null;
  
  function CResultatsLaboGraph($patient, $examen, $resultats) {
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
      $xlabels[] = $resultat->date;
    }
    
//    mbTrace($examen->getProps(), "exmamen");
//    mbTrace($ydata, "Ydata");
//    mbTrace($min, "Min");
//    mbTrace($max, "Max");
//    die();

    // Setup the graph.
    $this->Graph(400, 250, "auto");
    $delta = ($max-$min)/10;
    
    $this->SetScale("textlin", $min-$delta, $max+$delta);

    $this->SetMarginColor("lightblue");
    
    // Image setup
    $this->img->SetAntiAliasing(true);
    $this->img->SetMargin(45, 40, 30, 40);
    
    $uband=new PlotBand(HORIZONTAL,BAND_RDIAG,$examen->max,"max","#ffbbbb");
    $uband->ShowFrame(true);
    $uband->SetDensity(92); // 50% line density
    
    $lband=new PlotBand(HORIZONTAL,BAND_RDIAG,"min",$examen->min,"#ffbbbb");
    $lband->ShowFrame(true);
    $lband->SetDensity(92); // 50% line density
    
    $this->AddBand($uband);
    $this->AddBand($lband); 
    
    
    // Legend setup
    $this->legend->Pos(0.02, 0.5, "right", "center");
    $this->legend->SetShadow("darkgray@0.5", 3);
    $this->legend->SetFont(FF_ARIAL,FS_NORMAL, 7);
    $this->legend->SetFillColor("white@0.3");
  
    // Title setup
    $this->title->SetFont(FF_ARIAL,FS_NORMAL,10);
    $this->title->SetColor("darkred");
    $this->title->Set("Résultats pour " . $examen->_view);
    
    // Setup X-axis labels
    $this->xgrid->Show(true);
    $this->xgrid->SetColor("lightgray", "lightgray:1.7");
    $this->xaxis->SetFont(FF_ARIAL, FS_NORMAL, 8);
    //$this->xaxis->SetLabelMargin(22);
    $this->xaxis->SetLabelAngle(45);    
    
    // Setup Y-axis labels 
    $this->ygrid->Show(true, true);
    $this->ygrid->SetColor("lightgray", "lightgray:1.7");

    $this->yaxis->SetFont(FF_ARIAL,FS_NORMAL, 8);
    
    $this->yaxis->scale->ticks->SupressMinorTickMarks(false);
    $this->yaxis->title->SetFont(FF_ARIAL,FS_NORMAL, 7);
    $this->yaxis->title->SetColor("darkred");
    $this->yaxis->title->Set("valeurs en $examen->unite");
    

    $this->xaxis->SetTickLabels($xlabels);
    
    
    $plot = new LinePlot($ydata);
    $plot->SetCenter();
    $plot->mark->SetType(MARK_FILLEDCIRCLE);
    $plot->mark->SetFillColor("gray");
    $this->Add($plot);
    
  }
}


?>