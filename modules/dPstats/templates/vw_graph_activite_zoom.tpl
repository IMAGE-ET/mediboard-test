{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
var graphs = {{$graphs|@json}};
Main.add(function(){
  graphs.each(function(g, i){
    g.options.legend.container = $("legend-"+i);
    Flotr.draw($('graph-'+i), g.series, g.options);
  });
});
</script>

{{foreach from=$graphs item=graph key=key}}
<table class="layout">
  <tr>
    <td><div style="width: 600px; height: 400px; float: left; margin: 1em;" id="graph-{{$key}}"></div></td>
    <td style="vertical-align: top;" id="legend-{{$key}}"></td>
  </tr>
</table>
{{/foreach}}