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

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#config-CPlageOp">{{tr}}COperation{{/tr}}</a></li>
  <li><a href="#config-CActe">{{tr}}CActe{{/tr}}</a></li>
  <li><a href="#config-Diagnostics">{{tr}}Diagnostics{{/tr}}</a></li>
  <li><a href="#config-CDailyCheckList">{{tr}}CDailyCheckList{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="config-CPlageOp" style="display: none;">
  {{mb_include template=config-COperation}}
</div>

<div id="config-CActe" style="display: none;">
  {{mb_include template=config-CActe class=CActeCCAM}}
</div>

<div id="config-Diagnostics" style="display: none;">
  {{mb_include template=config-Diagnostic class=CDossierMedical}}
</div>

<div id="config-CDailyCheckList" style="display: none;">
  {{mb_include template=config-CDailyCheckList class=CDailyCheckList}}
</div>