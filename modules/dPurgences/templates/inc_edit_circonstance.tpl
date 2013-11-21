{{*
 * $Id$
 *  
 * @category dPurgences
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<form name="editCirc" method="post" onsubmit="return checkForm(this);">
  {{mb_class object=$circonstance}}
  {{mb_key object=$circonstance}}
  <input type="hidden" name="del" value="0" />
  <table class="form">
    <tr>
      {{if $circonstance->_id}}
      <th class="title modify text" colspan="2">
        {{mb_include module=system template=inc_object_idsante400 object=$circonstance}}
        {{mb_include module=system template=inc_object_history object=$circonstance}}

        {{tr}}{{$circonstance->_class}}-title-modify{{/tr}} '{{$circonstance}}'
        {{else}}
      <th class="title" colspan="2">
        {{tr}}{{$circonstance->_class}}-title-create{{/tr}}
        {{/if}}
      </th>
    </tr>
    <tr>
      <th>{{mb_label object=$circonstance field="code"}}</th>
      <td>{{mb_field object=$circonstance field="code"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$circonstance field="libelle"}}</th>
      <td>{{mb_field object=$circonstance field="libelle"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$circonstance field="commentaire"}}</th>
      <td>{{mb_field object=$circonstance field="commentaire"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$circonstance field="actif"}}</th>
      <td>{{mb_field object=$circonstance field="actif"}}</td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        {{if $circonstance->_id}}
          <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>
          <button class="trash" type="button"
                  onclick="confirmDeletion(this.form, {typeName:$T('CCirconstance'),
                    objName:'{{$circonstance->_view|smarty:nodefaults|JSAttribute}}'})">{{tr}}Delete{{/tr}}</button>
        {{else}}
          <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>