{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7951 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
WeekPlanning = {
  scroll  : function(guid, hour_min, hour_max) {
	  var div = $(guid);
    var trMin = div.down(".hour-"+hour_min);
    var trMax = div.down(".hour-"+hour_max);
    var posMin = trMin.offsetTop;
    var posMax = trMax.offsetTop;
    div.show();
		div.setStyle({ height: posMax-posMin +"px", visibility: "visible" });
    div.scrollTop = posMin;
	}
}
	
Main.add(function() {
  WeekPlanning.scroll('{{$planning->guid}}', '{{$planning->hour_min}}', '{{$planning->hour_max}}');
} )
</script>

<table class="tbl">
  <tr>
  	 <th class="title" colspan="10">{{$planning->title}}</th>
  </tr>
  <tr>
     <th style="width: 1%;"><div style="width: 2em;"></div></th>
  	 {{foreach from=$planning->days item=_day}}
     <th class="category" style="width: 10%;">{{$_day|date_format:"%a %d"|nl2br}}</th>
  	 {{/foreach}}
     <th style="width: 1%;"><div style="width: 9px;"></div></th>
  </tr>
</table>

<div id="{{$planning->guid}}" style="visibility: hidden; overflow-y: scroll; overflow-x: hidden;">
<table class="tbl">
    {{foreach from=$planning->hours item=_hour}}
    <tr class="hour-{{$_hour}}">
    {{if !$_hour}}
      <td colspan="9" style="background-color: #aaa; height: 4px;"/>
    {{else}}
      <th class="category" style="width: 1%;">
			  <div style="width: 2em;">
        {{$_hour}}h
				</div>
      </th>
      {{foreach from=$planning->days item=_day}}
      <td style="width: 10%; height: 20px; padding: 0;" class="segment-{{$_day}}-{{$_hour}}">
      </td>
      {{/foreach}}
    {{/if}}
    </tr>
    {{/foreach}}
</table>
	
</div>
