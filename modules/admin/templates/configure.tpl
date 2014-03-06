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
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#config-permissions">{{tr}}config-permissions{{/tr}}</a></li>
  <li><a href="#config-ldap">{{tr}}config-ldap{{/tr}}</a></li>
  <li><a href="#actions">{{tr}}Maintenance{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="config-permissions" style="display: none;">
  {{mb_include template=inc_config_permissions}}
</div>

<div id="config-ldap" style="display: none;">
  {{mb_include template=inc_config_ldap}}
</div>

<div id="actions" style="display: none;">
  {{mb_include template=inc_config_actions}}
</div>