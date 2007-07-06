<?php

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @author  Poiron Yohann
 *  @version $Revision: $
 */
 
class CMbGraphPie extends ezcGraphPieChart{
	
	function CMbGraphPie($title,$data) {
		parent::__construct();
		$this->palette = new ezcGraphPaletteEz();
  		$this->palette->dataSetColor  = array(
			'#FFFFFF', '#C90062', '#E05206', '#F0AB00', '#000000', '#FF0000', '#3C8A2E', '#006983', '#0098C3', '#21578A', '#55517B', '#4E7D5B', );
  		$this->title = $title;
  		$this->options->label = '%3$.1f%%';
  		$this->legend->symbolSize = "7";
  		$this->legend->portraitSize = ".25";
   		$this->data[$title] = new ezcGraphArrayDataSet($data);
		//$graph->legend = false;
//$this->properties['portraitSize'] = .2;
//        $this->properties['landscapeSize'] = .1;
//        $this->properties['symbolSize'] = 14;
//        $this->properties['padding'] = 1;
//        $this->properties['minimumSymbolSize'] = .05;
//        $this->properties['spacing'] = 2;
 		 $this->options->font = './shell/arial.ttf';
 			
	}
	
	function setMbMode($mode) {
		if($mode == "png") {
			$this->driver = new ezcGraphGdDriver();
			//mbTrace($this->driver);die();
		} else if($mode == "svg") {
			$this->renderer->options->moveOut = .2;
 			$this->renderer->options->pieChartOffset = 1;
 			$this->renderer->options->pieChartGleam = .3;
 			$this->renderer->options->pieChartGleamColor = '#FFFFFF';
 			$this->renderer->options->pieChartGleamBorder = 2;
			$this->renderer->options->pieChartShadowSize = 2;
  			$this->renderer->options->pieChartShadowColor = '#000000';
 			$this->renderer->options->legendSymbolGleam = .5;
  			$this->renderer->options->legendSymbolGleamSize = .9;
  			$this->renderer->options->legendSymbolGleamColor = '#FFFFFF';
 			$this->renderer->options->pieChartSymbolColor = '#BABDB688';
		}
	}
}

?>