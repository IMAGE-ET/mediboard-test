{{mb_default var=total_factures valu=24}}
<table class="tbl">
  {{if $total_factures > 25}}
    {{mb_include module=system template=inc_pagination total=$total_factures current=$page step=25 change_page='changePage'}}
  {{/if}}
  <tr>
    <th colspan="{{if $conf.dPfacturation.CEditPdf.use_bill_etab}}6{{else}}3{{/if}}" class="title">Factures</th>
  </tr>
  <tr>
    {{if $conf.dPfacturation.CEditPdf.use_bill_etab}}
      {{if $facture->_class == "CFactureEtablissement"}}
        <th>Date séjour</th>
      {{/if}}
      <th>Numéro</th>
    {{/if}}
    <th>Date</th>
    <th>Patient</th>
    {{if $conf.dPfacturation.CEditPdf.use_bill_etab}}
      <th>N° patient</th>
      {{if $facture->_class == "CFactureEtablissement"}}
        <th>Séjour</th>
      {{/if}}
    {{else}}
      <th>{{mb_title object=$facture field=numero}}</th>
    {{/if}}
  </tr>
  {{foreach from=$factures item=_facture}}
    <tr class="{{if $facture->_id == $_facture->_id}}selected{{/if}}" >
      {{assign var="cloture" value=""}}
      {{assign var="reglee" value=""}}
      {{if !$_facture->cloture}}
        {{assign var="cloture" value="cloture"}}
      {{/if}}
      {{if $_facture->patient_date_reglement}}
        {{assign var="reglee" value="reglee"}}
      {{/if}}
      {{if $_facture->annule}}
        {{assign var="cloture" value="hatching"}}
      {{/if}}
      {{if !$_facture->_ref_actes_tarmed|@count && !$_facture->_ref_actes_caisse|@count && !$_facture->_ref_actes_ngap|@count && !$_facture->_ref_actes_ccam|@count}}
        {{assign var="cloture" value="noncotee"}}
      {{/if}}
      {{assign var="classe" value=$facture->_class}}
      {{if $conf.dPfacturation.CEditPdf.use_bill_etab}}
        {{if $facture->_class == "CFactureEtablissement"}}
          <td class="{{$reglee}} {{$cloture}}">{{$_facture->_ref_last_sejour->entree_prevue|date_format:"%d/%m/%Y"}}</td>
        {{/if}}
        <td style="text-align: right;" class="{{$reglee}} {{$cloture}}">{{$_facture->_id|string_format:"%08d"}}</td>
      {{/if}}
      <td class=" narrow {{$reglee}} {{$cloture}}">
        {{if $_facture->cloture}}
          {{mb_value object=$_facture field="cloture"}}
        {{else}}
          {{$_facture->ouverture|date_format:"%d/%m/%Y"}}
        {{/if}}
      </td>
      <td class="text {{$reglee}} {{$cloture}}">
        <a onclick="viewFacture(this, '{{$_facture->facture_id}}', '{{$_facture->_class}}');" href="#"
           onmouseover="ObjectTooltip.createEx(this, '{{$_facture->_ref_patient->_guid}}')">
          {{$_facture->_ref_patient->_view|truncate:30:"...":true}}
        </a>
      </td>
      {{if $conf.dPfacturation.CEditPdf.use_bill_etab}}
        <td style="text-align: right;" class="{{$reglee}} {{$cloture}}">{{$_facture->patient_id}}</td>
        {{if $facture->_class == "CFactureEtablissement"}}
          <td style="text-align: right;" class="{{$reglee}} {{$cloture}}">{{$_facture->_ref_last_sejour->_id}}</td>
        {{/if}}
      {{else}}
        <td style="text-align: center;" class="{{$reglee}} {{$cloture}}">{{mb_value object=$_facture field=numero}}</td>
      {{/if}}
    </tr>
    {{foreachelse}}
    <tr>
      <td colspan="{{if $conf.dPfacturation.CEditPdf.use_bill_etab}}6{{else}}3{{/if}}" class="empty">
        {{tr}}{{$facture->_class}}.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>