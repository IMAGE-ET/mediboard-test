{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  Main.add(function() {
    var tabs = Control.Tabs.create('tabs-configure', true);
    if (tabs.activeLink.key == "CConfigEtab") {
      Configuration.edit('dPsalleOp', ['CGroups', 'CService CGroups.group_id'], $('CConfigEtab'));
    }
  });
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#config-CPlageOp">{{tr}}COperation{{/tr}}</a></li>
  <li><a href="#config-CActe">{{tr}}CActe{{/tr}}</a></li>
  <li><a href="#config-Diagnostics">{{tr}}Diagnostics{{/tr}}</a></li>
  <li onmousedown="Configuration.edit('dPsalleOp', 'CGroups', $('CConfigEtab'))">
    <a href="#CConfigEtab">Config par établissement</a>
  </li>
</ul>

<div id="config-CPlageOp" style="display: none;">
  {{mb_include template=config-COperation}}
</div>

<div id="config-CActe" style="display: none;">
  {{mb_include template=config-CActe class=CActeCCAM}}
</div>

<div id="config-Diagnostics" style="display: none;">
  {{mb_include template=config-Diagnostic class=CDossierMedical}}
</div>

<div id="CConfigEtab" style="display: none"></div>