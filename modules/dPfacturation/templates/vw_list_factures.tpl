<style>
  table.main td.reglee, table.main tr.reglee{
    background-color:#cfc;
  }
  table.main td.cloture, table.main tr.cloture{
    background-color:#fcc;
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
  requestModal();
}
</script>

<table class="main" style="overflow:auto;">
  <tr>
    <td style="width:200px;">
      <table class="tbl">
        <tr>
          <th colspan="{{if $conf.dPfacturation.CEditPdf.use_bill_etab}}5{{else}}2{{/if}}" class="title">Factures</th>
        </tr>
        <tr>
          <th>Date</th>
          <th>Patient</th>
          {{if $conf.dPfacturation.CEditPdf.use_bill_etab}}
            <th>Numéro</th>
            <th>Date séjour</th>
            <th>Numéro séjour</th>
          {{/if}}
        </tr>
        {{foreach from=$factures item=_facture}}
          <tr class="{{if $facture->_id == $_facture->_id}}selected{{/if}}" >
            {{assign var="classe" value=$facture->_class}}
            <td class=" narrow {{if !$_facture->cloture}}cloture{{/if}} {{if $_facture->patient_date_reglement}}reglee{{/if}}">
              {{if $_facture->cloture}}
                {{mb_value object=$_facture field="cloture"}}
              {{else}}
                {{$_facture->ouverture|date_format:"%d/%m/%Y"}}
              {{/if}}
            </td>
            <td class="text {{if !$_facture->cloture}}cloture{{/if}} {{if $_facture->patient_date_reglement}}reglee{{/if}}">
              <a onclick="viewFacture(this, '{{$_facture->facture_id}}', '{{$_facture->_class}}');" href="#"
                onmouseover="ObjectTooltip.createEx(this, '{{$_facture->_ref_patient->_guid}}')">
                {{$_facture->_ref_patient->_view|truncate:30:"...":true}}
              </a>
            </td>
            {{if $conf.dPfacturation.CEditPdf.use_bill_etab}}
              <td style="text-align: right;">{{$_facture->_id|string_format:"%08d"}}</td>
              <td>{{$_facture->_ref_last_sejour->entree_prevue|date_format:"%d/%m/%Y"}}</td>
              <td style="text-align: right;">{{$_facture->_ref_last_sejour->_id}}</td>
            {{/if}}
          </tr>
        {{foreachelse}}
          <tr>
            <td colspan="2" class="empty">{{tr}}{{$facture->_class}}.none{{/tr}}</td>
          </tr>
        {{/foreach}}
      </table>
    </td>
    <td id="load_facture">
      {{mb_include module=facturation template=inc_vw_facturation }}
    </td>
  </tr>
</table>