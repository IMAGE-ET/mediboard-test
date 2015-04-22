<?php

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage dPpatients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

/**
 * Description
 */
class CConstantGraph {

  /** @var CMbObject The host of the configs (CGroups|CFunction|CService) */
  private $host;
  /** @var string The selected context of the constants */
  private $context_guid;
  /** @var bool Full display or limited display for synthesis */
  private $widget;
  /** @var array The ticks of the xaxis */
  private $ticks;
  /** @var array The colors for each constant */
  private $colors;

  /** @var array Key mode for classic display or time display, and key time for the time interval displayed */
  public $display;
  /** @var array The graph structure */
  public $structure;
  /** @var array The graph data */
  public $graphs;
  /** @var integer The index of the min x-coordinate displayed */
  public $min_x_index;
  /** @var integer The value of the min x-coordinate displayed */
  public $min_x_value;

  /** @var array The default colors for the curve */
  static private $default_colors = array('#0066FF', '#FF0000', '#FF9600', '#009900', '#9900CC');

  /**
   * Constructor
   *
   * @param CMbObject|string $host         The host of the configs (CGroups|CFunction|CService)
   * @param string           $context_guid The selected context of the constants
   * @param bool             $widget       Full display or limited display for synthesis
   * @param bool             $full_time    If true, the graph will be displayed in time mode (with no selected periods, unlike the display mode config)
   */
  function __construct($host, $context_guid, $widget = false, $full_time = false) {
    $this->host = $host;
    $this->context_guid = $context_guid;
    $this->widget = $widget;
    if ($full_time) {
      $this->display = array('mode' => 'time', 'time' => 'full');
    }
    else {
      $this->display = self::getDisplayMode($host);
    }
    $this->colors = self::getColors($this->host);
  }

  /**
   * Format the datas for the Flot plotting library
   *
   * @param CConstantesMedicales[] $constants          The CConstantesMedicales objects who contain the values for the constants
   * @param array                  $constants_by_graph Allow to have a custom graph structure
   *
   * @return void
   */
  function formatGraphDatas($constants, $constants_by_graph = array()) {
    $datas = array();

    if (empty($constants_by_graph)) {
      $this->sortConstantsbyGraph($constants);
    }
    else {
      $this->structure = $constants_by_graph;
    }

    $xaxis = $this->createXaxis($constants);

    foreach ($this->structure as $_rank => $_graphs) {
      foreach ($_graphs as $_graph_constants) {
        $title = '';
        $yaxes = array();
        $markings = array();
        $axis_id = 1;
        $series = array();

        foreach ($_graph_constants as $_constant) {
          if (!array_key_exists($_constant, $this->colors)) {
            $this->colors[$_constant] = self::$default_colors[$axis_id - 1];
          }

          // The label of the yaxis and the title of the graph are formatted
          $label = CAppUI::tr("CConstantesMedicales-$_constant-court");
          if (CConstantesMedicales::$list_constantes[$_constant]['unit'] && !$this->widget) {
            $label .= ' (' . CConstantesMedicales::$list_constantes[$_constant]['unit'] . ')';
          }
          $label = utf8_encode($label);

          if ($axis_id == 1) {
            $title = $label;
          }
          else {
            $title .= " + $label";
          }

          /* The type of series is determined for this constant */
          list($constant_type, $cumul) = self::getConstantType($_constant);

          $values = self::getDatasForConstant($_constant, $constant_type, $constants);

          $yaxis = $this->createYaxis($_constant, $values, $axis_id);

          /* Create the markings for the standard value */
          $config = self::getConfigFor($_constant, $this->host);
          $color = $this->colors[$_constant];
            $color = 'rgba(' . hexdec(substr($color, 1, 2)) .
              ', ' . hexdec(substr($color, 3, 2)) .
              ', ' . hexdec(substr($color, 5, 2)) . ', 0.5)';

          if ($config['norm_min'] != 0) {
            $markings[] = array(
              'y' . $axis_id . 'axis' => array(
                'from'    => $config['norm_min'],
                'to'      => $config['norm_min'],
              ),
              'color'   => $color,
            );
          }
          if ($config['norm_max'] != 0) {
            $markings[] = array(
              'y' . $axis_id . 'axis' => array(
                'from'    => $config['norm_max'],
                'to'      => $config['norm_max'],
              ),
              'color'   => $color,
            );
          }

          $series = $this->addSerie($series, $values, $_constant, $constant_type, $axis_id, $label);

          $yaxes[] = $yaxis;
          $axis_id++;
        }

        /* Create the graph structure and add the datas */
        $graph = array(
          'title'   => $title,
          'datas'   => $series,
          'options' => array(
            'xaxis'   => $xaxis,
            'yaxes'   => $yaxes,
            'series'  => array(
              'shadowSize'  => 0,
              'bandwidth'   => array(
                'active'        => 1,
              )
            ),
            'grid' => array(
              'clickable' => 1,
              'hoverable' => 1,
            )
          ),
          'cumul'       => $cumul,
          'margin_left' => (5 - count($yaxes)) * 40,
          'width'       => 700 - ((5 - count($yaxes)) * 40),
        );

        if (sizeof($_graph_constants) > 1 && !$this->widget) {
          $graph['options']['grid']['borderColor'] = array(
            'top' => '#4B4B4B',
            'right' => '#4B4B4B',
            'left' => $this->colors[$_graph_constants[0]],
            'bottom' => '#4B4B4B'
          );
        }

        if (!empty($markings)) {
          $graph['options']['grid']['markings'] = $markings;
        }

        $datas[$_rank][] = $graph;
      }
    }

    $this->graphs = $datas;
  }

