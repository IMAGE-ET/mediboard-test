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
          <tr class="{{if $facture->_id == $_facture->_id}}selected{{/if}}">
            <td>
              {{if $_facture->cloture}}
                {{mb_value object=$_facture field="cloture"}}
              {{else}}
                {{$_facture->ouverture|date_format:"%d/%m/%Y"}}
              {{/if}}
            </td>
            <td class="text">
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