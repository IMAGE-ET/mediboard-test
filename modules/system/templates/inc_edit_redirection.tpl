{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_http_redirection_aed" />
<input type="hidden" name="http_redirection_id" value="{{$http_redirection->_id}}" />
<input type="hidden" name="del" value="0" />

<table class="form">

<tr>
  {{if $http_redirection->_id}}
  <th class="title modify text" colspan="2">
    {{mb_include module=system template=inc_object_idsante400 object=$http_redirection}}
    {{mb_include module=system template=inc_object_history object=$http_redirection}}

    {{tr}}CHttpRedirection-title-modify{{/tr}} '{{$http_redirection}}'
  {{else}}
  <th class="title" colspan="2">
    {{tr}}CHttpRedirection-title-create{{/tr}}
  {{/if}}
  </th>
</tr>

<tr>
  <th class="narrow">{{mb_label object=$http_redirection field="priority"}}</th>
  <td>{{mb_field object=$http_redirection field="priority"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$http_redirection field="from"}}</th>
  <td>{{mb_field object=$http_redirection field="from"}}</td>
</tr>

<tr>
  <th>{{mb_label object=$http_redirection field="to"}}</th>
  <td>{{mb_field object=$http_redirection field="to"}}</td>
</tr>

<tr>
  <td class="button" colspan="2">
    {{if $http_redirection->_id}}
    <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'http_redirection',objName:'{{$http_redirection->_view|smarty:nodefaults|JSAttribute}}'})">
      {{tr}}Delete{{/tr}}
    </button>
    {{else}}
    <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
    {{/if}}
  </td>
</tr>

</table>

<div class="big-info text">
  Pour indiquer que la redirection prend en charge toutes les adresses de provenance, indiquez * dans le champs de provenance.
</div>

</form>
