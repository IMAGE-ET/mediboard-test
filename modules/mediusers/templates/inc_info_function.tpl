{{* $Id: inc_edit_user.tpl 8378 2010-03-18 15:15:48Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 8378 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">     
Main.add(function () {
  InseeFields.initCPVille("editFct", "cp", "ville", "tel");
});
</script>

<form name="editFct" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this)">

<input type="hidden" name="dosql" value="do_functions_aed" />
<input type="hidden" name="m" value="mediusuers" />
<input type="hidden" name="function_id" value="{{$fonction->function_id}}" />
<input type="hidden" name="del" value="0" />
{{if !$fonction->canEdit()}}
  <input type="hidden" name="_locked" value="1" />
{{/if}}


<table class="form">
  <tr>
    <th class="title modify text" colspan="2">
      {{$fonction}}
    </th>
  </tr>
  <tr>
    <th>{{mb_label object=$fonction field="soustitre"}}</th>
    <td>{{mb_field object=$fonction field="soustitre"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$fonction field="adresse"}}</th>
    <td>{{mb_field object=$fonction field="adresse"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$fonction field="cp"}}</th>
    <td>{{mb_field object=$fonction field="cp"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$fonction field="ville"}}</th>
    <td>{{mb_field object=$fonction field="ville"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$fonction field="tel"}}</th>
    <td>{{mb_field object=$fonction field="tel"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$fonction field="fax"}}</th>
    <td>{{mb_field object=$fonction field="fax"}}</td>
  </tr>
  {{if $fonction->canEdit()}}
  <tr>
    <td colspan="2" class="button">
      <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
  {{/if}}
</table>

</form>