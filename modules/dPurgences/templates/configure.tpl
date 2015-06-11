{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  Main.add(function () {
    var tabs = Control.Tabs.create('tabs-configure', true);
    if (tabs.activeLink.key == "CConfigEtab") {
      Configuration.edit('dPurgences', 'CGroups', $('CConfigEtab'));
    }
  });
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#RPU">{{tr}}config-dPurgences-rpu{{/tr}}</a></li>
  <li><a href="#Display">{{tr}}config-dPurgences-display{{/tr}}</a></li>
  <li><a href="#Sender">{{tr}}config-dPurgences-sender{{/tr}}</a></li>
  <li><a href="#Offline">{{tr}}config-dPurgences-offline{{/tr}}</a></li>
  <li onmousedown="Configuration.edit('dPurgences', 'CGroups', $('CConfigEtab'))">
    <a href="#CConfigEtab">{{tr}}CConfigEtab{{/tr}}</a>
  </li>
  <li><a href="#Maintenance">{{tr}}config-dPurgences-maintenance{{/tr}}</a></li>
</ul>
<hr class="control_tabs" />

<div id="RPU" style="display: none;">
  {{mb_include template=inc_config_rpu}}
</div>

<div id="Display" style="display: none;">
  {{mb_include template=inc_config_display}}
</div>

<div id="Offline" style="display: none;">
  {{mb_include template=inc_config_offline}}
</div>

<div id="Sender" style="display: none;">
  {{mb_include template=inc_config_sender}}
</div>

<div id="CConfigEtab" style="display: none"></div>

<div id="Maintenance" style="display: none;">
  {{mb_include template=inc_config_maintenance}}
</div>
