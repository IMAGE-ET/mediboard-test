{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editDetCCAM" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_detccam_aed" />
<input type="hidden" name="idacte" value="{{$detccam->_id}}" />
<input type="hidden" name="del" value="0" />

<table class="form">
  <tr>
    {{if $detccam->_id != ''}}
    <th class="title modify" colspan="2">
 		  Informations du détail CCAM {{$detccam->_view}}
    </th>
    {{else}}
    <th class="title" colspan="2">
			Informations du détail CCAM
    </th>
    {{/if}}
  </tr>
  {{if $detccam->_id != ''}}
  
  <tr>
		<th>{{mb_label object=$detccam field="numdos"}}</th>
		<td>{{mb_value object=$detccam field="numdos"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detccam field="malnum"}}</th>
		<td>{{mb_value object=$detccam field="malnum"}}</td>
  </tr>  
  
  {{* Ajouter champ date
  <tr>
		<th>{{mb_label object=$detccam field="date"}}</th>
		<td>{{mb_value object=$detccam field="date"}}</td>
  </tr>  
  *}}
  
  <tr>
		<th>{{mb_label object=$detccam field="codpra"}}</th>
		<td>{{mb_value object=$detccam field="codpra"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detccam field="codact"}}</th>
		<td>{{mb_value object=$detccam field="codact"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detccam field="activ"}}</th>
		<td>{{mb_value object=$detccam field="activ"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detccam field="phase"}}</th>
		<td>{{mb_value object=$detccam field="phase"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detccam field="modt1"}}</th>
		<td>{{mb_value object=$detccam field="modt1"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detccam field="modt2"}}</th>
		<td>{{mb_value object=$detccam field="modt2"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detccam field="modt3"}}</th>
		<td>{{mb_value object=$detccam field="modt3"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detccam field="modt4"}}</th>
		<td>{{mb_value object=$detccam field="modt4"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detccam field="assoc"}}</th>
		<td>{{mb_value object=$detccam field="assoc"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detccam field="dephon"}}</th>
		<td>{{mb_value object=$detccam field="dephon"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detccam field="datact"}}</th>
		<td>{{mb_value object=$detccam field="datact"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detccam field="extdoc"}}</th>
		<td>{{mb_value object=$detccam field="extdoc"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detccam field="rembex"}}</th>
		<td>{{mb_value object=$detccam field="rembex"}}</td>
  </tr>  
  
  <tr>
		<th>{{mb_label object=$detccam field="datmaj"}}</th>
		<td>{{mb_value object=$detccam field="datmaj"}}</td>
  </tr>  

  {{if $can->edit}}
  <tr>
    <td class="button" colspan="2">
      <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'entête CCAM',objName:'{{$detccam->_view|smarty:nodefaults|JSAttribute}}'})">
        {{tr}}Delete{{/tr}}</button>
    </td>
  </tr>
  {{/if}}     
</table>

</form>

{{assign var=id400 value=$detccam->_ref_id400}}
<table class="tbl">
  <tr>
    <th class="title" colspan="10">Correspondance pour l'établissement courant</th>
  </tr>
  
  <tr>
    <th>{{mb_label object=$id400 field=last_update}}</th>
    <th>{{mb_label object=$id400 field=object_id}}</th>
  </tr>

	{{assign var=acteccam value=$id400->_ref_object}}
  <tr>
	  {{if $id400->_id}}
    <td>{{mb_value object=$id400 field=last_update}}</td>
    <td>{{$acteccam->_view}}</td>
    {{else}}
    <td colspan="2"><em>Pas de correspondance</em></td>
    {{/if}}
  </tr>
</table>

{{/if}}
