{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !$dialog}}
  {{mb_include module=system template=filter_history}}
{{/if}}

{{if !$stats}}
  <table class="tbl">
    {{if $dialog && $object->_id}}
      <tr>
        <th colspan="7" class="title">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$object->_guid}}');">
          Historique de {{$object->_view}}
        </span>
        </th>
      </tr>
    {{/if}}

    <tr>
      {{if !$dialog}}
        <th>{{mb_title class=CUserLog field=object_class}}</th>
        <th>{{mb_title class=CUserLog field=object_id}}</th>
        <th>{{mb_title class=CUserLog field=ip_address}}</th>
      {{/if}}
      <th>{{mb_title class=CUserLog field=user_id}}</th>
      <th colspan="2">{{mb_title class=CUserLog field=date}}</th>
      <th>{{mb_title class=CUserLog field=type}}</th>
      <th>{{mb_title class=CUserLog field=fields}}</th>
      {{if $object->_id}}
        <th>{{tr}}CUserLog-values_before{{/tr}}</th>
        <th>{{tr}}CUserLog-values_after{{/tr}}</th>
      {{/if}}
    </tr>

    {{mb_include module=system template=inc_history_line logs=$list}}
  </table>
{{else}}
  <script type="text/javascript">
    var graphs = {{$graphs|@json}};
    var graphSizes = [
      {width: '400px', height: '250px', yaxisNoTicks: 5},
      {width: '700px', height: '500px', yaxisNoTicks: 10}
    ];

    function yAxisTickFormatter(val) {
      return Flotr.engineeringNotation(val, 2, 1000);
    }

    function mouseTrackFormatter(obj) {
      return obj.y+' , '+obj.series.data[obj.index][2];
    }

    function drawGraphs(size) {
      var container;
      size = size || graphSizes[0];

      $A(graphs).each(function(g, key) {
        container = $('graph-'+key);
        container.setStyle(size);

        g.options.y2axis.noTicks       = size.yaxisNoTicks;
        g.options.yaxis.noTicks        = size.yaxisNoTicks;
        g.options.yaxis.tickFormatter  = yAxisTickFormatter;
        g.options.y2axis.tickFormatter = yAxisTickFormatter;
        g.options.mouse.trackFormatter = mouseTrackFormatter;

        var f = Flotr.draw(container, g.series, g.options);
      });
    }

    Main.add(function () {
      drawGraphs(graphSizes[1]);
    });
  </script>

  <table class="main">
  <tr>
    <td colspan="2">
    {{foreach from=$graphs item=graph name=graphs}}
      <div id="graph-{{$smarty.foreach.graphs.index}}" style="width: 350px; height: 250px; float: left; margin: 1em;"></div>
    {{/foreach}}
    </td>
  </tr>
  </table>
{{/if}}