  /**
   * Sort the constants by rank, and create a structure for dividing the constants in graphs
   *
   * @param CConstantesMedicales[] $constants The CConstantesMedicales objects who contain the values for the constants
   *
   * @return array
   */
  function sortConstantsbyGraph($constants) {
    $constants_list = array();
    $constants_by_graph = array();
    $constants_by_rank = CConstantesMedicales::getConstantsByRank('graph', false, $this->host);

    /* The valued constants are sorted by rank */
    foreach (CConstantesMedicales::$list_constantes as $cst_name => $cst_attr) {
      if (substr($cst_name, 0, 1) == '_' && !isset($cst_attr['plot'])) {
        continue;
      }
      foreach ($constants as $cst) {
        if (!is_null($cst->$cst_name) && array_search($cst_name, $constants_list) === false) {
          $rank = CMbArray::searchRecursive($cst_name, $constants_by_rank);
          if (empty($rank)) {
            continue;
          }
          $rank = array_keys($rank['all']);
          $rank = $rank[0];

          if (!array_key_exists($rank, $constants_by_graph)) {
            $constants_by_graph[$rank] = array();
          }
          $constants_by_graph[$rank][] = $cst_name;
          $constants_list[] = $cst_name;
        }
      }
    }
    /* We remove the constant with the rank 0, sort the array and add the rank 0 at the end of the array */
    $hidden_cst = null;
    if (array_key_exists('hidden', $constants_by_graph)) {
      $hidden_cst = $constants_by_graph['hidden'];
      unset($constants_by_graph['hidden']);
    }
    ksort($constants_by_graph);
    if (!is_null($hidden_cst)) {
      $constants_by_graph['hidden'] = $hidden_cst;
    }

    $stacked_graphs = CConstantesMedicales::getHostConfig('stacked_graphs', $this->host);

    foreach ($constants_by_graph as $_rank => $_constants) {
      $constants_by_graph[$_rank] = array();

      if ($_rank != 'hidden' && $stacked_graphs) {
        $cumuls_constants = array();
        foreach ($_constants as $_key => $_constant) {
          /* The constants with a cumul can't be stacked with other constants */
          if (isset(CConstantesMedicales::$list_constantes[$_constant]['cumul_reset_config'])) {
            unset($_constants[$_key]);
            $cumuls_constants[] = $_constant;
          }
        }

        /* The number of constants by graph is limited to 5 */
        if (count($_constants) > 5) {
          $constants = array();
          for ($i = 0; $i < count($_constants); $i = $i + 5) {
            $constants[] = array_slice($_constants, $i, 5);
          }
          foreach ($cumuls_constants as $_key => $_cumul) {
            $cumuls_constants[$_key] = array($_cumul);
          }
          $constants_by_graph[$_rank] = array_merge($constants, $cumuls_constants);
        }
        else {
          if (!empty($_constants)) {
            $constants_by_graph[$_rank][] = $_constants;
          }
          foreach ($cumuls_constants as $_cumul) {
            $constants_by_graph[$_rank][] = array($_cumul);
          }
        }
      }
      else {
        /* The constants with a rank of 0 can't be stacked */
        foreach ($_constants as $_constant) {
          $constants_by_graph[$_rank][] = array($_constant);
        }
      }
    }

    $this->structure = $constants_by_graph;
  }

