<?php

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @author  Poiron Yohann
 *  @version $Revision: $
 */

global $AppUI;

require_once($AppUI->getLibraryFile("jpgraph/src/mbjpgraph"));
require_once($AppUI->getLibraryFile("jpgraph/src/jpgraph_bar"));
require_once($AppUI->getLibraryFile("jpgraph/src/jpgraph_line"));
require_once($AppUI->getLibraryFile("jpgraph/src/jpgraph_regstat"));
require_once($AppUI->getLibraryFile("jpgraph/src/jpgraph_pie"));

class CMbGraph {
	var $graph = null;
	
	var $options = null; //tab of options

	function CMbGraph() {
		global $dPconfig;
		$this->config = $dPconfig['graph_engine'];
		$this->options = array ("width" => 320,
														"height" => 125,
														"size" => 1,
														"title" => "Title",
														"subtitle" => " ",
														"sizeFontTitle" => 7,
														"palette" => array ( "#aa5500", "#55aa00", "#0055aa", "#aa0055", 
																								 "#5500aa", "#00aa55", "#ff0000", "#00ff00", 
																								 "#0000ff", "#ffff00", "#ff00ff", "#00ffff", 
																								 "#FFFFFF", "#C90062", "#E05206", "#F0AB00", 
																								 "#000000", "#FF0000", "#3C8A2E", "#006983", 
																								 "#0098C3", "#21578A", "#55517B", "#4E7D5B" ),
														
														"margin" => array (0, 
																							 0, 
																							 0, 
																							 0),
														"marginColor" => "lightblue",
														"titleColor"	=> "darkred",
														"subtitleColor"	=> "black",
														"sizeFontLegend" => 7,
														"posLegend" => array(0, 
																								 0,
																								 "right",
																								 "top"),
														"sizeFontAxis" => 6,
														"labelAngle" => 0,
														"textTickInterval" => 0,
														"posXAbsDelta" => 0,
														"posYAbsDelta" => 0,
														"dataPie" => array(),
														"dataBar" => array(),
														"dataLine" => array(),
														"dataAccBar" => array(),										 
														"datax" => array(),
														"from" => "#AAAAAA",
														"to" => "#EEEEEE",
														"graphBarColor" => "white",
														"graphBarLegend" => "Legend",
														"graphLineColor" => "white",
														"graphLineLegend" => "Legend",
														"graphPieLegend" => "Legend",
														"graphAccLegend" => "Legend",
														"map" => "non",
													  "mapInfo" => array(),						 										 											 																																										
														"renderPath" => "tmp/graphtmp.png",
														"nameHtmlImageMap" => "nameHtmlImageMap"										 									 								 												 		
														);
	}

	function selectType($type,$options) {
		if ($this->config == 'eZgraph') {
			$this->eZgraphSelectType($type,$options);
		} else if ($this->config == 'jpgraph'){
			$this->jpgraphSelectType($type,$options);
		}
	}
	
	function selectPalette($options) {
		if ($this->config == 'eZgraph') {
			$this->eZgraphSelectPalette($options);
		} else if ($this->config == 'jpgraph'){
			$this->jpgraphSelectPalette($options);
		}
	}
	
	function setupAxis($options) {
		if ($this->config == 'jpgraph'){
			$this->jpgraphSetupAxis($options);
		}
	}
	
	function addDataPiePlot($options) {
		if ($this->config == 'eZgraph') {
			$this->eZgraphAddData($options);
		} else if ($this->config == 'jpgraph'){
			$this->jpgraphAddDataPiePlot($options);
		}
	}
	
	function addDataBarPlot($options) {
		if ($this->config == 'jpgraph'){
			$this->jpgraphAddDataBarPlot($options);
		}
	}
	
	function addDataLinePlot($options) {
		if ($this->config == 'jpgraph'){
			$this->jpgraphAddDataLinePlot($options);
		}
	}
	
