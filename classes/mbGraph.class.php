<?php

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @author  Poiron Yohann
 *  @version $Revision: $
 */
 
class CMbGraph {
	var $graph = null;
	
	function selectType($type,$title,$size) {
		if($type == "Pie") {
			$this->graph = new ezcGraphPieChart();
			$this->graph->title = $title;
			$this->graph->options->label = '%3$.1f%%';
			$this->graph->options->font = './shell/arial.ttf';
			$this->graph->legend->symbolSize = $size*9;
  			$this->graph->legend->portraitSize = ".25";
			$this->graph->options->font->maxFontSize = $size*8;
		}
	}
	
	function selectPalette($palette) {
		if($palette == "ez") {
			$this->graph->palette = new ezcGraphPaletteEz();
			$this->graph->palette->dataSetColor  = array(
				"#aa5500", "#55aa00", "#0055aa", "#aa0055", "#5500aa", "#00aa55", "#ff0000", "#00ff00", "#0000ff", "#ffff00", "#ff00ff", "#00ffff", '#FFFFFF', '#C90062', '#E05206', '#F0AB00', '#000000', '#FF0000', '#3C8A2E', '#006983', '#0098C3', '#21578A', '#55517B', '#4E7D5B', );
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
	
	function addData($data,$title) {
		$this->graph->data[$title] = new ezcGraphArrayDataSet($data);
	}
	
	function selectMode($mode) {
		global $dPconfig;
		if($mode == "non") {
			$this->graph->driver = new ezcGraphGdDriver();
		} else if($mode == "oui") {
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
	}
	
	function render($render,$size) {
		$this->renderer->options->moveOut = .2;
		if($render == "in") {
			$this->graph->render($size*300, $size*145);
		} else if($render == "out") {
			$this->graph->renderToOutput($size*300, $size*145);
		}
	}
}
?>