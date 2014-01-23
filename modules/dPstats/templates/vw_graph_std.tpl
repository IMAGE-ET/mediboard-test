{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  graph = {{$graph|@json}};

  Main.add(function(){
    graph.options.legend.container = $('display-legend-{{$type_graph}}');
    Flotr.draw($('display-graph-{{$type_graph}}'), graph.series, graph.options);
    {{if $can_zoom}}
      var select = DOM.select({},
        DOM.option({value: ""}, "&ndash; Vue sur un mois &ndash;")
      );

      graph.options.xaxis.ticks.each(function(tick){
        select.insert(DOM.option({value: tick[1]}, tick[1]));
      });

      select.observe("change", function(event){
        var url = Object.clone(DisplayGraph.lastUrl);
        url.addParam("type_graph", '{{$can_zoom}}');
        url.addParam("date_zoom", $V(Event.element(event)));
        url.requestModal();
      });

      $('display-graph-{{$type_graph}}').down('.flotr-tabs-group').insert(select);
    {{/if}}
  });
</script>


<table class="layout">
  <tr>
    <td style="vertical-align: top;"><div style="width: 600px; height: 400px; float: left; margin: 1em;" id="display-graph-{{$type_graph}}"></div></td>
    <td style="vertical-align: top;" id="display-legend-{{$type_graph}}"></td>
  </tr>
</table>