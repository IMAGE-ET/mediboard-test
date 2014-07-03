{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

  Main.add(Control.Tabs.create.curry('tabs-configure', true));

</script>

{{assign var="class" value="CPlageOp"}}

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#config-CPlageOp">{{tr}}{{$class}}{{/tr}}</a></li>
  <li><a href="#config-print_planning">{{tr}}mod-dPbloc-tab-print_planning{{/tr}}</a></li>
  <li><a href="#actions">{{tr}}Maintenance{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="config-CPlageOp" style="display: none;">
  {{mb_include template=CPlageOp_config}}
</div>

<div id="config-print_planning" style="display: none;">
  {{mb_include template=inc_config_print_planning}}
</div>

<div id="actions" style="display: none;">
  {{mb_include template=inc_config_actions}}
</div>