  /**
   * Create the xaxis
   *
   * @param CConstantesMedicales[] $constants The CConstantesMedicales objects
   *
   * @return array The xaxis
   */
  function createXaxis($constants) {
    $xaxis = array();
    $min_x_index = 0;
    if ($this->display['mode'] == 'time') {
      $ticks = self::createTicksTimeMode($constants);
      $xaxis['mode']  = 'time';

      if ($this->display['time'] == 'full') {
        $max = end($ticks);
        $min_x_value = reset($ticks);
        $min = $min_x_value;
        $xaxis['timeformat'] = '%d/%m/%y';
      }
      else {
        $dtz = new DateTimeZone('Europe/Paris');
        $tz_ofsset = $dtz->getOffset(new DateTime('now', $dtz));

        $ticks = self::createTicksTimeMode($constants);
        $min_x_value = (time() - ($this->display['time'] * 3600) + $tz_ofsset) * 1000;
        $min = $min_x_value;
        $max = (time() + $tz_ofsset) * 1000;
        $xaxis['timeformat'] = '%d/%m/%y<br/>%H:%M';
      }
    }
    else {
      $ticks = $this->createTicks($constants);
      $min_x_value = 1;
      if (count($ticks) > 15) {
        $min_x_index = count($ticks) - 15;
        $min_x_value = $ticks[$min_x_index][0];
      }
      $min = $min_x_value - 0.5;
      if (!$this->widget) {
        $max = $min + 15;
      }
      else {
        $max = $min + 10;
      }
      $xaxis['ticks'] = $ticks;
    }

    $this->ticks = $ticks;
    $this->min_x_index = $min_x_index;
    $this->min_x_value = $min_x_value;
    $xaxis['position'] = 'bottom';
    $xaxis['min'] = $min;
    $xaxis['max'] = $max;
    if ($this->widget) {
      $xaxis['font'] = array(
        'size' => 8,
        'color' => '#000000'
      );
      $xaxis['alignTickWithAxis'] = 1;
    }

    return $xaxis;
  }

  /**
   * Create the ticks for the xaxis
   *
   * @param CConstantesMedicales[] $constants The CConstantesMedicales objects
   *
   * @return array
   */
  function createTicks($constants) {
    $ticks = array();
    $i = 1;
    foreach ($constants as $_cst) {
      $_cst->loadRefContext();

      $style = 'cursor: pointer;';
      if (isset($_cst->comment)) {
        $style .= 'color: red';
      }
      if (!$this->widget) {
        $str = "<span style=\"$style\" onclick=\"editConstants('$_cst->_id', '$_cst->context_class-$_cst->context_id')\">";
        $str .= '<strong>' . CMbDT::format($_cst->datetime, '%Hh%M') . '</strong><br/>'.
          CMbDT::format($_cst->datetime, '%d/%m') . '</span>';
      }
      else {
        $str = '<strong>' . CMbDT::format($_cst->datetime, '%Hh%M') . '</strong><br/>'.
          CMbDT::format($_cst->datetime, '%d/%m');
      }

      $ticks[] = array(
        $i,
        utf8_encode($str)
      );
      $i++;
    }

    return $ticks;
  }

  /**
   * Create the ticks for the xaxis in time mode
   *
   * @param CConstantesMedicales[] $constants The CConstantesMedicales objects
   *
   * @return array
   */
  function createTicksTimeMode($constants) {
    $ticks = array();

    foreach ($constants as $_cst) {
      $ticks[] = CMbDate::toUTCTimestamp($_cst->datetime);
    }

    return $ticks;
  }

