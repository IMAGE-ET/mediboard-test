{{* $Id: configure.tpl 10842 2010-12-08 21:57:35Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 10842 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<td>
  <button class="{{if $mediuser->_id}}tick{{else}}new{{/if}}" {{if $user_ldap.associate}}disabled="disabled"{{/if}} 
    onclick="associateUserLDAP('{{$mediuser->_id}}', '{{$user_ldap.objectguid}}', '{{$user_ldap.user_username}}');">
    {{if !$mediuser->_id}}
      {{tr}}CUser_user-ldap-create-and-associate{{/tr}}
    {{else}}  
      {{tr}}CUser_user-ldap-associate{{/tr}}
    {{/if}}
  </button>
</td>
<td>
  {{if $user_ldap.associate}}
    <a href="?m=admin&amp;tab=view_edit_users&amp;user_id={{$user_ldap.associate}}">
      {{$user_ldap.user_username}}
    </a>
  {{else}}
    {{if $samaccountname == $user_ldap.user_username}}
      <strong>{{$user_ldap.user_username}}</strong>
    {{else}}
      {{$user_ldap.user_username}}
    {{/if}}
  {{/if}}
</td>  
<td>
  {{if $sn == $user_ldap.user_last_name}}
    <strong>{{$user_ldap.user_last_name}}</strong>
  {{else}}
    {{$user_ldap.user_last_name}}
  {{/if}}
</td>
<td>
  {{if $givenname == $user_ldap.user_first_name}}
    <strong>{{$user_ldap.user_first_name}}</strong>
  {{else}}
    {{$user_ldap.user_first_name}}
  {{/if}}
</td>
<td>{{if $user_ldap.actif}}Oui{{else}}Non{{/if}}</td>  