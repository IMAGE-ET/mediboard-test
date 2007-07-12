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
														"render" => "out"										 								 												 		
														);
	}

	function selectType($type,$options) {
		if($this->config == 'eZgraph') {
			$this->eZgraphSelectType($type,$options);
		} else if($this->config == 'jpgraph'){
			$this->jpgraphSelectType($type,$options);
		}
	}
	
	function selectPalette($options) {
		if($this->config == 'eZgraph') {
			$this->eZgraphSelectPalette($options);
		} else if($this->config == 'jpgraph'){
			$this->jpgraphSelectPalette($options);
		}
	}
	
	function setupAxis($options) {
		if($this->config == 'jpgraph'){
			$this->jpgraphSetupAxis($options);
		}
	}
	
	function addDataPiePlot($options) {
		if($this->config == 'eZgraph') {
			$this->eZgraphAddData($options);
		} else if($this->config == 'jpgraph'){
			$this->jpgraphAddDataPiePlot($options);
		}
	}
	
	function addDataBarPlot($options) {
		if($this->config == 'jpgraph'){
			$this->jpgraphAddDataBarPlot($options);
		}
	}
	
	function addDataLinePlot($options) {
		if($this->config == 'jpgraph'){
			$this->jpgraphAddDataLinePlot($options);
		}
	}
	
	function accBarPlot($options) {
		if($this->config == 'jpgraph'){
			$this->jpgraphAccBarPlot($options);
		}
	}
	
	function addSecondAxis ($options) {
	if($this->config == 'jpgraph'){
			$this->jpgraphAddSecondAxis($options);
		}
	}
	
	function render($options) {
		if($this->config == 'eZgraph') {
			$this->eZgraphRender($options);
		} else if($this->config == 'jpgraph'){
			$this->jpgraphRender($options);
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
		$this->graph->pplot = new PiePlot($this->options['dataPie']);
		$this->graph->pplot->SetLegends($this->options['graphPieLegend']);
		$this->graph->pplot->SetCenter(0.25+($this->options['size']*0.07), 0.55);
		$this->graph->pplot->SetSize(0.3);
		$this->graph->pplot->SetGuideLines ();
		$this->graph->Add($this->graph->pplot);
	}
	
	//$data,$from,$to,$color,$legend
	function jpgraphAddDataBarPlot ($options) {
		$this->options = array_merge($this->options, $options);
		// Create the bar hits pot
		$this->graph->bplot = new BarPlot($this->options['dataBar']);
		$this->graph->bplot->SetWidth(0.8);
		$this->graph->bplot->SetFillGradient($this->options['from'],$this->options['to'],GRAD_LEFT_REFLECTION);
		$this->graph->bplot->SetColor($this->options['graphBarColor']);
		$this->graph->bplot->setLegend($this->options['graphBarLegend']);
		$this->graph->AddY2($this->graph->bplot);
	}
	
	//$data,$legend,$size,$color
	function jpgraphAddDataLinePlot ($options) {
		$this->options = array_merge($this->options, $options);
		if (!is_array($this->options['graphLineLegend'])) {
			$this->graph->lplot = new LinePlot($this->options['dataLine']);
			$this->graph->lplot->setLegend($this->options['graphLineLegend']);
			$this->graph->lplot->SetWeight($this->options['size']);
			$this->graph->Add($this->graph->lplot);
		} else {
			$i = 0;
			foreach($this->options['graphLineLegend'] as $key => $value) {
				$this->graph->lplot = new LinePlot($this->options['dataLine'][$key]);
				$this->graph->lplot->setLegend($this->options['graphLineLegend'][$key]);
				$this->graph->lplot->SetWeight($this->options['size']);
				$this->graph->lplot->SetColor($this->options['palette'][$key]);
				$this->graph->Add($this->graph->lplot);
				$i++;
			}
		}
	}
	
	function jpgraphAccBarPlot ($options) {
		$this->options = array_merge($this->options, $options);
		$this->graph->listPlots = array();
		foreach($this->options['dataAccBar'] as $key => $value) {
		  $this->graph->bplot = new BarPlot($value["data"]);
		  $this->graph->bplot->to = "#EEEEEE";
		  $this->graph->bplot->SetFillGradient($this->options['palette'][$key],$this->options['to'],GRAD_LEFT_REFLECTION);
		  $this->graph->bplot->SetColor("white");
		  $this->graph->bplot->setLegend($value["legend"]);
		  $this->graph->bplot->value->SetFormat("%01.0f");
		  $this->graph->bplot->value->SetColor($this->options['palette'][$key]);
		  $this->graph->bplot->value->SetFont(FF_ARIAL,FS_NORMAL, 8); 
		  $this->graph->listPlots[] = $this->graph->bplot;
		}

		$this->graph->gbplot = new AccBarPlot($this->graph->listPlots);
		$this->graph->gbplot->SetWidth(0.6);
		$this->graph->gbplot->value->SetFormat("%01.0f"); 
		$this->graph->gbplot->value->show();
		$this->graph->Add($this->graph->gbplot);
	}
	
	function jpgraphAddSecondAxis ($options) {
		$this->options = array_merge($this->options, $options);
		$this->graph->SetY2Scale("int");
		$this->graph->y2axis->SetFont(FF_ARIAL,FS_NORMAL,$this->options['sizeFontAxis']+$this->options['size']);
		$this->graph->y2axis->SetColor("#888888");		
	}
	
	//$render,$size
	function jpgraphRender ($options) {
		if($this->options['render'] == "out") {
			$this->graph->Stroke();
		}
	}
}
?>