  /**
   * Create the yaxis for a cosntant
   *
   * @param string  $constant The name of the constant
   * @param array   $values   The values of the constant
   * @param integer $axis_id  The id of the yaxis
   *
   * @return array
   */
  function createYaxis($constant, $values, $axis_id) {
    $yaxis = array(
      'label'               => utf8_encode(CAppUI::tr("CConstantesMedicales-$constant-court")),
      'position'            => 'left',
      'tickDecimals'        => 1,
      'labelWidth'          => 20,
      'reserveSpace'        => true
    );
    if (array_key_exists('min', $values) && array_key_exists('max', $values)) {
      $yaxis['min'] = self::getMin($constant, $values['min'], $this->host);
      $yaxis['max'] = self::getMax($constant, $values['max'], $this->host);
    }
    if ($axis_id != 1) {
      $yaxis['color'] = $this->colors[$constant];
    }
    if ($this->widget) {
      $yaxis['show'] = false;
      $yaxis['reserveSpace'] = false;
    }

    return $yaxis;
  }

  /**
   * Add series to the list of series of the current graph
   *
   * @param array   $series        The array to which the series will be added
   * @param array   $values        The values of the constant
   * @param string  $constant_name The name of the constant
   * @param string  $constant_type The type of the constant (line, bandwidth, cumul, formula
   * @param integer $axis_id       The id of the yaxis
   * @param string  $label         The label of the series
   *
   * @return array
   */
  function addSerie($series, $values, $constant_name, $constant_type, $axis_id, $label) {
    /* Add the datas, and the options of the serie to the graph */
    switch ($constant_type) {
      case 'bandwidth':
        $series[] = array(
          'data'      => $values['values'],
          'yaxis'     => $axis_id,
          'label'     => $label,
          'unit'      => utf8_encode(CMbString::htmlEntities(CConstantesMedicales::$list_constantes[$constant_name]['unit'])),
          'color'     => $this->colors[$constant_name],
          'bandwidth' => array(
            'show' => true,
            'lineWidth' => "6px"
          ),
          'name' => utf8_encode($constant_name)
        );
        break;
      case 'cumul':
        $i = 0;
        foreach ($values['cumul'] as $_cumul) {
          $series[] = array(
            'data'  => array($_cumul),
            'yaxis' => $axis_id,
            'label' => $i == 0 ? 'Cumul ' . $label : null,
            'color' => '#4DA74D',
            'unit'  => utf8_encode(CMbString::htmlEntities(CConstantesMedicales::$list_constantes[$constant_name]['unit'])),
            'bars' => array(
              'show'  => true,
              'fill'  => true,
              'barWidth' => $_cumul['barWidth'],
            )
          );
          $i++;
        }

        $series[] = array(
          'data'    => $values['values'],
          'yaxis'   => $axis_id,
          'label'   => $label,
          'color'   => $this->colors[$constant_name],
          'unit'    => utf8_encode(CMbString::htmlEntities(CConstantesMedicales::$list_constantes[$constant_name]['unit'])),
          'lines'   => array(
            'show'    => 1
          ),
          'points'  => array(
            'show'      => 1,
          ),
          'name' => utf8_encode($constant_name)
        );
        break;
      case 'formula':
        $i = 0;
        foreach ($values['cumul'] as $_cumul) {
          $series[] = array(
            'data'  => array($_cumul),
            'yaxis' => $axis_id,
            'label' => $i == 0 ? $label : null,
            'color' => '#006EFF',
            'unit'  => utf8_encode(CMbString::htmlEntities(CConstantesMedicales::$list_constantes[$constant_name]['unit'])),
            'bars' => array(
              'show'  => true,
              'fill'  => true,
              'barWidth' => $_cumul['barWidth'],
            )
          );

          $i++;
        }
        break;
      case 'line':
        $series[] = array(
          'data'    => $values['values'],
          'yaxis'   => $axis_id,
          'label'   => $label,
          'color'   => $this->colors[$constant_name],
          'unit'    => utf8_encode(CMbString::htmlEntities(CConstantesMedicales::$list_constantes[$constant_name]['unit'])),
          'lines'   => array(
            'show'    => 1
          ),
          'points'  => array(
            'show'      => 1
          ),
          'name' => utf8_encode($constant_name)
        );
        break;
    }

    return $series;
  }

