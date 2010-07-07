{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage webservices
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  Main.add(Control.Tabs.create.curry('tabs-configure', true));
</script>'

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#config-webservices">{{tr}}config-webservices{{/tr}}</a></li>
  <li><a href="#config-purge_echange">{{tr}}config-webservices-purge-echange{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="config-webservices" style="display: none;">
  {{mb_include template=inc_config_webservices}}
</div>

<div id="config-purge_echange" style="display: none;">
  {{mb_include template=inc_config_purge_echange}}
</div>