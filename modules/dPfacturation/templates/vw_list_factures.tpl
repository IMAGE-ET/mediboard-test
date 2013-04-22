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
          <th colspan="2" class="title">Factures</th>
        </tr>
        <tr>
          <th>Date</th>
          <th>Patient</th>
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
            {{*<td class="text {{if $_facture->cloture && !$conf.dPfacturation.$classe.use_auto_cloture}}hatching{{/if}} {{if $_facture->patient_date_reglement}}reglee{{/if}}">*}}
              <a onclick="viewFacture(this, '{{$_facture->facture_id}}', '{{$_facture->_class}}');" href="#"
                onmouseover="ObjectTooltip.createEx(this, '{{$_facture->_ref_patient->_guid}}')">
                {{$_facture->_ref_patient->_view|truncate:30:"...":true}}
              </a>
            </td>
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