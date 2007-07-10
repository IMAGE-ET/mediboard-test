<?php

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @author  Poiron Yohann
 *  @version $Revision: $
 */
 
class CMbGraph {
	var $graph = null;
	var $palette = null;
	var $config = null;
	
	function CMbGraph() {
		global $dPconfig;
		$this->config = $dPconfig['graph_engine'];
		$this->palette =  array(
				"#aa5500", "#55aa00", "#0055aa", "#aa0055", "#5500aa", "#00aa55", "#ff0000", "#00ff00", "#0000ff", "#ffff00", "#ff00ff", "#00ffff", '#FFFFFF', '#C90062', '#E05206', '#F0AB00', '#000000', '#FF0000', '#3C8A2E', '#006983', '#0098C3', '#21578A', '#55517B', '#4E7D5B', );
	}

	function selectType($type,$title,$size) {
		if($this->config == 'eZgraph') {
			$this->eZgraphSelectType($type,$title,$size);
		} else if($this->config == 'jpgraph'){
			$this->jpgraphSelectType($type,$title,$size);
		}
	}
	
	function selectPalette($palette) {
		if($this->config == 'eZgraph') {
			$this->eZgraphSelectPalette($palette);
		} else if($this->config == 'jpgraph'){
			$this->jpgraphSelectPalette($palette);
		}
	}
	
	function setupAxis($datax,$size) {
		if($this->config == 'jpgraph'){
			$this->jpgraphSetupAxis($datax,$size);
		}
	}
	
	function addDataPiePlot($datas,$title = null,$size = null) {
		if($this->config == 'eZgraph') {
			$this->eZgraphAddData($datas,$title = null,$size = null);
		} else if($this->config == 'jpgraph'){
			$this->jpgraphAddDataPiePlot($datas,$title,$size);
		}
	}
	
	function addDataBarPlot($data,$from,$to,$color,$legend) {
		if($this->config == 'jpgraph'){
			$this->jpgraphAddDataBarPlot($data,$from,$to,$color,$legend);
		}
	}
	
	function addDataLinePlot($data,$legend,$size,$color = null) {
		if($this->config == 'jpgraph'){
			$this->jpgraphAddDataLinePlot($data,$legend,$size,$color);
		}
	}
	
	function render($render,$size = null) {
		if($this->config == 'eZgraph') {
			$this->eZgraphRender($render,$size);
		} else if($this->config == 'jpgraph'){
			$this->jpgraphRender($render,$size);
		}
	}
	
	function eZgraphSelectType($type,$title,$size) {
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
	
	function eZgraphAddData($datas,$title,$size) {
		$tab = array();
		foreach($datas as $data) {
  			$tab[$data['legend']] = $data['value'];
		}
		$this->graph->data[$title] = new ezcGraphArrayDataSet($tab);
	}
	
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
	
	function jpgraphSelectType ($type,$title,$size) {
		if($type == "Pie") {
			$this->graph = new PieGraph(300*(1+$size*0.2),200*$size,"auto");
			$this->graph->title->Set($title);
			$this->graph->title->SetFont(FF_ARIAL,FS_NORMAL,7+$size);
			$this->graph->legend->SetFont(FF_ARIAL,FS_NORMAL, 7);
			$this->graph->legend->Pos(0.015,0.1, "right", "top");
			$this->graph->img->SetAntiAliasing();
		} else if($type == "Bar") {
			$this->graph = new Graph(320*$size,125*$size,"auto");    
			$this->graph->img->SetMargin(15+$size*10,75+$size*10,10+$size*10,15+$size*10);
			$this->graph->img->SetAntiAliasing();
			$this->graph->SetScale("textlin");
			$this->graph->SetY2Scale("int");
			$this->graph->title->Set($title);
			$this->graph->title->SetFont(FF_ARIAL,FS_NORMAL,7+$size);
			$this->graph->subtitle->SetFont(FF_ARIAL,FS_NORMAL,6+$size);
			$this->graph->legend->SetFont(FF_ARIAL,FS_NORMAL, 7);
			$this->graph->legend->Pos(0.015,0.79, "right", "center");
		}
	}
		
	function jpgraphSelectPalette ($palette) {
		$this->graph->SetMarginColor($palette);
		$this->graph->title->SetColor("darkred");
	}
	
	function jpgraphSetupAxis ($datax,$size) {
		// Setup font for axis
		$this->graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,6+$size);
		$this->graph->yaxis->SetFont(FF_ARIAL,FS_NORMAL,6+$size);
		$this->graph->y2axis->SetFont(FF_ARIAL,FS_NORMAL,6+$size);
		
		// Show 0 label on Y-axis (default is not to show)
		$this->graph->yscale->ticks->SupressZeroLabel(false);
		$this->graph->y2axis->SetColor("#888888");
		$this->graph->yaxis->SetColor("black");
		
		// Setup X-axis labels
		$this->graph->xaxis->SetTickLabels($datax);
		$this->graph->xaxis->SetTextTickInterval(2);
		$this->graph->xaxis->SetLabelAngle(50);
	}
	
	function jpgraphAddDataPiePlot ($datas,$title,$size) {
		$values  = array();
		$legends = array();
		foreach($datas as $data) {
  			$values[]  = $data["value"];
  			$legends[] = $data["legend"];
		}
		$this->graph->pplot = new PiePlot($values);
		$this->graph->pplot->SetLegends($legends);
		$this->graph->pplot->SetCenter(0.25+($size*0.07), 0.55);
		$this->graph->pplot->SetSize(0.3);
		$this->graph->pplot->SetGuideLines ();
		$this->graph->Add($this->graph->pplot);
	}
	
	function jpgraphAddDataBarPlot ($data,$from,$to,$color,$legend) {
		// Create the bar hits pot
		$this->graph->bplot = new BarPlot($data);
		$this->graph->bplot->SetWidth(0.8);
		$this->graph->bplot->SetFillGradient($from,$to,GRAD_LEFT_REFLECTION);
		$this->graph->bplot->SetColor($color);
		$this->graph->bplot->setLegend($legend);
		$this->graph->AddY2($this->graph->bplot);
	}
	
	function jpgraphAddDataLinePlot ($data,$legend,$size,$color) {
		//pas de légende
		if ($legend == null) {
		  return "Ne pas peut pas avoir une valeur nulle";
		} else if (!is_array($legend)) {
			$this->graph->lplot = new LinePlot($data);
			if($color == null) {
					$this->graph->lplot->SetColor($this->palette[0]);
			} else {
					$this->graph->lplot->SetColor($color);
			}
			$this->graph->lplot->SetWeight($size);
			$this->graph->lplot->setLegend($legend);
			$this->graph->Add($this->graph->lplot);
		} else {
			$i = 0;
			foreach($legend as $key => $value) {
				$this->graph->lplot = new LinePlot($data[$key]);
				$this->graph->lplot->setLegend($legend[$key]);
				$this->graph->lplot->SetWeight($size);
				if($color == null) {
					$this->graph->lplot->SetColor($this->palette[$i]);
				} else {
					$this->graph->lplot->SetColor($color[$key]);
				}
				$this->graph->Add($this->graph->lplot);
				$i++;
			}
		}
	}
	
	function jpgraphRender ($render,$size) {
		if($render == "out") {
			$this->graph->Stroke();
		}
	}
}
?>