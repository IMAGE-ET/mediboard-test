{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editDossiers" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_SpUrgDos_aed" />
<input type="hidden" name="numdos" value="{{$dossier->_id}}" />
<input type="hidden" name="del" value="0" />

<table class="form">
  <tr>
    {{if $dossier->_id}}
    <th class="title modify" colspan="2">
 		  Affichage des informations du dossier {{$dossier->numdos}}
    </th>
    {{else}}
    <th class="title" colspan="2">
			Affichage des informations du dossier
    </th>
    {{/if}}
  </tr>
  {{if $dossier->_id}}
  
  <tr>
		<th>{{mb_label object=$dossier field="numdos"}}</th>
		<td>{{mb_value object=$dossier field="numdos"}}</td>
  </tr>
  
  <tr>
		<th>{{mb_label object=$dossier field="malnum"}}</th>
		<td>{{mb_value object=$dossier field="malnum"}}</td>
  </tr>
  
  <tr>
		<th>{{mb_label object=$dossier field="anndos"}}</th>
		<td>{{mb_value object=$dossier field="anndos"}}</td>
  </tr>
  
  
  
  {{if $can->edit}}
  <tr>
    <td class="button" colspan="2">
      <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le dossier',objName:'{{$dossier->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
    </td>
  </tr>
  {{/if}}     
</table>

</form>

{{assign var=id400 value=$dossier->_ref_id400}}
<table class="tbl">
  <tr>
    <th class="title" colspan="10">Correspondance pour l'établissement courant</th>
  </tr>
  <tr>
    <th>{{mb_label object=$id400 field=last_update}}</th>
    <th>{{mb_label object=$id400 field=object_id}}</th>
  </tr>

	{{assign var=dossier value=$id400->_ref_object}}
  <tr>
	  {{if $id400->_id}}
    <td>{{mb_value object=$id400 field=last_update}}</td>
    <td>{{$dossier->_view}}</td>
    {{else}}
    <td colspan="2"><em>Pas de correspondance</em></td>
    {{/if}}
  </tr>
  <tr>
</table>

{{/if}}
