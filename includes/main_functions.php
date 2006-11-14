<?php /* INCLUDES $Id$ */
##
## Global General Purpose Functions
##

$CR = "\n";

// returns a select box based on an key,value array where selected is based on key
function arraySelect( &$arr, $select_name, $select_attribs, $selected, $translate=false ) {
	GLOBAL $AppUI;
	reset( $arr );
	$s = "\n<select name=\"$select_name\" $select_attribs>";
	foreach ($arr as $k => $v ) {
		if ($translate) {
			$v = @$AppUI->_( $v );
			// This is supplied to allow some Hungarian characters to
			// be translated correctly. There are probably others.
			// As such a more general approach probably based upon an
			// array lookup for replacements would be a better approach. AJD.
			$v=str_replace('&#369;','û',$v);
			$v=str_replace('&#337;','õ',$v);
		}
		$s .= "\n\t<option value=\"".$k."\"".($k == $selected ? " selected=\"selected\"" : '').">" . dPformSafe( $v ) . "</option>";
	}
	$s .= "\n</select>\n";
	return $s;
}

// Merges arrays maintaining/overwriting shared numeric indicees
function arrayMerge( $a1, $a2 ) {
	foreach ($a2 as $k => $v) {
		$a1[$k] = $v;
	}
	return $a1;
}

// displays the configuration array of a module for informational purposes
function dPshowModuleConfig( $config ) {
	GLOBAL $AppUI;
	$s = '<table cellspacing="2" cellpadding="2" border="0" class="std" width="50%">';
	$s .= '<tr><th colspan="2">'.$AppUI->_( 'Module Configuration' ).'</th></tr>';
	foreach ($config as $k => $v) {
		$s .= '<tr><td width="50%">'.$AppUI->_( $k ).'</td><td width="50%" class="hilite">'.$AppUI->_( $v ).'</td></tr>';
	}
	$s .= '</table>';
	return ($s);
}

// Function to recussively find an image in a number of places
function dPfindImage( $name, $module=null ) {
  // uistyle must be declared globally
	global $AppUI, $uistyle;

	if (file_exists( "{$AppUI->cfg['root_dir']}/style/$uistyle/images/$name" )) {
		return "./style/$uistyle/images/$name";
	} else if ($module && file_exists( "{$AppUI->cfg['root_dir']}/modules/$module/images/$name" )) {
		return "./modules/$module/images/$name";
	} else if (file_exists( "{$AppUI->cfg['root_dir']}/images/icons/$name" )) {
		return "./images/icons/$name";
	} else if (file_exists( "{$AppUI->cfg['root_dir']}/images/obj/$name" )) {
		return "./images/obj/$name";
	} else {
		return "./images/$name";
	}
}

/**
 *	Workaround removed due to problems in Opera and other issues
 *	with IE6.
 *	Workaround to display png images with alpha-transparency in IE6.0
 */
function dPshowImage( $src, $wid='', $hgt='', $alt='' ) {
	if (strpos( $src, '.png' ) > 0 && strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0' ) !== false) {
		return "<div style=\"height:{$hgt}px; width:{$wid}px; filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='$src', sizingMethod='scale');\" ></div>";
	} else {
		return "<img src=\"$src\" width=\"$wid\" height=\"$hgt\" alt=\"$alt\" border=\"0\" />";
  }
}

// Utility function to return a value from a named array or a specified default
function dPgetParam( &$arr, $name, $def=null ) {
	return isset( $arr[$name] ) ? $arr[$name] : $def;
}

// Make text safe to output into double-quote enclosed attirbutes of an HTML tag
function dPformSafe( $txt, $deslash=false ) {
	if (is_object( $txt )) {
		foreach (get_object_vars($txt) as $k => $v) {
			if ($deslash) {
				$obj->$k = htmlspecialchars( stripslashes( $v ) );
			} else {
				$obj->$k = htmlspecialchars( $v );
			}
		}
	} else if (is_array( $txt )) {
		foreach ($txt as $k=>$v) {
			if ($deslash) {
				$txt[$k] = htmlspecialchars( stripslashes( $v ) );
			} else {
				$txt[$k] = htmlspecialchars( $v );
			}
		}
	} else {
		if ($deslash) {
			$txt = htmlspecialchars( stripslashes( $txt ) );
		} else {
			$txt = htmlspecialchars( $txt );
		}
	}
	return $txt;
}

function formatCurrency( $number, $format ) {
	if (!$format) {
		$format = $AppUI->getPref('SHCURRFORMAT');
	}
	setlocale(LC_MONETARY, $format);
	if (function_exists('money_format'))
		return money_format('%i', $number);

	// NOTE: This is called if money format doesn't exist.
	// Money_format only exists on non-windows 4.3.x sites.
	// This uses localeconv to get the information required
	// to format the money.  It tries to set reasonable defaults.
	$mondat = localeconv();
	//les if font tout planter : currency inutilisable...
	//if (! isset($mondat['int_frac_digits']))
		$mondat['int_frac_digits'] = 2;
	//if (! isset($mondat['int_curr_symbol']))
		$mondat['int_curr_symbol'] = '';
	//if (! isset($mondat['mon_decimal_point']))
		$mondat['mon_decimal_point'] = '.';
	//if (! isset($mondat['mon_thousands_sep']))
		$mondat['mon_thousands_sep'] = ',';
	$numeric_portion = number_format(abs($number),
		$mondat['int_frac_digits'],
		$mondat['mon_decimal_point'],
		$mondat['mon_thousands_sep']);
	// Not sure, but most countries don't put the sign in if it is positive.
	$letter='p';
	$currency_prefix="";
	$currency_suffix="";
	$prefix="";
	$suffix="";
	if ($number < 0) {
		$sign = $mondat['negative_sign'];
		$letter = 'n';
		switch ($mondat['n_sign_posn']) {
			case 0:
				$prefix="(";
				$suffix=")";
				break;
			case 1:
				$prefix = $sign;
				break;
			case 2:
				$suffix = $sign;
				break;
			case 3:
				$currency_prefix = $sign;
				break;
			case 4:
				$currency_suffix = $sign;
				break;
		}
	}
	$currency = $currency_prefix . $mondat['int_curr_symbol'] . $currency_suffix;
	$space = "";
	if ($mondat[$letter . "_sep_by_space"])
		$space = " ";
	if ($mondat[$letter . "_cs_precedes"]) {
		$result = "$currency$space$numeric_portion";
	} else {
		$result = "$numeric_portion$space$currency";
	}
	return $result;
}

?>
