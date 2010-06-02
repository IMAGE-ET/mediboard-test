{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<hr />
<form name="group" action="?" method="post" onsubmit="return onSubmitFormAjax(this, { onComplete: refreshListElement.curry('','{{$category_id}}','',true) });">
  <input type="hidden" name="m" value="dPprescription" />
	<input type="hidden" name="dosql" value="do_executant_prescription_line_aed" />
  <input type="hidden" name="executant_prescription_line_id" value="{{$executant_prescription_line->_id}}" />
  <input type="hidden" name="category_prescription_id" value="{{$category_id}}" />
  <input type="hidden" name="del" value="0" />
	<input type="hidden" name="callback" value="refreshFormExecutant" />
 
  <table class="form">
    <tr>
      <th class="title {{if $executant_prescription_line->_id}}modify{{/if}}" colspan="2">
      {{if $executant_prescription_line->_id}}
        {{mb_include module=system template=inc_object_idsante400 object=$executant_prescription_line}}
        {{mb_include module=system template=inc_object_history object=$executant_prescription_line}}
        Modification de l'executant &lsquo;{{$executant_prescription_line->nom}}&rsquo;
      {{else}}
        Création d'un exécutant
      {{/if}}
      </th>
    </tr>
    <tr>
      <th>{{mb_label object=$executant_prescription_line field="nom"}}</th>
      <td>{{mb_field object=$executant_prescription_line field="nom"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$executant_prescription_line field="description"}}</th>
      <td>{{mb_field object=$executant_prescription_line field="description"}}</td>
    </tr>
    <tr>
      <td class="button" colspan="2">
      {{if $executant_prescription_line->_id}}
        <button class="modify" type="submit" name="modify">
          {{tr}}Save{{/tr}}
        </button>
        <button class="trash" type="button" name="delete" onclick="confirmDeletion(this.form,{ ajax:true, typeName:'l\'executant',objName:'{{$executant_prescription_line->nom|smarty:nodefaults|JSAttribute}}'})">
          {{tr}}Delete{{/tr}}
        </button>
      {{else}}
        <button class="new" type="submit" name="create">
          {{tr}}Create{{/tr}}
        </button>
      {{/if}}
      </td>
    </tr>
  </table>
</form> 