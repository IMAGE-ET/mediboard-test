{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  graphs = {{$graphs|@json}};
  graphSizes = [
    {width: '400px', height: '250px', yaxisNoTicks: 5},
    {width: '700px', height: '500px', yaxisNoTicks: 10}
  ];

  yAxisTickFormatter = function(val) {
    return Flotr.engineeringNotation(val, 2, 1000);
  };

  drawGraphs = function(size) {
    var container;
    size = size || graphSizes[0];
    $A(graphs).each(function(g, key) {
      container = $('graph-'+key);
      container.setStyle(size);
      g.options.y2axis.noTicks = size.yaxisNoTicks;
      g.options.yaxis.noTicks = size.yaxisNoTicks;
      g.options.yaxis.tickFormatter  = yAxisTickFormatter;
      g.options.y2axis.tickFormatter = yAxisTickFormatter;
      g.options.mouse                = {
        track: true,
        position: "ne",
        relative: true,
        sensibility: 2,
        trackDecimals: 3,
        trackFormatter: function (obj) {
          return obj.series.label + "<br />Valeur : " + obj.y + "<br />Date : " + g.datetime_by_index[obj.index];
        }
      };
      var f = Flotr.draw(container, g.series, g.options);

      {{if $groupmod==1}}
      f.overlay.setStyle({cursor: 'pointer'})
        .observe('click', function(m){return function(){$V(getForm('typevue').groupmod, m)}}(g.module));
      {{/if}}
    });
  }
</script>

<script>
  Main.add(function() {
    drawGraphs({{if $groupmod == 2}}graphSizes[1]{{/if}});
  });
</script>

{{foreach from=$graphs item=graph name=graphs}}
  <div id="graph-{{$smarty.foreach.graphs.index}}" style="width: 350px; height: 250px; float: left; margin: 1em;"></div>
{{/foreach}}

<!-- For styles purpose -->
<div style="clear: both;"></div>