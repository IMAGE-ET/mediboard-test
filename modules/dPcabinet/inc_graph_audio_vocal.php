<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $can, $m;

CAppUI::requireModuleFile("dPcabinet", "jpgraph_bezier");
CAppUI::requireLibraryFile("jpgraph/src/mbjpgraph");
CAppUI::requireLibraryFile("jpgraph/src/jpgraph_line");
CAppUI::requireLibraryFile("jpgraph/src/jpgraph_scatter");
CAppUI::requireLibraryFile("jpgraph/src/jpgraph_regstat");

function xPseudoAxisFormatCb ($value) {
	$value -= 50;
  $value *= 100;
  // Prevents rounding errors intrinsic to float values...
  $value = round($value);
  $value *= 10;
  return sprintf("%d", $value);
}

class AudiogrammeVocal extends Graph {
  static public $graph = null;
  
  function AudiogrammeVocal() {
    // Setup the graph.
    $this->Graph(460,305); 
       
    $this->SetScale("intint", 0, 100, 0, 120);
    $this->SetMarginColor("lightblue");
    
    // Image setup
    $this->img->SetAntiAliasing();
    $this->img->SetMargin(40,20,45,20);
    
    // Legend setup
    $this->legend->Pos(0.02, 0.02, "right","top");
    $this->legend->SetShadow("darkgray@0.5", 3);
    $this->legend->SetFillColor('white@0.3');
    $this->legend->SetFont(FF_ARIAL,FS_NORMAL, 7);

  
    // Title setup
    $this->title->Set("Audiométrie vocale");
    $this->title->SetFont(FF_ARIAL,FS_NORMAL,10);
    $this->title->SetColor("darkred");
    
    // Setup font for axis
    $this->xgrid->Show(true, true);
    $this->xgrid->SetColor("lightgray", "lightgray:1.8");
    $this->xaxis->SetFont(FF_ARIAL, FS_NORMAL,8);
    $this->xaxis->SetLabelFormatString("%ddB");
    $this->xaxis->scale->ticks->Set(10, 5);
    $this->xaxis->scale->ticks->SupressZeroLabel(true);
    $this->xaxis->scale->ticks->SupressMinorTickMarks(false);

    // Setup Y-axis labels 
    $this->ygrid->Show(true, true);
    $this->ygrid->SetColor("lightgray", "lightgray:1.8");
    $this->yaxis->SetFont(FF_ARIAL,FS_NORMAL,8);
    $this->yaxis->SetLabelFormatString("%d%%");
    $this->yaxis->scale->ticks->Set(10, 5);
    $this->yaxis->scale->ticks->SupressZeroLabel(true);
    $this->yaxis->scale->ticks->SupressMinorTickMarks(false);
    
    // Perfect audiogramme
    $datax = array(0,  5, 10, 15, 22);
    $datay = array(0, 10, 50, 98, 100);

    $pa = new LinePlot($datay, $datax);
//    $pa->SetWeight(0);
//    $pa->mark->SetType(MARK_SQUARE);
//    $pa->mark->SetWidth(2);
//    $this->Add($pa);

    $bezier = new BezierTD($datax, $datay, 5);
    
    list($datax, $datay) = $bezier->Get(20);

    $pb = new LinePlot($datay, $datax);
    $pb->SetColor("#999999");
    $pb->SetWeight(2);
    $this->Add($pb);
    
    // Secondary x pseudo-axis
    $datax = array();
    $datay = array();
    for ($i = 10; $i < 120; $i += 10) {
      $datax[] = $i;
      $datay[] = 50 + ($i-10)/1000;
    }

    $sp1 = new LinePlot($datay,$datax);
    $sp1->mark->SetType(MARK_CROSS);
    $sp1->value->SetFont(FF_ARIAL,FS_NORMAL,7);
    $sp1->value->SetFormatCallback("xPseudoAxisFormatCb");
    $sp1->value->Show();
    
    $this->Add($sp1);
  }
  
  function addAudiogramme($points, $title, $mark_color, $mark_type) {
    global $frequences;
    
    CMbArray::removeValue(array("", ""), $points);

    // Empty plot case
    if (!count($points)) {
      $datay = array(50);
      $p1 = new LinePlot($datay, $datay);
      $this->Add($p1);
      return;
    }

    $words = explode(" ", $title);
    $cote = $words[1];

    $labels = array();
    $jscalls = array();
    $dBs = array();
    $pcs = array();
    foreach ($points as $key => $point) {
      $dB = @$point[0];
      $pc = @$point[1];
      $dBs[] = $dB;
      $pcs[] = $pc;
      $labels[] = "Modifier le valeur {$pc}%% à {$dB}dB pour l'oreille $cote";
      $jscalls[] = "javascript:changeVocalValue('$cote',$key)";
    }

    $p1 = new LinePlot($pcs, $dBs);

    // Create the first line
    $p1->SetColor($mark_color);
    $p1->SetLegend($title);
    $p1->SetCSIMTargets($jscalls, $labels);
    $p1->SetWeight(1);

    // Marks
    $p1->mark->SetType($mark_type);
    $p1->mark->SetColor($mark_color);
    $p1->mark->SetFillColor("$mark_color@0.6");
    $p1->mark->SetWidth(5);

    // Create the splined line
    if (count($points) > 1) {
//      $spline = new Spline($dBs, $pcs);
//      list($sdBs, $spcs) = $spline->Get(40);
//      $p2 = new LinePlot($spcs, $sdBs);
//      $p2->SetColor("$mark_color:1.8");
//  
//      $this->Add($p2);
      
      $spline = new BezierTD($dBs, $pcs, 5);
      list($bdBs, $bpcs) = $spline->Get(40);

      $p3 = new LinePlot($bpcs, $bdBs);
      $p3->SetColor("$mark_color:1.8");
  
      $this->Add($p3);
    }

    $this->Add($p1);
  }
}

global $exam_audio;

AudiogrammeVocal::$graph = new AudiogrammeVocal();
AudiogrammeVocal::$graph->addAudiogramme($exam_audio->_gauche_vocale, "Oreille gauche", "blue", MARK_STAR);
AudiogrammeVocal::$graph->addAudiogramme($exam_audio->_droite_vocale, "Oreille droite", "red", MARK_SQUARE);

// Ajout de la legende courbe optimale
$_p = new LinePlot("0");
AudiogrammeVocal::$graph->Add($_p);
$_p->SetLegend("Courbe optimale");
$_p->SetColor("#999999");

			
?>