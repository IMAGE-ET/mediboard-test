{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(Control.Tabs.create.curry('tabs-configure', true));
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#RPU">{{tr}}config-dPurgences-rpu{{/tr}}</a></li>
  <li><a href="#Display">{{tr}}config-dPurgences-display{{/tr}}</a></li>
  <li><a href="#Sender">{{tr}}config-dPurgences-sender{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="RPU" style="display: none;">
  {{mb_include template=inc_config_rpu}}
</div>

<div id="Display" style="display: none;">
  {{mb_include template=inc_config_display}}
</div>

<div id="Sender" style="display: none;">
  {{mb_include template=inc_config_sender}}
</div>