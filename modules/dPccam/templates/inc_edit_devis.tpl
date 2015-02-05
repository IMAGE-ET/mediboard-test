{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage ccam
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

{{mb_ternary var=view test=$devis->_id value=$devis->_id other="new"}}

{{if $devis->date}}
  <script type="text/javascript">
    Main.add(function() {
      var tabsActes = Control.Tabs.create('tab-actes', false);
    });
  </script>
{{/if}}

<table class="form">
  <tr>
    <th colspan="2" class="title">{{tr}}CDevisCodage{{/tr}} pour {{$devis->_ref_patient}}</th>
  </tr>
  <tr>
    <td class="halfPane">
      <fieldset>
        <legend>Informations sur le devis</legend>
        <form name="editDevis-{{$view}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: function() {
          DevisCodage.refresh('{{$devis->_id}}');
          }});">
          {{mb_class object=$devis}}
          {{mb_key object=$devis}}
          <input type="hidden" name="del" value="0"/>

          {{mb_field object=$devis field=codable_class hidden=true}}
          {{mb_field object=$devis field=codable_id hidden=true}}
          {{mb_field object=$devis field=patient_id hidden=true}}
          {{mb_field object=$devis field=praticien_id hidden=true}}
          {{mb_field object=$devis field=creation_date hidden=true}}
          {{mb_field object=$devis field=base hidden=true}}
          {{mb_field object=$devis field=dh hidden=true}}
          {{mb_field object=$devis field=ht hidden=true}}
          {{mb_field object=$devis field=tax_rate hidden=true}}

          <table class="layout main">
            <tr>
              <th>
                {{mb_label object=$devis field=date class=notNull}}
              </th>
              <td>
                {{mb_field object=$devis field=date form="editDevis-$view" class=notNull register=true}}
              </td>
              <th>
                {{mb_label object=$devis field=libelle class=notNull}}
              </th>
              <td>
                {{mb_field object=$devis field=libelle class=notNull}}
              </td>
            </tr>
            <tr>
              <th>
                {{mb_label object=$devis field=event_type class=notNull}}
              </th>
              <td>
                {{mb_field object=$devis field=event_type class=notNull}}
              </td>
              <th>
                {{mb_label object=$devis field=patient_id}}
              </th>
              <td>
                {{$devis->_ref_patient}}
              </td>
            </tr>
            <tr>
              <td colspan="4">
                {{mb_label object=$devis field=comment}}
              </td>
            </tr>
            <tr>
              <td colspan="4">
                {{mb_field object=$devis field=comment}}
              </td>
            </tr>
            <tr>
              <td colspan="4" class="button">
                <button class="save" type="submit">{{tr}}Save{{/tr}}</button>
                <button class="print" type="button" onclick="DevisCodage.print('{{$devis->_id}}')">{{tr}}Print{{/tr}}</button>
                <button class="trash" type="button" onclick="$V(this.form.del, 1); return onSubmitFormAjax(this.form, {onComplete: function() {
                  this.form, window.parent.Control.Modal.close();}});" {{if $devis->_count_actes != 0}} disabled="disabled"{{/if}}>
                  {{tr}}Delete{{/tr}}
                </button>
              </td>
            </tr>
          </table>
        </form>
      </fieldset>
    </td>
    <td>
      <fieldset>
        <legend>Récapitulatif</legend>
        <form name="editDevisRecap" action="?" method="post" onsubmit="return false;">
          <table class="layout main">
            <tr>
              <th>
                {{mb_label object=$devis field=base}}
              </th>
              <td>
                {{mb_field object=$devis field=base readonly=readonly}}
              </td>
              <td colspan="2"></td>
            </tr>
            <tr>
              <th>
                {{mb_label object=$devis field=dh}}
              </th>
              <td>
                {{mb_field object=$devis field=dh readonly=readonly}}
              </td>
              <td colspan="2"></td>
            </tr>
            <tr>
              <th>
                {{mb_label object=$devis field=ht}}
              </th>
              <td>
                {{mb_field object=$devis field=ht readonly=readonly}}
              </td>
              <th>
                {{mb_label object=$devis field=tax_rate}}
              </th>
              <td>
                {{assign var=taux_tva value="|"|explode:$conf.dPcabinet.CConsultation.default_taux_tva}}
                <select name="tax_rate" onchange="DevisCodage.syncField(this, '{{$view}}');">
                  {{foreach from=$taux_tva item=taux}}
                    <option value="{{$taux}}" {{if $devis->tax_rate == $taux}}selected="selected"{{/if}}>{{tr}}CConsultation.taux_tva.{{$taux}}{{/tr}}</option>
                  {{/foreach}}
                </select>
              </td>
            </tr>
            <tr>
              <th>
                <strong>
                  {{mb_label object=$devis field=_total}}
                </strong>
              </th>
              <td>
                {{mb_field object=$devis field=_total readonly=readonly}}
              </td>
              <td colspan="2"></td>
            </tr>
          </table>
        </form>
      </fieldset>
    </td>
  </tr>
  <tr>
  <tr>
    <td colspan="2">
      <ul id="tab-actes" class="control_tabs">
      {{if $conf.dPccam.CCodeCCAM.use_cotation_ccam == "1"}}
        <li><a href="#ccam">Actes CCAM</a></li>
        <li><a id="acc_consultations_a_actes_ngap" href="#ngap">Actes NGAP</a></li>
      {{/if}}
      {{if $conf.dPccam.CCodable.use_frais_divers.CConsultation && $conf.dPccam.CCodeCCAM.use_cotation_ccam}}
        <li><a href="#fraisdivers">Frais divers</a></li>
      {{/if}}
    </ul>

    <div id="ccam" style="display: none;">
      {{assign var=chir_id        value=$devis->praticien_id}}
      {{assign var=do_subject_aed value='do_devis_codage_aed'}}
      {{assign var=module         value='ccam'}}
      {{assign var=object         value=$devis}}
      {{mb_include module=salleOp template=js_codage_ccam}}
      {{assign var=module value="dPcabinet"}}
      {{assign var=subject value=$devis}}
      {{assign var=do_subject_aed value='do_devis_codage_aed'}}
      {{mb_include module=salleOp template=inc_codage_ccam}}
    </div>

    <div id="ngap" style="display: none;">
      <div id="listActesNGAP">
        {{assign var="_object_class" value="CDevisCodage"}}
        {{mb_include module=cabinet template=inc_codage_ngap object=$devis}}
      </div>
    </div>


    {{if $conf.dPccam.CCodable.use_frais_divers.CConsultation && $conf.dPccam.CCodeCCAM.use_cotation_ccam}}
      <div id="fraisdivers" style="display: none;">
        {{mb_include module=ccam template=inc_frais_divers object=$devis}}
      </div>
    {{/if}}
    </td>
  </tr>
</table>