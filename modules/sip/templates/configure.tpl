{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(Control.Tabs.create.curry('tabs-configure', true));
</script>'

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#SIP">{{tr}}SIP{{/tr}}</a></li>
  <li><a href="#Export">{{tr}}Export{{/tr}}</a></li>
	<li><a href="#Repair">{{tr}}Repair{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="SIP" style="display: none;">
{{mb_include template=inc_config_sip}}
</div>

<div id="Export" style="display: none;">
{{mb_include template=inc_config_export}}
</div>

<div id="Repair" style="display: none;">
{{mb_include template=inc_config_repair}}
</div>