{{*
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  Main.add(function() {
    var tabsActes = Control.Tabs.create('tab-actes', false);
  });
</script>

{{assign var="sejour" value=$consult->_ref_sejour}}
{{assign var="do_subject_aed" value="do_consultation_aed"}}
{{assign var="object" value=$consult}}

<ul id="tab-actes" class="control_tabs">
  {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
    <li><a href="#ccam">Actes CCAM</a></li>
    <li><a id="acc_consultations_a_actes_ngap" href="#ngap">Actes NGAP</a></li>
  {{/if}}
  {{if $sejour && $sejour->_id}}
    <li><a href="#cim">Diagnostics</a></li>
  {{/if}}
  {{if $conf.dPccam.CCodable.use_frais_divers.CConsultation && $conf.dPccam.CCodeCCAM.use_cotation_ccam}}
    <li><a href="#fraisdivers">Frais divers</a></li>
  {{/if}}
  {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed == "1"}}
    <li><a href="#tarmed_tab">Tarmed</a></li>
    <li><a href="#caisse_tab">Caisses</a></li>
  {{/if}}
</ul>

<div id="ccam" style="display: none;">
  {{assign var="module" value="dPcabinet"}}
  {{assign var="subject" value=$consult}}
  {{mb_include module=salleOp template=inc_codage_ccam}}
</div>

<div id="ngap" style="display: none;">
  <div id="listActesNGAP">
    {{assign var="_object_class" value="CConsultation"}}
    {{mb_include module=cabinet template=inc_codage_ngap object=$consult}}
  </div>
</div>

{{if $sejour && $sejour->_id}}
  <div id="cim" style="display: none;">
    {{assign var=sejour value=$consult->_ref_sejour}}
    {{mb_include module=salleOp template=inc_diagnostic_principal modeDAS="1"}}
  </div>
{{/if}}

{{if $conf.dPccam.CCodable.use_frais_divers.CConsultation && $conf.dPccam.CCodeCCAM.use_cotation_ccam}}
  <div id="fraisdivers" style="display: none;">
    {{mb_include module=ccam template=inc_frais_divers object=$consult}}
  </div>
{{/if}}

{{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
  <div id="tarmed_tab" style="display:none">
    <div id="listActesTarmed">
      {{mb_include module=tarmed template=inc_codage_tarmed }}
    </div>
  </div>
  <div id="caisse_tab" style="display:none">
    <div id="listActesCaisse">
      {{mb_include module=tarmed template=inc_codage_caisse}}
    </div>
  </div>
{{/if}}