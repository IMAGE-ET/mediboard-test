<style>
  table.main td.reglee, table.main tr.reglee{
    background-color:#cfc;
  }
  table.main td.cloture, table.main tr.cloture{
    background-color:#fcc;
  }
  table.main td.noncotee, table.main tr.noncotee{
    background-color:#ffcd75;
  }
</style>
{{if $conf.dPfacturation.CRelance.use_relances}}
  {{mb_script module=facturation script=relance ajax="true"}}
{{/if}}
<script>
viewFacture = function(element, facture_id, facture_class){
  if (element) {
    element.up("tr").addUniqueClassName("selected");
  }
   
  var url = new Url("facturation"     , "ajax_view_facture");
  url.addParam("facture_id", facture_id);
  url.addParam("object_class", facture_class);
  url.requestUpdate('load_facture');
}

showLegend = function() {
  new Url('facturation', 'vw_legende').
  addParam('classe', '{{$facture->_class}}').
  requestModal(200);
}
</script>

<table class="main" style="overflow:auto;">
  <tr>
    <td style="width:200px;">
      <table class="tbl">
        <tr>
          <th colspan="{{if $conf.dPfacturation.CEditPdf.use_bill_etab}}6{{else}}2{{/if}}" class="title">Factures</th>
        </tr>
        <tr>
          {{if $conf.dPfacturation.CEditPdf.use_bill_etab}}
            <th>Date séjour</th>
            <th>Numéro</th>
          {{/if}}
          <th>Date</th>
          <th>Patient</th>
          {{if $conf.dPfacturation.CEditPdf.use_bill_etab}}
            <th>N° patient</th>
            <th>Numéro séjour</th>
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
              <td class="{{$reglee}} {{$cloture}}">{{$_facture->_ref_last_sejour->entree_prevue|date_format:"%d/%m/%Y"}}</td>
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
              <td style="text-align: right;" class="{{$reglee}} {{$cloture}}">{{$_facture->_ref_last_sejour->_id}}</td>
            {{/if}}
          </tr>
        {{foreachelse}}
          <tr>
            <td colspan="{{if $conf.dPfacturation.CEditPdf.use_bill_etab}}6{{else}}2{{/if}}" class="empty">
              {{tr}}{{$facture->_class}}.none{{/tr}}
            </td>
          </tr>
        {{/foreach}}
      </table>
    </td>
    <td id="load_facture">
      {{mb_include module=facturation template=inc_vw_facturation }}
    </td>
  </tr>
</table>