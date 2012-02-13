{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=configLDAP value=$conf.admin.LDAP.ldap_connection}}
{{if $configLDAP && $user->_ldap_linked}}
  {{assign var=readOnlyLDAP value=true}}
  <div class="small-warning">
    {{tr}}CUser_associate-ldap{{/tr}}
  </div>
{{else}}
  {{assign var=readOnlyLDAP value=null}}
{{/if}}

<form name="Edit-{{$user->_class}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_user_aed" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="user_id" value="{{$user->_id}}" />

<table class="form">
  <tr>
    {{if $user->_id}}
    <th class="title modify" colspan="2">
      {{assign var=object value=$user}}
      {{mb_include module=system template=inc_object_idsante400}}
      {{mb_include module=system template=inc_object_history}}
      {{mb_include module=system template=inc_object_notes}}
      Utilisateur '{{$user}}'
    {{else}}
    <th class="title" colspan="2">
      {{tr}}CUser-title-create{{/tr}}
    {{/if}}
    </th>
  </tr>
</table>

<script type="text/javascript">
Main.add(function() {
  Control.Tabs.create('tabs-user');
  Control.Tabs.setTabCount("profiled_users", "{{$user->_ref_profiled_users|@count}}");
});
</script>

<ul id="tabs-user" class="control_tabs">
  <li><a href="#identity">{{tr}}CUser-identity{{/tr}}</a></li>
  <li>
    <a href="#profiled_users">
      {{tr}}CUser-back-profiled_users{{/tr}}
      <small>(&ndash;)</small>
    </a>
  </li>
</ul>

<hr class="control_tabs" />

<div id="identity" style="display: none;">
{{mb_include template=inc_form_user}}
</div>

<div id="profiled_users" style="display: none;">
{{mb_include template=inc_profiled_users}}
</div>

</form>