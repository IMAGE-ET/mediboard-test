<?php /* $Id: inc_graph_audio_tonal.php,v 1.16 2005/12/22 18:37:08 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: 1.16 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPcabinet", "examaudio"));
require_once($AppUI->getLibraryClass('jpgraph/src/jpgraph'));
require_once($AppUI->getLibraryClass('jpgraph/src/jpgraph_line'));

class AudiogrammeTonal extends Graph {
  function setTitle($title) {
    $this->title->Set($title);
  }
  
  function AudiogrammeTonal($with_legend = true) {
    global $frequences;
    
    $delta = $with_legend ? 75 : 0;
    
    // Setup the graph.
    $this->Graph(300 + $delta, 250, "auto"); 
       
    $this->SetScale("textlin", -120, 10);
    $this->SetMarginColor("lightblue");
    
    // Image setup
    $this->img->SetAntiAliasing();
    $this->img->SetMargin(45, 20 + $delta, 30, 15);
    
    // Legend setup
    if ($with_legend) {
      $this->legend->Pos(0.02, 0.5, "right", "center");
      $this->legend->SetShadow("darkgray@0.5", 3);
      $this->legend->SetFont(FF_ARIAL,FS_NORMAL, 7);
      $this->legend->SetFillColor('white@0.3');
    } else {
      $this->legend->Hide();
    }
  
    // Title setup
    $this->title->SetFont(FF_ARIAL,FS_NORMAL,10);
    $this->title->SetColor("darkred");
    
    // Setup font for axis
    $this->xgrid->Show(true, true);
    $this->xgrid->SetColor("lightgray", "lightgray:1.8");
    
    $this->xaxis->SetFont(FF_ARIAL, FS_NORMAL, 8);
    $this->xaxis->scale->ticks->SupressTickMarks();
    $this->xaxis->labelPos = 1;
    $this->xaxis->SetLabelMargin(22);
    $this->xaxis->SetTickLabels($frequences);
    
    // Setup Y-axis labels 
    $this->ygrid->Show(true, true);
    $this->ygrid->SetColor("lightgray", "lightgray:1.8");

    $this->yaxis->SetFont(FF_ARIAL,FS_NORMAL, 8);
    $this->yaxis->SetLabelFormatString("%ddB");
    
    $this->yaxis->scale->ticks->Set(20, 10);
    $this->yaxis->scale->ticks->SupressZeroLabel(false);
    $this->yaxis->scale->ticks->SupressMinorTickMarks(false);

    // Empty plots for scale window
    foreach($frequences as $value) {
      $datay[] = 100;
    }
    $p1 = new LinePlot($datay);
    $p1->SetCenter();
    
    $this->Add($p1);
  }
  
  function addAudiogramme($values, $value_name, $title, $mark_color, $mark_type, $mark_file = null, $line = true) {
    global $frequences, $AppUI;
    $image_file = $AppUI->getModuleImage("dPcabinet", $mark_file); 

    // Empty plot case
    $datay = $values;
    mbRemoveValuesInArray("", $datay);
    if (!count($datay)) {
      return;
    }
    
    $words = explode(" ", $this->title->t);
    $cote = $words[1];
    $labels = array();
    $jscalls = array();
    foreach ($values as $key => $value) {
      $frequence = $frequences[$key];
      $jstitle = strtr($title, "\n", " ");
      $labels[] = "Modifier la valeur {$value}dB pour $jstitle à $frequence";
      $jscalls[] = "javascript:changeTonalValue('$cote','$value_name',$key)";
      
      if (is_numeric($value)) {
        $values[$key] = - intval($value);
      }
    }
    
    // Remove empty values to connect distant points
    $datax = array();
    $datay = array();
    foreach($values as $key => $value) {
      if ($value !== "") {
        $datay[] = $value;
        $datax[] = "$key"; // Needs to be a string when null
      }
    }
    
    $p1 = new LinePlot($datay, $datax);

    // Create the first line
    $p1->SetColor($mark_color);
    $p1->SetCenter();
    $p1->SetLegend($title);
    $p1->SetWeight($line ? 1 : 0);
    $p1->SetCSIMTargets($jscalls, $labels);

    // Marks
    $p1->mark->SetType($mark_type, $image_file, 1.0);
    $p1->mark->SetColor($mark_color);
    $p1->mark->SetFillColor("$mark_color@0.6");
    $p1->mark->SetWidth(4);

    $this->Add($p1);
  }
}

global $exam_audio;

$graph_tonal_gauche = new AudiogrammeTonal(true);
$graph_tonal_gauche->setTitle("Oreille gauche");
$graph_tonal_gauche->addAudiogramme($exam_audio->_gauche_aerien, "aerien", "Conduction\naérienne", "blue", MARK_FILLEDCIRCLE);
$graph_tonal_gauche->addAudiogramme($exam_audio->_gauche_osseux, "osseux", "Conduction\nosseuse", "red", MARK_STAR);
$graph_tonal_gauche->addAudiogramme($exam_audio->_gauche_pasrep, "pasrep", "Pas de\nréponse", "green", MARK_DTRIANGLE, null, false);
$graph_tonal_gauche->addAudiogramme($exam_audio->_gauche_ipslat, "ipslat", "Stapédien\nipsilatéral", "black", MARK_IMG, "si.png", false);
$graph_tonal_gauche->addAudiogramme($exam_audio->_gauche_conlat, "conlat", "Stapédien\ncontrolatéral", "black", MARK_IMG, "sc.png", false);

$graph_tonal_droite = new AudiogrammeTonal(true);
$graph_tonal_droite->setTitle("Oreille droite");
$graph_tonal_droite->addAudiogramme($exam_audio->_droite_aerien, "aerien", "Conduction\naérienne", "blue", MARK_FILLEDCIRCLE);
$graph_tonal_droite->addAudiogramme($exam_audio->_droite_osseux, "osseux", "Conduction\nosseuse", "red", MARK_STAR);
$graph_tonal_droite->addAudiogramme($exam_audio->_droite_pasrep, "pasrep", "Pas de\nréponse", "green", MARK_DTRIANGLE, null, false);
$graph_tonal_droite->addAudiogramme($exam_audio->_droite_ipslat, "ipslat", "Stapédien\nipsilatéral", "black", MARK_IMG, "si.png", false);
$graph_tonal_droite->addAudiogramme($exam_audio->_droite_conlat, "conlat", "Stapédien\ncontrolatéral", "black", MARK_IMG, "sc.png", false);
?>