	function accBarPlot($options) {
		if ($this->config == 'jpgraph'){
			$this->jpgraphAccBarPlot($options);
		}
	}
	
	function addSplinePlot($options) {
	  if ($this->config == 'jpgraph'){
			$this->jpgraphAddSplinePlot($options);
		}	
	}
	
	function addSecondAxis ($options) {
	  if ($this->config == 'jpgraph'){
			$this->jpgraphAddSecondAxis($options);
		}
	}
	
	function setMapTarget ($options){
	  if ($this->config == 'jpgraph'){
			$this->jpgraphSetMapTarget($options);
		}
	}
	
	function getHTMLImageMap (){
	  if ($this->config == 'jpgraph'){
			return $this->jpgraphGetHTMLImageMap();
		}
	}
	
	function render ($type,$options) {
		if ($this->config == 'eZgraph') {
			$this->eZgraphRender($type,$options);
		} else if ($this->config == 'jpgraph'){
			$this->jpgraphRender($type,$options);
		}
	}
	
	//$type,$title,$size,$taille,$margin
	function eZgraphSelectType($type,$title,$size,$taille,$margin) {
		if($type == "Pie") {
			$this->graph = new ezcGraphPieChart();
			$this->graph->title = $title;
			$this->graph->options->label = '%3$.1f%%';
			$this->graph->options->font = './shell/arial.ttf';
			$this->graph->legend->symbolSize = $size*9;
  			//$this->graph->legend->portraitSize = ".25";
			$this->graph->options->font->maxFontSize = $size*8;
			$this->graph->options->font->padding = "1";
		}
	}
	
	//$palette
	function eZgraphSelectPalette($palette) {
		if($palette == "ez") {
			$this->graph->palette = new ezcGraphPaletteEz();
			$this->graph->palette->dataSetColor  = $this->palette;
		} else if($palette == "black") {
			$this->graph->palette = new ezcGraphPaletteEzBlack();
		} else if($palette == "blue") {
			$this->graph->palette = new ezcGraphPaletteEzBlue();
		} else if($palette == "green") {
			$this->graph->palette = new ezcGraphPaletteEzGreen();
		} else if($palette == "red") {
			$this->graph->palette = new ezcGraphPaletteEzRed();
		}
	}
	
	//$datas,$title,$size
	function eZgraphAddData($datas,$title,$size) {
		$tab = array();
		foreach($datas as $data) {
  			$tab[$data['legend']] = $data['value'];
		}
		$this->graph->data[$title] = new ezcGraphArrayDataSet($tab);
	}
	
	//$render,$size
	function eZgraphRender($render,$size) {
		global $dPconfig;
		if($dPconfig['graph_svg'] == "non") {
			$this->graph->driver = new ezcGraphGdDriver();
		} else if($dPconfig['graph_svg'] == "oui") {
			$this->graph->renderer->options->moveOut = .2;
 			$this->graph->renderer->options->pieChartOffset = 1;
 			$this->graph->renderer->options->pieChartGleam = .3;
 			$this->graph->renderer->options->pieChartGleamColor = '#FFFFFF';
 			$this->graph->renderer->options->pieChartGleamBorder = 2;
			$this->graph->renderer->options->pieChartShadowSize = 2;
  			$this->graph->renderer->options->pieChartShadowColor = '#000000';
 			$this->graph->renderer->options->legendSymbolGleam = .5;
  			$this->graph->renderer->options->legendSymbolGleamSize = .9;
  			$this->graph->renderer->options->legendSymbolGleamColor = '#FFFFFF';
 			$this->graph->renderer->options->pieChartSymbolColor = '#BABDB688';
		}
		$this->renderer->options->moveOut = .2;
		if($render == "in") {
			$this->graph->render($size*420, $size*200);
		} else if($render == "out") {
			$this->graph->renderToOutput($size*420, $size*200);
		}
	}
	
