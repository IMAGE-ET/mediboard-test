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
    <th colspan="2" class="title">{{tr}}CDevisCodage-title-modify{{/tr}}</th>
  </tr>
  <tr>
    <td class="halfPane" style="text-align: center">
      <form name="editDevisDate-{{$view}}" action="?" method="post" onsubmit="return false;">
        {{mb_class object=$devis}}
        {{mb_key object=$devis}}
        <table class="form">
          <tr>
            <td class="halfPane" style="text-align: right">
              {{mb_label object=$devis field=date class=notNull}}
            </td>
            <td class="halfPane" style="text-align: left">
              {{mb_field object=$devis field=date form="editDevisDate-$view" class=notNull register=true onchange="DevisCodage.syncField(this, '$view');"}}
            </td>
          </tr>
        </table>
      </form>
    </td>
    <td class="halfPane" style="text-align: center">
      <form name="editDevisEvent-{{$view}}" action="?" method="post" onsubmit="return false;">
        <table class="form">
          <tr>
            <td class="halfPane" style="text-align: right">
              {{mb_label object=$devis field=event_type class=notNull}}
            </td>
            <td class="halfPane" style="text-align: left">
              {{mb_field object=$devis field=event_type class=notNull onchange="DevisCodage.syncField(this, '$view');"}}
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
  <tr>
    <td colspan="2" style="text-align: center">
      <form name="editDevisLibelle-{{$view}}" action="?" method="post" onsubmit="return false;">
        <table class="form">
          <tr>
            <td class="halfPane" style="text-align: right">
              {{mb_label object=$devis field=libelle class=notNull}}
            </td>
            <td class="halfPane" style="text-align: left">
              {{mb_field object=$devis field=libelle class=notNull onchange="DevisCodage.syncField(this, '$view');"}}
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
  {{if $devis->date}}
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
    <tr>
      <td colspan="2">
        <hr />
      </td>
    </tr>
  {{/if}}
  <tr>
    <td class="halfPane" style="text-align: center">
      <form name="editDevisBase" action="?" method="post" onsubmit="return false;">
        <table class="form">
          <tr>
            <td class="halfPane" style="text-align: right">
              {{mb_label object=$devis field=base}}
            </td>
            <td class="halfPane" style="text-align: left">
              {{mb_field object=$devis field=base readonly=readonly onchange="DevisCodage.syncField(this, '$view');"}}
            </td>
          </tr>
        </table>
      </form>
    </td>
    <td class="halfPane" style="text-align: center">
      <form name="editDevisDH" action="?" method="post" onsubmit="return false;">
        <table class="form">
          <tr>
            <td class="halfPane" style="text-align: right">
              {{mb_label object=$devis field=dh}}
            </td>
            <td class="halfPane" style="text-align: left">
              {{mb_field object=$devis field=dh readonly=readonly onchange="DevisCodage.syncField(this, '$view');"}}
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
  <tr>
    <td class="halfPane" style="text-align: center">
      <form name="editDevisHT" action="?" method="post" onsubmit="return false;">
        <table class="form">
          <tr>
            <td class="halfPane" style="text-align: right">
              {{mb_label object=$devis field=ht}}
            </td>
            <td class="halfPane" style="text-align: left">
                {{mb_field object=$devis field=ht readonly=readonly}}
            </td>
          </tr>
        </table>
      </form>
    </td>
    <td class="halfPane" style="text-align: center">
      <form name="editDevisTVA" action="?" method="post" onsubmit="return false;">
        <table class="form">
          <tr>
            <td class="halfPane" style="text-align: right">
              {{mb_label object=$devis field=tax_rate onchange="DevisCodage.syncField(this, '$view');"}}
            </td>
            <td class="halfPane" style="text-align: left">
              {{mb_field object=$devis field=tax_rate onchange="DevisCodage.syncField(this, '$view');"}}
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
  <tr>
    <td colspan="2" style="text-align: center">
      <form name="editDevisTotal" action="?" method="post" onsubmit="return false;">
        <table class="form">
          <tr>
            <td class="halfPane" style="text-align: right">
              {{mb_label object=$devis field=_total}}
            </td>
            <td class="halfPane" style="text-align: left">
              {{mb_field object=$devis field=_total readonly=readonly}}
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
  <tr>
    <td colspan="2" style="text-align: center">
      <form name="editDevisComment" action="?" method="post" onsubmit="return false;">
        <table class="form">
          <tr>
            <td class="halfPane" style="text-align: right">
              {{mb_label object=$devis field=comment}}
            </td>
            <td class="halfPane" style="text-align: left">
              {{mb_field object=$devis field=comment onchange="DevisCodage.syncField(this, '$view');"}}
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
  <tr>
    <td colspan="2" style="text-align: center">
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
        {{mb_field object=$devis field=date hidden=true}}
        {{mb_field object=$devis field=event_type hidden=true}}
        {{mb_field object=$devis field=libelle hidden=true}}
        {{mb_field object=$devis field=comment hidden=true}}
        {{mb_field object=$devis field=base hidden=true}}
        {{mb_field object=$devis field=dh hidden=true}}
        {{mb_field object=$devis field=ht hidden=true}}
        {{mb_field object=$devis field=tax_rate hidden=true}}

        <button class="save" type="submit">{{tr}}Save{{/tr}}</button>
        <button class="print" type="button" onclick="DevisCodage.print('{{$devis->_id}}')">{{tr}}Print{{/tr}}</button>
        <button class="trash" type="button" onclick="$V(this.form.del, 1); return onSubmitFormAjax(this.form, {onComplete: function() {
          this.form, window.parent.Control.Modal.close();}});"
          {{if $devis->_count_actes != 0}} disabled="disabled"{{/if}}>
          {{tr}}Delete{{/tr}}
        </button>
      </form>
    </td>
  </tr>
</table>