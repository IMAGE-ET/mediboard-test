{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
  Main.add(Control.Tabs.create.curry('tabs-configure', true));
</script>'

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#config-extract">{{tr}}config-hprimxml-extract{{/tr}}</a></li>
  <li><a href="#config-schema">{{tr}}config-hprimxml-schema{{/tr}}</a></li>
	<li><a href="#config-treatment">{{tr}}config-hprimxml-treatment{{/tr}}</a></li>
	<li><a href="#config-optimize">{{tr}}config-hprimxml-optimization-echange{{/tr}}</a></li>
	<li><a href="#config-purge_echange">{{tr}}config-hprimxml-purge-echange{{/tr}}</a></li>
	<li><a href="#config-file-to-echange">{{tr}}config-hprimxml-file-to-echange{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="config-extract" style="display: none;">
  {{mb_include template=inc_config_extract_schema}}
</div>

<div id="config-schema" style="display: none;">
  {{mb_include template=inc_config_schema}}
</div>

<div id="config-treatment" style="display: none;">
  {{mb_include template=inc_config_treatment}}
</div>

<div id="config-optimize" style="display: none;">
  {{mb_include template=inc_config_optimize_echange}}
</div>

<div id="config-purge_echange" style="display: none;">
  {{mb_include template=inc_config_purge_echange}}
</div>

<div id="config-file-to-echange" style="display: none;">
  {{mb_include template=inc_config_file_to_echange}}
</div>