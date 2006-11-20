<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m, $graph_tympan_gauche, $graph_tympan_droite;

require_once($AppUI->getLibraryFile("jpgraph/src/mbjpgraph"));
require_once($AppUI->getLibraryFile("jpgraph/src/jpgraph_line"));

class AudiogrammeTympano extends Graph {
  function setTitle($title) {
    $this->title->Set($title);
  }
  
  function AudiogrammeTympano() {
    global $pressions;
    
    // Setup the graph.
    $this->Graph(280, 160, "auto"); 
       
    $this->SetScale("textlin", 0, 15);
    $this->SetMarginColor("lightblue");
    
    // Image setup
    $this->img->SetAntiAliasing();
    $this->img->SetMargin(35, 10, 20, 30);
    
    // Legend setup
    $this->legend->Pos(0.02, 0.5, "right", "center");
    $this->legend->SetShadow("darkgray@0.5", 3);
    $this->legend->SetFont(FF_ARIAL,FS_NORMAL, 7);
    $this->legend->SetFillColor('white@0.3');
  
    // Title setup
    $this->title->SetFont(FF_ARIAL,FS_NORMAL,10);
    $this->title->SetColor("darkred");
    
    // Setup font for axis
    $this->xgrid->Show(true);
    $this->xgrid->SetColor("lightgray", "lightgray:1.8");
    
    $this->xaxis->SetFont(FF_ARIAL, FS_NORMAL, 7);
    $this->xaxis->SetTickLabels($pressions);
    $this->xaxis->title->Set("Pression en mm H²0");
    $this->xaxis->title->SetFont(FF_ARIAL,FS_NORMAL, 7);
    $this->xaxis->title->SetColor("darkred");

    
    // Setup Y-axis labels 
    $this->ygrid->Show(true,true);
    $this->ygrid->SetColor("lightgray", "lightgray:1.8");

    $this->yaxis->SetFont(FF_ARIAL,FS_NORMAL, 7);
    $this->yaxis->scale->ticks->Set(5, 1);
    $this->yaxis->scale->ticks->SupressZeroLabel(false);
    $this->yaxis->scale->ticks->SupressMinorTickMarks(false);
    $this->yaxis->title->Set("Admittance x10 en ml");
    $this->yaxis->title->SetFont(FF_ARIAL,FS_NORMAL, 7);
    $this->yaxis->title->SetColor("darkred");
    
    // Empty plots for scale window
    foreach($pressions as $pression) {
      $datay[] = 20;
    }
    $p1 = new LinePlot($datay);
    $p1->SetCenter();
    
    $this->Add($p1);
    return;
    
  }
  
  function addAudiogramme($values, $mark_color) {
    global $pressions;

    // Empty plot case
    $datay = $values;
    mbRemoveValuesInArray("", $datay);
    if (!count($datay)) {
      return;
    }
    
    $title = $this->title->t;
    $words = explode(" ", $title);
    $cote = $words[1];
    $labels = array();
    $jscalls = array();
      // Remove empty values to connect distant points
      $datax = array();
      $datay = array();
    foreach ($values as $key => $value) {
      if($value !== "" && $value!== null){
      	$pression = $pressions[$key];
        $jstitle = strtr($title, "\n", " ");
        $labels[] = "Modifier l'admittance {$value} ml pour $jstitle à la pression $pression mm H²0";
        $jscalls[] = "javascript:changeTympanValue('$cote',$key)";
        $datay[] = $value;
        $datax[] = "$key"; // Needs to be a string when null
      }
    }

    $p1 = new LinePlot($datay, $datax);

    // Create the first line
    $p1->SetColor($mark_color);
    $p1->SetCenter();
    $p1->SetWeight(1);
    $p1->SetCSIMTargets($jscalls, $labels);

    // Marks
    $p1->mark->SetType(MARK_FILLEDCIRCLE);
    $p1->mark->SetColor($mark_color);
    $p1->mark->SetFillColor("$mark_color@0.6");
    $p1->mark->SetWidth(4);

    $this->Add($p1);
  }
}

global $exam_audio,$reloadGraph;

if(!$reloadGraph || $reloadGraph=="gauche"){
  $graph_tympan_gauche = new AudiogrammeTympano();
  $graph_tympan_gauche->setTitle("Oreille gauche");
  $graph_tympan_gauche->addAudiogramme($exam_audio->_gauche_tympan, "blue");
}

if(!$reloadGraph || $reloadGraph=="droite"){
  $graph_tympan_droite = new AudiogrammeTympano();
  $graph_tympan_droite->setTitle("Oreille droite");
  $graph_tympan_droite->addAudiogramme($exam_audio->_droite_tympan, "red");
}
?>