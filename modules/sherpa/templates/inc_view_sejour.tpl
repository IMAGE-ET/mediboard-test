{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editSejour" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_SpSejMed_aed" />
<input type="hidden" name="numdos" value="{{$sejour->_id}}" />
<input type="hidden" name="del" value="0" />

<table class="form">
  <tr>
    {{if $sejour->_id}}
    <th class="title modify" colspan="2">
 		  Affichage des informations du sejour {{$sejour->numdos}}
    </th>
    {{else}}
    <th class="title" colspan="2">
			Affichage des informations d'un séjour
    </th>
    {{/if}}
  </tr>
  {{if $sejour->_id}}
  
  <tr>
		<th>{{mb_label object=$sejour field="numdos"}}</th>
		<td>{{mb_value object=$sejour field="numdos"}}</td>
  </tr>
  
  <tr>
		<th>{{mb_label object=$sejour field="sejfla"}}</th>
		<td>{{mb_value object=$sejour field="sejfla"}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$sejour field="malnum"}}</th>
		<td>{{mb_value object=$sejour field="malnum"}}</td>
  </tr>
  
  <tr>
		<th>{{mb_label object=$sejour field="datent"}}</th>
		<td>{{mb_value object=$sejour field="datent"}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$sejour field="litcod"}}</th>
		<td>{{mb_value object=$sejour field="litcod"}}</td>
  </tr>
  
  <tr>
		<th>{{mb_label object=$sejour field="sercod"}}</th>
		<td>{{mb_value object=$sejour field="sercod"}}</td>
  </tr>
  
  <tr>
		<th>{{mb_label object=$sejour field="pracod"}}</th>
		<td>{{mb_value object=$sejour field="pracod"}}</td>
  </tr>
   
  <tr>
		<th>{{mb_label object=$sejour field="depart"}}</th>
		<td>{{mb_value object=$sejour field="depart"}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$sejour field="etades"}}</th>
		<td>{{mb_value object=$sejour field="etades"}}</td>
  </tr>

  <tr>
		<th>{{mb_label object=$sejour field="datsor"}}</th>
		<td>{{mb_value object=$sejour field="datsor"}}</td>
  </tr>
     
  {{if $can->edit}}
  <tr>
    <td class="button" colspan="2">
      <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le sejour',objName:'{{$sejour->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
    </td>
  </tr>
  {{/if}}     
</table>

</form>

{{assign var=id400 value=$sejour->_ref_id400}}
<table class="tbl">
  <tr>
    <th class="title" colspan="10">Correspondance pour l'établissement courant</th>
  </tr>
  <tr>
    <th>{{mb_label object=$id400 field=last_update}}</th>
    <th>{{mb_label object=$id400 field=object_id}}</th>
  </tr>

	{{assign var=sejour value=$id400->_ref_object}}
  <tr>
	  {{if $id400->_id}}
    <td>{{mb_value object=$id400 field=last_update}}</td>
    <td>
      <a href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$sejour->_id}}">
        {{$sejour->_view}}
      </a>
    </td>
    {{else}}
    <td colspan="2"><em>Pas de correspondance</em></td>
    {{/if}}
  </tr>
</table>

{{/if}}
