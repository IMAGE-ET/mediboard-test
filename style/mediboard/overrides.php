<?php /* $Id$ */

//
// This overrides the show() function of the CTitleBlock_core class
//
class CTitleBlock extends CTitleBlock_core {
	function show($modeReturn = 0) {
		global $AppUI;
    
		$s = "\n<table class='titleblock'>";
		$s .= "\n<tr>";
    
		if ($this->icon) {
			$s .= "\n<td>";
			$s .= dPshowImage( dPFindImage( $this->icon, $this->module ), '24', '24' );
			$s .= "</td>";
		}

		$s .= "\n<td class='titlecell'>" . $AppUI->_($this->title) . "</td>";

		foreach ($this->cells1 as $c) {
			$s .= "\n$c[2]<td $c[0]>$c[1]</td>$c[3]";
		}
    
		$s .= "\n</tr>";
		$s .= "\n</table>";

		if (count( $this->crumbs ) || count( $this->cells2 )) {
			$crumbs = array();

			foreach ($this->crumbs as $k => $v) {
        $t = "";
        
        if ($v[1]) {
          $crumbIcon = dPfindImage( $v[1], $this->module );
  				$t = "<img src='$crumbIcon' />";            
        }
        
				$t .= $AppUI->_( $v[0] );
				$crumbs[] = "<a href='$k'>$t</a>";
			}

			$s .= "\n<table class='crumbsblock'>";
			$s .= "\n<tr>";
			$s .= "\n\t<td class='crumbscell'>";
			$s .= "\n\t\t" . implode(" \n<strong> : </strong>", $crumbs );
			$s .= "\n\t</td>";

			foreach ($this->cells2 as $c) {
  			$s .= "\n$c[2]<td $c[0]>$c[1]</td>$c[3]";
			}

			$s .= "\n</tr>";
      $s .= "\n</table>";
		}

    if($modeReturn){
      return $s;
    }else{
          echo "$s";
    }
	}
}

//
// This overrides the show() function of the CTabBox_core class
//
class CTabBox extends CTabBox_core {
  function CTabBox($baseHRef, $baseInc, $active) {
    $this->tabs = array();
    $this->active = $active;
    $this->baseHRef = ($baseHRef ? "$baseHRef&amp;" : "?");
    $this->baseInc = $baseInc;
  }

  function show( $extra='' ) {
    $this->checkActive();
    global $AppUI;
    
    $uistyle = $AppUI->getPref( 'UISTYLE' );
    if (!$uistyle)
      $uistyle = $AppUI->cfg['host_style'];
    if (!$uistyle)
      $uistyle = 'default';
      
    // tabbed / flat view options
    reset( $this->tabs );
    $s = "\n<table class='taborflat'>";
    $s .= "\n<tr>";

  	if (@$AppUI->getPref( 'TABVIEW' ) == 0) {
    	$s .= "\n<td><a href='{$this->baseHRef}tab=0'>".$AppUI->_('tabbed')."</a></td>";
      $s .= "\n<td><strong>:</strong></td>";
    	$s .= "\n<td><a href='{$this->baseHRef}tab=-1'>".$AppUI->_('flat')."</a></td>";
    }

    $s .= "\n$extra\n</tr></table>";
    //Don't show the tabbed / flat option
    //echo $s;
    
    if ($this->active < 0 || @$AppUI->getPref( 'TABVIEW' ) == 2 ) {
      // flat view, active = -1
      echo "\n<table class='flatview'>\n";

      foreach ($this->tabs as $v) {
        echo "\n<tr><td><strong>".$AppUI->_($v[1])."</strong></td></tr>";
        echo "\n<tr><td>";
        include $this->baseInc.$v[0].".php";
        echo "\n</td></tr>";
      }

      echo "\n</table>";
    } else {
      // tabbed view
      $s = "\n<table class='tabview'>";
      $s .= "\n<tr>\n<td>";
      
      $s .= "\n<table class='tabmenu' cellspacing='0'>"; // IE Hack: cellspacing should be useless
      $s .= "\n\t<tr>";
      
      foreach( $this->tabs as $k => $v ) {
        $sel = ($k == $this->active) ? "selected" : "normal";
        $value = $AppUI->_($v[1]);
        
        $s .= "\n\t\t";
        $s .= "<td class='{$sel}Left' />";
        $s .= "<td class='$sel'><a href='{$this->baseHRef}tab=$v[0]'>$value</a></td>";
        $s .= "<td class='{$sel}Right' />";
      }

      $s .= "\n\t</tr>";
      $s .= "\n</table>";
      
      $s .= "\n</td>\n</tr>";
      $s .= "\n<tr>\n<td class='tabox'>";
      echo $s;

      $activeURL = $this->baseInc.$this->tabs[$this->active][0];

      // Will be null if the previous selection tab is not available in the new window eg. Children tasks
      if ($activeURL)
        require "$activeURL.php";
        
      echo "\n</td>\n</tr>";
      echo "\n</table>";
    }
  }
}
?>
