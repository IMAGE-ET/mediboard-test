{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7951 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function() {

})
</script>

<table class="tbl">
  <tr>
  	 <th class="title" colspan="10">{{$planning->title}}</th>
  </tr>
  <tr>
     <th />
  	 {{foreach from=$planning->days item=_day}}
     <th class="category">{{$_day|date_format:"%a %d"}}</th>
  	 {{/foreach}}
  </tr>

  {{foreach from=$planning->hours item=_hour}}
	{{if !$_hour}}
	<tr>
		<td colspan="10" style="background-color: #666;"/>
	</tr>
	{{else}}
  {{foreach from=$planning->minutes item=_minute name=minutes}}
  <tr>
    {{if $smarty.foreach.minutes.first}} 
    <th rowspan="{{$planning->minutes|@count}}" class="category" style="width: 1%;">
      {{$_hour}}h
    </th>
    {{/if}}
    {{foreach from=$planning->days item=_day}}
    <td style="width: 40px; height: 4px; padding: 0;" class="segment-{{$_day}}-{{$_hour}}-{{$_minute}}">
    </td>
    {{/foreach}}
  </tr>
  {{/foreach}}
  {{/if}}
  {{/foreach}}
</table>
