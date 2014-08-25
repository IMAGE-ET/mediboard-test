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
{{mb_script module=dPpatients   script=patient}}
{{mb_script module=dPpatients   script="autocomplete"}}

<script>
  Main.add(function () {
    Control.Tabs.create('tabs-test_hl7v2', false);
  });
</script>

<ul id="tabs-test_hl7v2" class="control_tabs">
  <li><a href="#test_hl7v2_pam">PAM</a></li>
  <li><a href="#test_hl7v2_pdq">PDQ</a></li>
  <li><a href="#test_hl7v2_dec">DEC</a></li>
</ul>

<div id="test_hl7v2_pam" style="display: none">
  {{mb_include module=hl7 template=inc_vw_pam}}
</div>
<div id="test_hl7v2_pdq" style="display: none">
  {{mb_include module=hl7 template=inc_vw_pdq}}
</div>
<div id="test_hl7v2_dec" style="display: none">
  {{mb_include module=hl7 template=inc_vw_dec}}
</div>