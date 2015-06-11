{{* $Id: $ *}}

{{*
  * @package Mediboard
  * @subpackage admin
  * @version $Revision: $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

{{assign var=configLDAP value=$conf.admin.LDAP.ldap_connection}}
{{if $configLDAP && $user->_ref_user->_ldap_linked}}
  {{assign var=readOnlyLDAP value=true}}
{{else}}
  {{assign var=readOnlyLDAP value=null}}
{{/if}}

<form name="editUser" action="?m={{$m}}&amp;a=edit_infos" method="post" onsubmit="return onSubmitFormAjax(this);">

<input type="hidden" name="dosql" value="do_mediusers_aed" />
<input type="hidden" name="m" value="mediusers" />
<input type="hidden" name="user_id" value="{{$user->user_id}}" />
<input type="hidden" name="del" value="0" />

<table class="form">
  <tr>
    <th class="title modify text" colspan="2">
      {{$user}}
    </th>
  </tr>
  <tr>
    <th>{{mb_label object=$user field="_user_last_name"}}</th>
    <td>
      {{if !$readOnlyLDAP}}
        {{mb_field object=$user field="_user_last_name"}}
      {{else}}
        {{mb_value object=$user field="_user_last_name"}}
        {{mb_field object=$user field="_user_last_name" hidden=true}}
      {{/if}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$user field="_user_first_name"}}</th>
    <td>
      {{if !$readOnlyLDAP}}
        {{mb_field object=$user field="_user_first_name"}}
      {{else}}
        {{mb_value object=$user field="_user_first_name"}}
        {{mb_field object=$user field="_user_first_name" hidden=true}}
      {{/if}}
    </td>
  </tr>

  <tbody {{if ($user->_user_type != 3) && ($user->_user_type != 4) && ($user->_user_type != 13)}}style="display:none"{{/if}}>

    {{mb_include module=mediusers template="inc_infos_praticien" object=$user name_form="editUser"}}

  </tbody>

  <tr>
    <th>{{mb_label object=$user field="_user_email"}}</th>
    <td>
      {{if !$readOnlyLDAP}}
        {{mb_field object=$user field="_user_email"}}
      {{else}}
        {{mb_value object=$user field="_user_email"}}
        {{mb_field object=$user field="_user_email" hidden=true}}
      {{/if}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$user field="_user_phone"}}</th>
    <td>
      {{if !$readOnlyLDAP}}
        {{mb_field object=$user field="_user_phone"}}
      {{else}}
        {{mb_value object=$user field="_user_phone"}}
        {{mb_field object=$user field="_user_phone" hidden=true}}
      {{/if}}
    </td>
  </tr>
  <tr>
    <th>{{mb_label object=$user field="_user_astreinte"}}</th>
    <td>
      {{if !$readOnlyLDAP}}
        {{mb_field object=$user field="_user_astreinte"}}
      {{else}}
        {{mb_value object=$user field="_user_astreinte"}}
        {{mb_field object=$user field="_user_astreinte" hidden=true}}
      {{/if}}
    </td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>

</form>

