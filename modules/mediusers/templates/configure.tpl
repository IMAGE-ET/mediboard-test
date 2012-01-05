{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage mediusers
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  Main.add(Control.Tabs.create.curry('tabs-configure', true));
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#config-mediusers">{{tr}}config-mediusers{{/tr}}</a></li>
  <li><a href="#config-maintenance">{{tr}}Maintenance{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="config-mediusers" style="display: none;">
  {{mb_include module=mediusers template=inc_config_mediusers}}
</div>

<div id="config-maintenance" style="display: none;">
  {{mb_include module=mediusers template=inc_config_maintenance}}
</div>