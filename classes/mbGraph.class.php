<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author Poiron Yohann
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireLibraryFile('jpgraph/src/mbjpgraph');
CAppUI::requireLibraryFile('jpgraph/src/jpgraph_bar');
CAppUI::requireLibraryFile('jpgraph/src/jpgraph_line');
CAppUI::requireLibraryFile('jpgraph/src/jpgraph_regstat');
CAppUI::requireLibraryFile('jpgraph/src/jpgraph_pie');

class CMbGraph {
	/**
   * Global properties
   */
	var $graph = null;   //graph object
	var $options = null; //tab of options
	
	/**
  * Constructor
  */
	function CMbGraph() {
		$this->config = CAppUI::conf('graph_engine');
		$this->options = array ("width" => 320,   //width of frame 
														"height" => 125,  //height of frame
														"size" => 1, 			//frame scale
														"title" => "Title",  //title of frame
														"subtitle" => " ",   //subtitle of frame 
														"sizeFontTitle" => 7,  //title font size 
														"palette" => array ( "#aa5500", "#55aa00", "#0055aa", "#aa0055", 
																								 "#5500aa", "#00aa55", "#ff0000", "#00ff00", 
																								 "#0000ff", "#ffff00", "#ff00ff", "#00ffff", 
																								 "#FFFFFF", "#C90062", "#E05206", "#F0AB00", 
																								 "#000000", "#FF0000", "#3C8A2E", "#006983", 
																								 "#0098C3", "#21578A", "#55517B", "#4E7D5B" ),  //color pallets
														"ezCPalette" => "ez", //eZComponent pallets
														"margin" => array (0, 
																							 0, 
																							 0, 
																							 0), //margin of frame
														"marginColor" => "lightblue", //margin color
														"titleColor"	=> "darkred", //title color
														"subtitleColor"	=> "black", //subtitle color
														"sizeFontLegend" => 7, //legend font size 
														"posLegend" => array(0, 
																								 0,
																								 "right",
																								 "top"),  //legend position
														"sizeFontAxis" => 6, //axis font size 
														"labelAngle" => 0,   //label angle
														"textTickInterval" => 0, //text tick interval
														"posXAbsDelta" => 0, //position X 
														"posYAbsDelta" => 0, //position Y
														"dataPie" => array(), //data of pie graph 
														"dataBar" => array(), //data of bar graph 
														"dataLine" => array(), //data of line graph 
														"dataAccBar" => array(), //data of accBar graph 
														"datax" => array(),		//data of X axis							 
														"addY" => "addY1",		//choice add data is Y1 or Y2
														"from" => "#AAAAAA",  //beginning graph color 
														"to" => "#EEEEEE",		//end graph color 
														"graphBarColor" => "white", //color of bar graph 
														"graphBarLegend" => "", //legend of bar graph 
														"graphLineColor" => "white", //color of line graph 
														"graphLineLegend" => "", //legend of line graph 
														"graphSplineLegend" => "", //legend of spline graph
														"graphPieLegend" => "Legend", //legend of pie graph
														"graphAccLegend" => "Legend", //legend of accBar graph
														"graphLineSize" => 1,  //size of line size
														"map" => "non", //choice if graph is a map
													  "mapInfo" => array(),	//map info					 								 		 											 																																										
														"renderPath" => "tmp/graphtmp.png",  //render path
														"nameHtmlImageMap" => "nameHtmlImageMap", //name html image map
														"scale" => array(0, 
																						 0)	//scale of xaxis and yaxis									 									 								 												 		
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
	
	function addAccBarPlot($options) {
		if ($this->config == 'jpgraph'){
			$this->jpgraphAddAccBarPlot($options);
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
	
	function eZgraphSelectType($type,$options) {
		$this->options = array_merge($this->options, $options);
		if($type == "Pie") {
			$this->graph = new ezcGraphPieChart();
			$this->graph->title = $this->options['title'];
			$this->graph->options->label = '%3$.1f%%';
			$this->graph->options->font = './shell/arial.ttf';
			$this->graph->legend->symbolSize = $this->options['size']*9;
  			//$this->graph->legend->portraitSize = ".25";
			$this->graph->options->font->maxFontSize = $this->options['size']*8;
			$this->graph->options->font->padding = "1";
		}
	}
	
	function eZgraphSelectPalette($options) {
		$this->options = array_merge($this->options, $options);
		if($this->options['ezCPalette'] == "ez") {
			$this->graph->palette = new ezcGraphPaletteEz();
			$this->graph->palette->dataSetColor  = $this->options['palette'];
		} else if($this->options['ezCPalette'] == "black") {
			$this->graph->palette = new ezcGraphPaletteEzBlack();
		} else if($this->options['ezCPalette'] == "blue") {
			$this->graph->palette = new ezcGraphPaletteEzBlue();
		} else if($this->options['ezCPalette'] == "green") {
			$this->graph->palette = new ezcGraphPaletteEzGreen();
		} else if($this->options['ezCPalette'] == "red") {
			$this->graph->palette = new ezcGraphPaletteEzRed();
		}
	}
	
	function eZgraphAddData($options) {
		$this->options = array_merge($this->options, $options);
//		$tab = array();
//		foreach($datas as $data) {
//  			$tab[$data['legend']] = $data['value'];
//		}
		$datas = array();
		mbTrace($this->options['dataPie']);
		foreach($values as $value) {
//  		$datas[]  = $data["value"];
//  		$legends[] = $data["legend"];
		}
		$this->graph->data[$this->options['title']] = new ezcGraphArrayDataSet($tab);
	}
	
	function eZgraphRender($render,$options) {
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

	function jpgraphSelectPalette ($options) {
		$this->options = array_merge($this->options, $options);
		$this->graph->SetMarginColor($this->options['marginColor']);
		$this->graph->title->SetColor($this->options['titleColor']);
		$this->graph->subtitle->SetColor($this->options['subtitleColor']);
	}
	
	function jpgraphSetupAxis ($options) {
		$this->options = array_merge($this->options, $options);
		
		if ($this->options['scale'] != array(0,0)) {
			$this->graph->SetScale("intint", $this->options['scale'][0], $this->options['scale'][1]);
		}
		// Setup font for axis
		$this->graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,$this->options['sizeFontAxis']+$this->options['size']);
		$this->graph->yaxis->SetFont(FF_ARIAL,FS_NORMAL,$this->options['sizeFontAxis']+$this->options['size']);
		
		// Show 0 label on Y-axis (default is not to show)
		$this->graph->yscale->ticks->SupressZeroLabel(false);
		$this->graph->yaxis->SetColor("black");

		// Setup X-axis labels
		$this->graph->xaxis->SetTickLabels($this->options['datax']);
		$this->graph->xaxis->SetTextTickInterval($this->options['textTickInterval']);
		$this->graph->xaxis->SetLabelAngle($this->options['labelAngle']);
	}
	
	function jpgraphAddSecondAxis ($options) {
		$this->options = array_merge($this->options, $options);
		$this->graph->SetY2Scale("int");
		$this->graph->y2axis->SetFont(FF_ARIAL,FS_NORMAL,$this->options['sizeFontAxis']+$this->options['size']);
		$this->graph->y2axis->SetColor("#888888");		
	}

	function jpgraphAddDataPiePlot ($options) {
		$this->options = array_merge($this->options, $options);
		$pplot = new PiePlot($this->options['dataPie']);
		$pplot->SetLegends($this->options['graphPieLegend']);
		$pplot->SetCenter(0.25+($this->options['size']*0.07), 0.55);
		$pplot->SetSize(0.3);
		$pplot->SetGuideLines ();
		$this->graph->Add($pplot);
	}
	
	function jpgraphAddDataBarPlot ($options) {
		$this->options = array_merge($this->options, $options);
		// Create the bar hits pot
		$bplot = new BarPlot($this->options['dataBar']);
		$bplot->SetWidth(0.8);
		$bplot->SetFillGradient($this->options['from'],$this->options['to'],GRAD_LEFT_REFLECTION);
		$bplot->SetColor($this->options['graphBarColor']);
		$bplot->setLegend($this->options['graphBarLegend']);
		if ($this->options['addY'] == "addY2") {
			$this->graph->AddY2($bplot);
		} else {
			$this->graph->Add($bplot);
		}
	}
	
	function jpgraphAddDataLinePlot ($options) {
		$this->options = array_merge($this->options, $options);
		if (!is_array($this->options['graphLineLegend'])) {
			$lplot = new LinePlot($this->options['dataLine']);
			$lplot->setLegend($this->options['graphLineLegend']);
			$lplot->SetWeight($this->options['graphLineSize']);
			$this->graph->Add($lplot);
		} else {
			foreach ($this->options['graphLineLegend'] as $key => $value) {
				$lplot = new LinePlot($this->options['dataLine'][$key]);
				$lplot->setLegend($this->options['graphLineLegend'][$key]);
				$lplot->SetWeight($this->options['graphLineSize']);
				$lplot->SetColor($this->options['palette'][$key]);
				$this->graph->Add($lplot);
			}
		}
	}
	
	function jpgraphAddAccBarPlot ($options) {
		$this->options = array_merge($this->options, $options);
			
		$listPlots = array();
		foreach ($this->options['dataAccBar'] as $key => $value) {
			$bplot = new BarPlot($value["data"]);
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
		$this->options = array_merge($this->options, $options);
		
		$this->graph->xgrid->Show();	
		if (!is_array($this->options['graphSplineLegend'])) {
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
			$splot = new LinePlot($sdatay, $sdatax);
			$splot->SetFillGradient("white", "darkgray");
			$splot->SetColor("black");
			
			// Add the plots to the graph
			$this->graph->Add($splot);	
			$this->graph->Add($lplot);
		} else {
			// Create the plot
			foreach ($this->options['graphSplineLegend'] as $key => $value) {
				$lplot = new LinePlot($this->options['dataLine'][$key]);
				$lplot->setLegend($this->options['graphSplineLegend'][$key]);
				$lplot->SetColor($this->options['palette'][$key]);
				$lplot->SetWeight(-10);
				$lplot->value->SetFormat("%01.2f");
				$lplot->value->SetFont(FF_ARIAL,FS_NORMAL, 7);
				$lplot->value->SetMargin(10);
				$lplot->mark->SetType(MARK_FILLEDCIRCLE);
				$lplot->mark->SetColor($this->options['palette'][$key]);
				$lplot->mark->SetFillColor($this->options['palette'][$key].":1.5");
				$lplot->value->show();
				
				// Create the spline plot
				$spline = new Spline(array_keys($this->options['datax']), array_values($this->options['dataLine'][$key]));
				list($sdatax,$sdatay) = $spline->Get(50);			
				$splot = new LinePlot($sdatay, $sdatax);
				
				// Add the plots to the graph
				$this->graph->Add($splot);	
				$this->graph->Add($lplot);
			}	
		}
	}
	
	function jpgraphGetHTMLImageMap () {
		return $this->graph->GetHTMLImageMap("graph_interventions");
	}
	
	function jpgraphRender ($type,$options) {
		if ($type == "out") {
			$this->graph->Stroke();
		} else if ($type == "in") {
			$this->graph->Stroke($this->options['renderPath']);
		}
	}
}
?>