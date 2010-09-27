{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function() {
  var count = {{$sejours|@count}};
  var link = $('tabs-sejours').down('a[href=#board-sejours-{{$mode}}]');
  link.down('small').update('('+count+')');
  link.setClassName('empty', count == 0);
})	
</script>
<table class="tbl">
  <tr>
    <th colspan="2">
      {{mb_title class=CSejour field=patient_id}} /
      {{mb_title class=CPatient field=_age}}
		</th>
    <th>{{mb_title class=CSejour field=libelle}}</th>
    <th style="width: 1%;">{{mb_title class=CSejour field=entree}}</th>
    <th style="width: 1%;">{{mb_title class=CSejour field=sortie}}</th>
    <th style="width: 1%;" colspan="2"><label title="Evenements planifiés par le rééducateur (cette semaine - pendant tout le séjour)">Evt.</label></th>
  </tr>
	
{{foreach from=$sejours item=_sejour}}
{{assign var=patient value=$_sejour->_ref_patient}}
  <tr {{if $_sejour->_count_evenements_ssr_week}} style="font-weight: bold;" {{/if}}>
    <td class="text">
      {{if $_sejour->_ref_prescription_sejour->_count_recent_modif_presc}}
      <img style="float: right" src="images/icons/ampoule.png" title="Prescription recemment modifiée"/>
      {{/if}}
    	<a href="?m=ssr&amp;tab=vw_aed_sejour_ssr&amp;sejour_id={{$_sejour->_id}}#planification">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')">
          {{mb_value object=$_sejour field=patient_id}}
        </span>
    	</a>
		</td>
		<td  style="width: 1%;">
			{{mb_value object=$patient field=_age}}    
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