	//$type,$title,$size,$taille,$margin
	function jpgraphSelectType ($type,$options) {
		$this->options = array_merge($this->options, $options);
		if ($type == "Pie") {
			$this->graph = new PieGraph($this->options['width']*(1+$this->options['size']*0.2),$this->options['height']*$this->options['size'],"auto");	
		} else if ($type == "Graph") {
			$this->graph = new Graph($this->options['width']*$this->options['size'],$this->options['height']*$this->options['size'],"auto");    
			$this->graph->SetScale("textlin");
			$this->graph->subtitle->SetFont(FF_ARIAL,FS_NORMAL,6+$this->options['size']);
		}
		$this->graph->title->Set($this->options['title']);
		$this->graph->subtitle->Set($this->options['subtitle']);
		$this->graph->img->SetMargin($this->options['margin'][0], $this->options['margin'][1], $this->options['margin'][2], $this->options['margin'][3]);
		$this->graph->img->SetAntiAliasing();
		$this->graph->title->SetFont(FF_ARIAL,FS_NORMAL,$this->options['sizeFontTitle']+$this->options['size']);
		$this->graph->legend->SetFont(FF_ARIAL,FS_NORMAL, $this->options['sizeFontLegend']);
		$this->graph->legend->Pos($this->options['posLegend'][0], $this->options['posLegend'][1], $this->options['posLegend'][2], $this->options['posLegend'][3]);
	}

	//$palette
	function jpgraphSelectPalette ($options) {
		$this->options = array_merge($this->options, $options);
		$this->graph->SetMarginColor($this->options['marginColor']);
		$this->graph->title->SetColor($this->options['titleColor']);
		$this->graph->subtitle->SetColor($this->options['subtitleColor']);
	}
	
	//$datax,$size,$taille
	function jpgraphSetupAxis ($options) {
		$this->options = array_merge($this->options, $options);
		// Setup font for axis
		$this->graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,$this->options['sizeFontAxis']+$this->options['size']);
		$this->graph->yaxis->SetFont(FF_ARIAL,FS_NORMAL,$this->options['sizeFontAxis']+$this->options['size']);
		//$this->graph->y2axis->SetFont(FF_ARIAL,FS_NORMAL,$this->options['sizeFontAxis']+$this->options['size']);
		
		// Show 0 label on Y-axis (default is not to show)
		$this->graph->yscale->ticks->SupressZeroLabel(false);
		$this->graph->yaxis->SetColor("black");
		//$this->graph->y2axis->SetColor("#888888");
		
