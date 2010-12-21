{{* $Id: $ *}}

{{*
  * @package Mediboard
  * @subpackage admin
  * @version $Revision: $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

<form name="editUser" action="?m={{$m}}&amp;a=edit_infos" method="post" onsubmit="return onSubmitFormAjax(this);">

<input type="hidden" name="dosql" value="do_mediusers_aed" />
<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="user_id" value="{{$user->user_id}}" />
<input type="hidden" name="del" value="0" />

<table class="form">
  <tr>
    <th class="title" colspan="2">
      {{$user->_view}}
    </th>
  </tr>
  <tr>
    <th>{{mb_label object=$user field="_user_last_name"}}</th>
    <td>{{mb_field object=$user field="_user_last_name"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$user field="_user_first_name"}}</th>
    <td>{{mb_field object=$user field="_user_first_name"}}</td>
  </tr>
  
  <tbody {{if ($user->_user_type != 3) && ($user->_user_type != 4) && ($user->_user_type != 13)}}style="display:none"{{/if}}>
  
    {{include file="inc_infos_praticien.tpl" object=$user}}     
    
  </tbody>
          
  <tr>
    <th>{{mb_label object=$user field="_user_email"}}</th>
    <td>{{mb_field object=$user field="_user_email"}}</td>
  </tr>
  <tr>
    <th>{{mb_label object=$user field="_user_phone"}}</th>
    <td>{{mb_field object=$user field="_user_phone"}}</td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <button type="submit" class="modify">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>

</form>