  /**
   * Return the formated datas of a constant for making graphs with Flot
   *
   * @param string                 $constant_name   The name of the constant
   * @param string                 $constant_type   The type of the constant : line|cumul|formula|bandwidth
   * @param CConstantesMedicales[] $constant_values The CConstantesMedicales containing the values
   *
   * @return array
   */
  function getDatasForConstant($constant_name, $constant_type, $constant_values) {
    $tick = 1;
    $datas = array(
      'values' => array()
    );
    $values = array();

    /* Initializing the period for the cumul */
    if (($constant_type == 'cumul' || $constant_type == 'formula') && !empty($constant_values)) {
      $reset_hour = CConstantesMedicales::getResetHour($constant_name);
      $first_value = reset($constant_values);

      $start = strtotime(CMbDT::format($first_value->datetime, "%Y-%m-%d 0$reset_hour:00:00"));
      if (strtotime($first_value->datetime) < $start) {
        $start = $start - 24 * 3600;
      }
      $current_period = array(
        'start' => $start,
        'end'   => $start + 24 * 3600,
      );
      $current_value = null;
      $start_tick = 1;
      $periods = array();
    }

    foreach ($constant_values as $_value) {
      /* Calculating the value for the cumul */
      if ($constant_type == 'cumul' || $constant_type == 'formula') {
        if ($constant_type == 'formula') {
          $_value->$constant_name = $_value->applyFormula($constant_name);
        }

        $timestamp = strtotime($_value->datetime);

        /* If the current CConstantesMedicales's datetime is in the current period, we add the value to the cumuled value */
        if ($timestamp > $current_period['start'] && $timestamp <= $current_period['end']) {
          $current_value += $_value->$constant_name;
        }
        else {
          /* If not, we add the cumuled value to the periods array, and initialize the current period */
          $periods[] = array(
            'start' => $start_tick,
            'end'   => $tick,
            'start_time'  => $current_period['start'],
            'end_time'    => $current_period['end'],
            'value' => $current_value
          );

          $current_value = $_value->$constant_name;
          $start_tick = $tick;
          $start = $current_period['end'];

          if ((strtotime($_value->datetime) - $start) > (24 * 3600)) {
            $start = strtotime(CMbDT::format($_value->datetime, '%Y-%m-%d' . sprintf('%02d', $reset_hour) . ':00:00'));
            if (strtotime($_value->datetime) < $start) {
              $start = $start - 24 * 3600;
            }
          }
          $current_period = array(
            'start' => $start,
            'end'   => $start + 24 * 3600,
          );
        }
      }

      if (!is_null($_value->$constant_name) && $constant_type != 'formula') {
        /* Get the last 5 user logs of the CConstantesMedicales object */
        $users_logs = array();
        if ($constant_name[0] !== "_") {
          $_value->loadRefUser();

          if ($_value->_ref_user) {
            $users_logs[] = utf8_encode(CMbDT::format($_value->getCreationDate(), '%d/%m/%y %H:%M') . ' - ' . $_value->_ref_user->_view);
          }
        }

        if ($this->display['mode'] == 'time') {
          $entry = array(CMbDate::toUTCTimestamp($_value->datetime));
        }
        else {
          $entry = array($tick);
        }

        if ($constant_type == 'bandwidth') {
          $field = CConstantesMedicales::$list_constantes[$constant_name]['formfields'];

          $entry[] = $_value->$field[0];
          $entry[] = $_value->$field[1];
          $values[] = $_value->$field[0];
          $values[] = $_value->$field[1];
        }
        else {
          if (isset(CConstantesMedicales::$list_constantes[$constant_name]['formfields'])) {
            $field = CConstantesMedicales::$list_constantes[$constant_name]['formfields'];
            $entry[] = $_value->$field[0];
            $values[] = $_value->$field[0];
          }
          else {
            $entry[] = $_value->$constant_name;
            $values[] = $_value->$constant_name;
          }
        }
        $entry['id'] = $_value->_id;
        $entry['date'] = utf8_encode(CMbDT::format($_value->datetime, '%d/%m/%y'));
        $entry['hour'] = utf8_encode(CMbDT::format($_value->datetime, '%Hh%M'));
        $entry['users'] = $users_logs;
        if ($_value->comment) {
          $entry['comment'] = utf8_encode($_value->comment);
        }
        if ("$_value->context_class-$_value->context_id" !== $this->context_guid) {
          $_value->loadRefContext();
          if ($_value->_ref_context) {
            $_value->_ref_context->loadRefsFwd();
            $entry['context']      = utf8_encode($_value->_ref_context->_view);
            $entry['context_guid'] = "$_value->context_class-$_value->context_id";
          }
        }

        $datas['values'][] = $entry;
      }

      $tick++;
    }

    if ($constant_type == 'cumul' || $constant_type == 'formula') {
      $cumul_datas = array();

      if (!empty($constant_values)) {
        $periods[] = array(
          'start' => $start_tick,
          'end'   => $tick,
          'start_time'  => $current_period['start'],
          'end_time'    => $current_period['end'],
          'value' => $current_value
        );

        $cumul_datas = array();
        $dtz = new DateTimeZone('Europe/Paris');
        $tz_ofsset = $dtz->getOffset(new DateTime('now', $dtz));
        foreach ($periods as $_period) {
          if ($this->display['mode'] == 'time') {
            $x = ($_period['start_time'] + $tz_ofsset) * 1000;
            $bar_width = ($_period['end_time'] - $_period['start_time']) * 1000;
          }
          else {
            $x = $_period['start'];
            $bar_width = $_period['end'] - $_period['start'];
          }
          if ($this->widget) {
            $first_value = reset($constant_values);
            $start_date = strftime('%Y-%m-%d %H:%M:%S', $_period['start_time']);
            $end_date = strftime('%Y-%m-%d %H:%M:%S', $_period['end_time']);
            $query = new CRequest();
            $query->addSelect("SUM(`$constant_name`)");
            $query->addTable('constantes_medicales');
            $query->addWhere(array(
              "`patient_id` = $first_value->patient_id",
              "`context_class` = '$first_value->context_class'",
              "`context_id` = $first_value->context_id",
              "`datetime` >= '$start_date'", "`datetime` <= '$end_date'", "`$constant_name` IS NOT NULL"
            ));
            $ds = CSQLDataSource::get('std');
            $_period['value'] = $ds->loadResult($query->makeSelect());
          }

          $cumul_datas[]  = array(
            $x,
            $_period['value'],
            'date' => utf8_encode(
              strftime('%d/%m/%y %H:%M', $_period['start_time']) . ' au ' . strftime('%d/%m/%y %H:%M', $_period['end_time'])
            ),
            'barWidth' => $bar_width
          );
          $values[] = $_period['value'];
        }
      }
      $datas['cumul'] = $cumul_datas;
    }
    if (!empty($values)) {
      $datas['min'] = floor(min($values));
      $datas['max'] = ceil(max($values));
    }

    return $datas;
  }

