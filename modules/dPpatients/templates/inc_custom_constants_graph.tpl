{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

{{mb_script module=patients script=constants_graph ajax=true}}

<style xmlns="http://www.w3.org/1999/html">
  .graph-legend {
    vertical-align: top;
    line-height: 1;
    padding-left: 1em !important;
    padding-top: 0.2em !important;
  }
</style>

<script type="text/javascript">
  Main.add(function () {
    var graphs_data = {{$graphs|@json}};
    window.oGraphs = new ConstantsGraph(graphs_data, {{$min_x_index}}, {{$min_x_value}}, true);
    window.oGraphs.draw();
  });
</script>

{{foreach from=$graphs item=_graph key=_id}}
  <div id="tab-{{$_id}}">
    <table class="layout">
      <tr>
        <td>
          <div id="placeholder_{{$_id}}" style="width: {{$_graph.width}}px; height: 175px; margin-bottom: 5px;"></div>
        </td>
        <td id="legend_{{$_id}}" class="graph-legend"></td>
      </tr>
    </table>
  </div>
{{/foreach}}