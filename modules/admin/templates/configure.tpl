{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(Control.Tabs.create.curry('tabs-configure', true));

refreshSourceLDAP = function(source_ldap_id) {
  var url = new Url("admin", "ajax_refresh_source_ldap");
  url.addParam("source_ldap_id", source_ldap_id);
  url.requestUpdate("CSourceLDAP");
}
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#config-permissions">{{tr}}config-permissions{{/tr}}</a></li>
  <li><a href="#CSourceLDAP">{{tr}}CSourceLDAP{{/tr}}</a></li>
  <li><a href="#actions">Actions</a></li>
</ul>

<hr class="control_tabs" />

<div id="config-permissions" style="display: none;">
  {{mb_include template=inc_config_permissions}}
</div>

<div id="CSourceLDAP" style="display: none;">
  {{mb_include template=inc_source_ldap}}
</div>

<div id="actions" style="display: none;">
  {{mb_include template=inc_config_actions}}
</div>