  /**
   * Return the max for the Yaxis
   *
   * @param string    $constant  The name of the constant
   * @param integer   $max_value The minimum value
   * @param CMbObject $host      The host
   *
   * @return integer
   */
  static function getMax($constant, $max_value, $host = null) {
    $config = self::getConfigFor($constant, $host);

    if ($config['mode'] == 'float') {
      $max = $max_value + $config['max'];
    }
    else {
      $max = max($config['max'], $max_value * 1.05);
    }

    return $max;
  }

  /**
   * Return the min for the Yaxis
   *
   * @param string    $constant  The name of the constant
   * @param integer   $min_value The minimum value
   * @param CMbObject $host      The host
   *
   * @return integer
   */
  static function getMin($constant, $min_value, $host = null) {
    $config = self::getConfigFor($constant, $host);

    if ($config['mode'] == 'float') {
      $min = $min_value - $config['min'];
    }
    else {
      $min = $config['min'];
    }

    return $min;
  }

  /**
   * Get the color configs for the constants
   *
   * @param CMbObject|string $host Host from which we'll get the configuration
   *
   * @return array
   */
  static function getColors($host = null) {
    if ($host) {
      $configs = CConstantesMedicales::getHostConfig('selection', $host);
    }
    else {
      $configs = CConstantesMedicales::getConfig('selection');
    }

    foreach ($configs as $_constant => $_config) {
      $_config = explode('|', $_config);
      if (array_key_exists(2, $_config) && $_config[2] != '') {
        $configs[$_constant] = "#$_config[2]";
      }
      else {
        unset($configs[$_constant]);
      }
    }

    return $configs;
  }

