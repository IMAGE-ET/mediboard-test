{{* $Id$ *}}

{{*
  * @package Mediboard
  * @subpackage dPbloc
  * @version $Revision$
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

<h1>
	 {{mb_label class=COperation field=materiel}} 
	 du {{mb_value object=$filter field=_date_min}} 
	 au {{mb_value object=$filter field=_date_max}} 
</h1>

<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create('tabs-commande_mat', true);
});
</script>

<ul id="tabs-commande_mat" class="control_tabs">
  {{foreach from=$operations key=commande_mat item=_operations}}
  <li>
    {{assign var=op_count value=$_operations|@count}}
  	<a href="#commande_mat_{{$commande_mat}}" {{if !$op_count}}class="empty"{{/if}}>
			{{tr}}COperation.commande_mat.{{$commande_mat}}{{/tr}} 
      <small>({{$op_count}})</small>
		</a>
	</li>
  {{/foreach}}
</ul>

<hr class="control_tabs" />

{{foreach from=$operations key=commande_mat item=_operations}}
<table id="commande_mat_{{$commande_mat}}" style="display: none;" class="tbl">
  <tr>
	  <th>{{mb_title class=COperation field=date}}</th>
    <th>{{mb_label class=COperation field=chir_id}}</th>
    <th>{{mb_label class=CSejour field=patient_id}}</th>
	  <th>{{tr}}COperation{{/tr}}</th>
    <th>{{mb_label class=COperation field=cote}}</th>
    <th>{{mb_label class=CSejour field=patient_id}}</th>
	</tr>

	{{foreach from=$_operations item=_operation}}
	<tr>
	  <td style="text-align: center;">
		  {{mb_ditto name=date value=$_operation->_datetime|date_format:$dPconfig.date}}
			<br />{{mb_ditto name=weekday value=$_operation->_datetime|date_format:"%A"}}
		</td>
		
		{{assign var=chir value=$_operation->_ref_chir}}
	  <td><span class="mediuser" style="border-color: #{{$chir->_ref_function->color}};">{{$chir}}</span></td>
		
	  <td class="text">{{$_operation->_ref_sejour->_ref_patient}}</td>
	  <td class="text">
      {{if $_operation->libelle}}
      <strong>[{{$_operation->libelle}}]</strong>
      <br />
      {{/if}}
      {{foreach from=$_operation->_ext_codes_ccam item=_code}}
      {{$_code->code}} : <em>{{$_code->libelleLong}}</em><br />
      {{/foreach}}
    </td>
	
		<td>{{mb_value object=$_operation field=cote}}</td>
    <td>{{mb_value object=$_operation field=materiel}}</td>
	</tr>
	{{/foreach}}
</table>
{{/foreach}}
