{{*
 * $Id$
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_script module=hl7          script=test_hl7}}
{{mb_script module=dPhospi      script=affectation}}
{{mb_script module=dPpatients   script=patient}}
{{mb_script module=dPpatients   script="autocomplete"}}

<script>
  Main.add(function () {
    Control.Tabs.create('tabs-test_hl7v2', false);
  });
</script>

<ul id="tabs-test_hl7v2" class="control_tabs">
  <li><a href="#test_hl7v2_pam">{{tr}}CPAM{{/tr}}</a></li>
  <li><a href="#test_hl7v2_pdq">{{tr}}CPDQ{{/tr}}</a></li>
  <li><a href="#test_hl7v2_pix">{{tr}}CPIX{{/tr}}</a></li>
  <li><a href="#test_hl7v2_dec">{{tr}}CDEC{{/tr}}</a></li>
  <li><a href="#test_hl7v2_svs">{{tr}}CSVS{{/tr}}</a></li>
</ul>

<div id="test_hl7v2_pam" style="display: none">
  {{mb_include module=hl7 template=inc_vw_pam}}
</div>

<div id="test_hl7v2_pdq" style="display: none">
  {{mb_include module=hl7 template=inc_vw_pdq}}
</div>

<div id="test_hl7v2_pix" style="display: none">
  {{mb_include module=hl7 template=inc_vw_pix}}
</div>

<div id="test_hl7v2_dec" style="display: none">
  {{mb_include module=hl7 template=inc_vw_dec}}
</div>

<div id="test_hl7v2_svs" style="display: none">
  {{mb_include module=hl7 template=inc_vw_svs}}
</div>