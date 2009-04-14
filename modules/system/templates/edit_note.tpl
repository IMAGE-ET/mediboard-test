{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
function reloadNotes(){
  window.opener.reloadNotes();
}
function submitNote(){
  var oForm = document.editFrm;
  
  if(checkForm(oForm)){
    submitFormAjax(oForm, 'systemMsg', {onComplete: function() {reloadNotes(); window.close()} });
    oForm.reset();
  }
}
</script>
<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="dosql" value="do_note_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="m" value="system" />
{{mb_field object=$note field="note_id" hidden=1 prop=""}}
{{mb_field object=$note field="object_id" hidden=1}}
{{mb_field object=$note field="object_class" hidden=1}}

<table class="form">
  <tr>
    {{if $note->_id}}
    <th class="title modify" colspan="2">
      Modifier une note &mdash; {{$note->_ref_object->_view}}
    </th>
    {{else}}
    <th class="title" colspan="2">
      Créer une note &mdash; {{$note->_ref_object->_view}}
    </th>
    {{/if}}
  </tr>
  <tr>
    <th>{{mb_label object=$note field="user_id"}}</th>
    <td>
      {{$note->_ref_user->_view}} &mdash; {{$note->_ref_user->_ref_function->_view}}
      {{mb_field object=$note field="user_id" hidden=1}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$note field="date"}}</th>
    <td>
      {{$note->date|date_format:"%A %d %B %Y à %Hh%M"}}
      {{mb_field object=$note field="date" hidden=1}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$note field="public"}}</th>
    <td>{{mb_field object=$note field="public"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$note field="degre"}}</th>
    <td>{{mb_field object=$note field="degre"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$note field="libelle"}}</th>
    <td>{{mb_field object=$note field="libelle"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$note field="text"}}</th>
    <td>{{mb_field object=$note field="text"}}</td>
  </tr>
  <tr>
    <td class="button" colspan="2">
      {{if $note->_id}}
      <button type="button" class="submit">Modifier</button>
      <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'la ',objName:'{{$note->_view|smarty:nodefaults|JSAttribute}}'})">
        Supprimer
      </button>
      {{else}}
      <button type="button" class="submit" onclick="submitNote()">Créer</button>
      <button type="button" class="cancel" onclick="window.close();">Fermer</button>
      {{/if}}
    </td>
  </tr>
</table>

</form>