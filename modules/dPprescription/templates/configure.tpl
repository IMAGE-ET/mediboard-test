{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create('tabs-configure', true);
});
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#CPrescription"              >{{tr}}CPrescription{{/tr}}              </a></li>
  <li><a href="#CCategoryPrescription"      >Chapitre                                </a></li>
  <li><a href="#CMomentUnitaire"            >{{tr}}CMomentUnitaire{{/tr}}            </a></li>
  <li><a href="#CPrescriptionLineMedicament">{{tr}}CPrescriptionLineMedicament{{/tr}}</a></li>
  <li><a href="#CPrescriptionLineHandler"   >{{tr}}CPrescriptionLineHandler{{/tr}}   </a></li>
  <li><a href="#imports">{{tr}}Imports/Exports{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="CPrescription" style="display: none;">
  {{mb_include template=CPrescription_configure}}
</div>
<div id="CCategoryPrescription" style="display: none;">
  {{mb_include template=CCategoryPrescription_configure}}
</div>
<div id="CMomentUnitaire" style="display: none;">
  {{mb_include template=CMomentUnitaire_configure}}
</div>
<div id="CPrescriptionLineMedicament" style="display: none;">
  {{mb_include template=CPrescriptionLineMedicament_configure}}
</div>
<div id="CPrescriptionLineHandler" style="display: none;">
  {{mb_include template=CPrescriptionLineHandler_configure}}
</div>
<div id="imports" style="display: none;">
  {{mb_include template=inc_imports_configure}}
</div>

