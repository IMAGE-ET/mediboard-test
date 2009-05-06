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
		Flotr.draw($('graph-'+i), g.series, g.options);
	});
});
</script>

{{foreach from=$graphs item=graph key=key}}
	<div style="width: 600px; height: 350px; float: left; margin: 1em;" id="graph-{{$key}}"></div>
{{/foreach}}