  /**
   * Return the type of the constant and if it's a cumul or not
   *
   * @param string $constant_name The name of the constant
   *
   * @return array
   */
  static function getConstantType($constant_name) {
    $type = 'line';
    $cumul = 0;

    if (
        isset(CConstantesMedicales::$list_constantes[$constant_name]['candles']) &&
        CConstantesMedicales::$list_constantes[$constant_name]['candles'] === true
    ) {
      $type = 'bandwidth';
    }
    elseif (isset(CConstantesMedicales::$list_constantes[$constant_name]['formula'])) {
      $type = 'formula';
      $cumul = 1;
    }
    elseif (isset(CConstantesMedicales::$list_constantes[$constant_name]['cumul_reset_config'])) {
      $type = 'cumul';
      $cumul = 1;
    }
    return array($type, $cumul);
  }

  /**
   * Get the configs for the given constant, and return an associative array of the configs
   *
   * @param string    $constant The name of the constant
   * @param CMbObject $host     The host
   *
   * @return array
   */
  static function getConfigFor($constant, $host) {
    $config = CConstantesMedicales::getHostConfig('selection', $host);
    $config = explode('|', $config[$constant]);

    $min = CConstantesMedicales::$list_constantes[$constant]['min'];
    $max = CConstantesMedicales::$list_constantes[$constant]['max'];
    $norm_min = 0;
    $norm_max = 0;

    $mode = 'fixed';
    if (strpos($min, '@') !== false) {
      $mode = 'float';
      $min = substr($min, strpos($min, '-') + 1);
      $max = substr($max, strpos($max, '+') + 1);
    }

    if (isset(CConstantesMedicales::$list_constantes[$constant]['norm_min'])) {
      $norm_min = CConstantesMedicales::$list_constantes[$constant]['norm_min'];
    }
    if (isset(CConstantesMedicales::$list_constantes[$constant]['norm_max'])) {
      $norm_max = CConstantesMedicales::$list_constantes[$constant]['norm_max'];
    }
    if (count($config) == 3) {
      $config = array_merge($config, array($mode, $min, $max, $norm_min, $norm_max));
    }
    elseif (count($config) == 8) {
      if (in_array($constant, array('poids', 'taille')) && $config[3] == 'fixed' && ($config[4] == '' || $config[5] == '')) {
        $config[3] = 'float';
        $config[4] = '';
        $config[5]=  '';
      }
      if ($config[4] == '') {
        if ($min < 0) {
          $min *= -1;
        }
        $config[4] = $min;
      }
      if ($config[5] == '') {
        $config[5] = $max;
      }
      if ($config[6] == '') {
        $config[6] = $norm_min;
      }
      if ($config[7] == '') {
        $config[7] = $norm_max;
      }
    }

    return array_combine(array('form', 'graph', 'color', 'mode', 'min', 'max', 'norm_min', 'norm_max'), $config);
  }

  /**
   * Return the graphs display mode config
   *
   * @param CMbObject|string $host Host from which we'll get the configuration
   *
   * @return array
   */
  static function getDisplayMode($host = null) {
    if ($host) {
      $config = CConstantesMedicales::getHostConfig('graphs_display_mode', $host);
    }
    else {
      $config = CConstantesMedicales::getConfig('graphs_display_mode');
    }
    $config = explode('|', $config);

    return array('mode' => $config[0], 'time' => $config[1]);
  }
}
