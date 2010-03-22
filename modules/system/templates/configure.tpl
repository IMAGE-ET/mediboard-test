{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function(){
  Control.Tabs.create("config-tabs", true);
});
</script>

<ul class="control_tabs" id="config-tabs">
  <li><a href="#ui">{{tr}}config-ui{{/tr}}</a></li>
  <li><a href="#formats">{{tr}}config-formats{{/tr}}</a></li>
  <li><a href="#system">{{tr}}config-system{{/tr}}</a></li>
  <li><a href="#php-config">PHP</a></li>
</ul>

<hr class="control_tabs" />

{{assign var=m value=""}}

<div id="ui" style="display: none;">
{{mb_include template=inc_config_ui}}
</div>

<div id="formats" style="display: none;">
{{mb_include template=inc_config_formats}}
</div>

<div id="system" style="display: none;">
{{mb_include template=inc_config_system}}
</div>

<div id="php-config" style="display: none;">
{{mb_include template=inc_config_php}}
</div>