		// Setup X-axis labels
		$this->graph->xaxis->SetTickLabels($this->options['datax']);
		$this->graph->xaxis->SetTextTickInterval($this->options['textTickInterval']);
		$this->graph->xaxis->SetLabelAngle($this->options['labelAngle']);
	}
	
	//$datas,$title,$size
	function jpgraphAddDataPiePlot ($options) {
		$this->options = array_merge($this->options, $options);
		$pplot = new PiePlot($this->options['dataPie']);
		$pplot->SetLegends($this->options['graphPieLegend']);
		$pplot->SetCenter(0.25+($this->options['size']*0.07), 0.55);
		$pplot->SetSize(0.3);
		$pplot->SetGuideLines ();
		$this->graph->Add($pplot);
	}
	
	//$data,$from,$to,$color,$legend
	function jpgraphAddDataBarPlot ($options) {
		$this->options = array_merge($this->options, $options);
		// Create the bar hits pot
		$bplot = new BarPlot($this->options['dataBar']);
		$bplot->SetWidth(0.8);
		$bplot->SetFillGradient($this->options['from'],$this->options['to'],GRAD_LEFT_REFLECTION);
		$bplot->SetColor($this->options['graphBarColor']);
		$bplot->setLegend($this->options['graphBarLegend']);
		$this->graph->AddY2($bplot);
	}
	
	//$data,$legend,$size,$color
	function jpgraphAddDataLinePlot ($options) {
		$this->options = array_merge($this->options, $options);
		if (!is_array($this->options['graphLineLegend'])) {
			$lplot = new LinePlot($this->options['dataLine']);
			$lplot->setLegend($this->options['graphLineLegend']);
			$lplot->SetWeight($this->options['size']);
			$this->graph->Add($lplot);
		} else {
			$i = 0;
			foreach ($this->options['graphLineLegend'] as $key => $value) {
				$lplot = new LinePlot($this->options['dataLine'][$key]);
				$lplot->setLegend($this->options['graphLineLegend'][$key]);
				$lplot->SetWeight($this->options['size']);
				$lplot->SetColor($this->options['palette'][$key]);
				$this->graph->Add($lplot);
				$i++;
			}
		}
	}
	
	function jpgraphAccBarPlot ($options) {
		$this->options = array_merge($this->options, $options);
		//mbTrace($this->options['dataAccBar'])
		$listPlots = array();
		foreach ($this->options['dataAccBar'] as $key => $value) {
			$bplot = new BarPlot($value["data"]);
		  //$bplot->to = "#EEEEEE";
		  $bplot->SetFillGradient($this->options['palette'][$key],$this->options['to'],GRAD_LEFT_REFLECTION);
		  $bplot->SetColor("white");
		  $bplot->setLegend($value["legend"]);
		  $bplot->value->SetFormat("%01.0f");
		  $bplot->value->SetColor($this->options['palette'][$key]);
		  $bplot->value->SetFont(FF_ARIAL,FS_NORMAL, 8); 
		  $listPlots[] = $bplot;
		  if ($this->options['map'] == "oui") {
		  	$bplot->SetCSIMTargets($this->options['mapInfo'][0],$this->options['mapInfo'][1]);
		  }
		}
		$gbplot = new AccBarPlot($listPlots);
		$gbplot->SetWidth(0.6);
		$gbplot->value->SetFormat("%01.0f"); 
		$gbplot->value->show();
		
		$this->graph->Add($gbplot);
	}
	
	function jpgraphAddSplinePlot ($options) {
		$opSorted = $this->options['dataLine'];
		rsort($opSorted);
		$this->graph->SetScale("intint", 0, intval($opSorted[0])+1);
		
		// Create the plot
		$lplot = new LinePlot($this->options['dataLine']);
		$lplot->SetColor("blue");
		$lplot->SetWeight(-10);
		$lplot->value->SetFormat("%01.2f");
		$lplot->value->SetFont(FF_ARIAL,FS_NORMAL, 7);
		$lplot->value->SetMargin(10);
		$lplot->mark->SetType(MARK_FILLEDCIRCLE);
		$lplot->mark->SetColor("blue");
		$lplot->mark->SetFillColor("blue:1.5");
		$lplot->value->show();
		
		// Create the spline plot
		$spline = new Spline(array_keys($this->options['datax']), array_values($this->options['dataLine']));
		list($sdatax,$sdatay) = $spline->Get(50);
		
		$lplot2 = new LinePlot($sdatay, $sdatax);
		$lplot2->SetFillGradient("white", "darkgray");
		$lplot2->SetColor("black");
		
		// Add the plots to the graph
		$this->graph->Add($lplot2);
		$this->graph->Add($lplot);	
	}
	
	function jpgraphAddSecondAxis ($options) {
		$this->options = array_merge($this->options, $options);
		$this->graph->SetY2Scale("int");
		$this->graph->y2axis->SetFont(FF_ARIAL,FS_NORMAL,$this->options['sizeFontAxis']+$this->options['size']);
		$this->graph->y2axis->SetColor("#888888");		
	}
	
	function jpgraphGetHTMLImageMap () {
		return $this->graph->GetHTMLImageMap("graph_interventions");
	}
	
	//$render,$size
	function jpgraphRender ($type,$options) {
		if ($type == "out") {
			$this->graph->Stroke();
		} else if ($type == "in") {
			$this->graph->Stroke($this->options['renderPath']);
		}
	}
}
?>