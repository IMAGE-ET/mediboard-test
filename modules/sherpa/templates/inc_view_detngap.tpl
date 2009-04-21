{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editActeNGAP" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_ngap_aed" />
<input type="hidden" name="idacte" value="{{$detngap->_id}}" />
<input type="hidden" name="del" value="0" />

<table class="form">
  <tr>
    {{if $detngap->_id != ''}}
    <th class="title modify" colspan="2">
 		  Informations du détail CCAM {{$detngap->_view}}
    </th>
    {{else}}
    <th class="title" colspan="2">
			Informations du détail CCAM
    </th>
    {{/if}}
  </tr>
  {{if $detngap->_id != ''}}
  
  <tr>
		<th>{{mb_label object=$detngap field="numdos"}}</th>
		<td>{{mb_value object=$detngap field="numdos"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detngap field="malnum"}}</th>
		<td>{{mb_value object=$detngap field="malnum"}}</td>
  </tr>  
  
  {{* Ajouter champ date
  <tr>
		<th>{{mb_label object=$detngap field="date"}}</th>
		<td>{{mb_value object=$detngap field="date"}}</td>
  </tr>  
  *}}
  
  <tr>
		<th>{{mb_label object=$detngap field="pracod"}}</th>
		<td>{{mb_value object=$detngap field="pracod"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detngap field="datact"}}</th>
		<td>{{mb_value object=$detngap field="datact"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detngap field="codact"}}</th>
		<td>{{mb_value object=$detngap field="codact"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detngap field="actqte"}}</th>
		<td>{{mb_value object=$detngap field="actqte"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detngap field="quant"}}</th>
		<td>{{mb_value object=$detngap field="quant"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detngap field="coeff"}}</th>
		<td>{{mb_value object=$detngap field="coeff"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detngap field="depass"}}</th>
		<td>{{mb_value object=$detngap field="depass"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detngap field="valdep"}}</th>
		<td>{{mb_value object=$detngap field="valdep"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detngap field="nuit"}}</th>
		<td>{{mb_value object=$detngap field="nuit"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detngap field="ferie"}}</th>
		<td>{{mb_value object=$detngap field="ferie"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detngap field="gratuit"}}</th>
		<td>{{mb_value object=$detngap field="gratuit"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detngap field="flag"}}</th>
		<td>{{mb_value object=$detngap field="flag"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detngap field="datmaj"}}</th>
		<td>{{mb_value object=$detngap field="datmaj"}}</td>
  </tr>  
  
  {{if $can->edit}}
  <tr>
    <td class="button" colspan="2">
      <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le détail NGPAP,objName:'{{$detngap->_view|smarty:nodefaults|JSAttribute}}'})">
        {{tr}}Delete{{/tr}}</button>
    </td>
  </tr>
  {{/if}}     
</table>

</form>

{{assign var=id400 value=$detngap->_ref_id400}}
<table class="tbl">
  <tr>
    <th class="title" colspan="10">Correspondance pour l'établissement courant</th>
  </tr>
  
  <tr>
    <th>{{mb_label object=$id400 field=last_update}}</th>
    <th>{{mb_label object=$id400 field=object_id}}</th>
  </tr>

	{{assign var=actengap value=$id400->_ref_object}}
  <tr>
	  {{if $id400->_id}}
    <td>{{mb_value object=$id400 field=last_update}}</td>
    <td>{{$actengap->_view}}</td>
    {{else}}
    <td colspan="2"><em>Pas de correspondance</em></td>
    {{/if}}
  </tr>
</table>

{{/if}}
