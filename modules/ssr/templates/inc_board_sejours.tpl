{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create('tabs-sejours', true);
});
</script>

<ul id="tabs-sejours" class="control_tabs">
	{{foreach from=$sejours key=key item=_sejours}}
  <li>
  	{{assign var=count_sejours value=$_sejours|@count}}
  	<a {{if !$count_sejours}} class="empty" {{/if}} href="#board-sejours-{{$key}}">
  		{{tr}}ssr-board-sejours-{{$key}}{{/tr}}
			{{if $count_sejours}}
			<small>({{$count_sejours}})</small>
			{{/if}}
		</a>
	</li>
	{{/foreach}}
</ul>

<hr class="control_tabs" />


<label>
  <input name="hide_noevents" type="checkbox" {{if $hide_noevents}} checked="true" {{/if}} onclick="updateBoardSejours(this.checked)" />
  Masquer les séjours sans planification cette semaine
</label>

{{foreach from=$sejours key=key item=_sejours}}
<div style="display: none;" id="board-sejours-{{$key}}">
<table class="tbl">
  <tr>
    <th>{{mb_title class=CSejour field=patient_id}}</th>
    <th>{{mb_title class=CSejour field=libelle}}</th>
    <th style="width: 1%;">{{mb_title class=CSejour field=entree}}</th>
    <th style="width: 1%;">{{mb_title class=CSejour field=sortie}}</th>
    <th style="width: 1%;" colspan="2"><label title="Evenements planifiés par le rééducateur (cette semaine - pendant tout le séjour)">Evt.</label></th>
  </tr>
	
{{foreach from=$_sejours item=_sejour}}
{{assign var=patient value=$_sejour->_ref_patient}}
  <tr {{if $_sejour->_count_evenements_ssr_week}} style="font-weight: bold;" {{/if}}>
    <td class="text">
    	<a href="?m=ssr&amp;tab=vw_aed_sejour_ssr&amp;sejour_id={{$_sejour->_id}}#planification">
        {{mb_value object=$_sejour field=patient_id}}
				({{mb_value object=$patient field=_age}})
    	</a>
		</td>
    <td class="text">{{mb_value object=$_sejour field=libelle}}</td>
    <td>{{mb_value object=$_sejour field=entree format=$dPconfig.date}}</td>
    <td>{{mb_value object=$_sejour field=sortie format=$dPconfig.date}}</td>

    <td style="text-align: right;">
		  {{assign var=count_evenements value=$_sejour->_count_evenements_ssr_week}}
      {{$count_evenements|ternary:$count_evenements:"-"}}
    </td>

    <td style="text-align: right;">
      {{assign var=count_evenements value=$_sejour->_count_evenements_ssr}}
      {{$count_evenements|ternary:$count_evenements:"-"}}
    </td>
  </tr>
{{foreachelse}}
<tr>
	<td colspan="10">
		<em>{{tr}}CSejour.none{{/tr}}</em>
	</td>
</tr>
{{/foreach}}
</table>
</div>
{{/foreach}}
