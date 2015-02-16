{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

<script type="text/javascript">
  graph = {{$graph|@json}};

  Main.add(function(){
    graph.options.legend.container = $('display-legend');
    if (graph.options.mouse) {
      graph.options.mouse.trackFormatter = eval(graph.options.mouse.trackFormatter);
    }
    Flotr.draw($('display-graph'), graph.series, graph.options);
  });
</script>


<table class="layout">
  <tr>
    <td style="vertical-align: top;"><div style="width: 800px; height: 500px; float: left; margin: 1em;" id="display-graph"></div></td>
    <td style="vertical-align: top;" id="display-legend"></td>
  </tr>
</table>