{{mb_include_script module="dPmedicament" script="medicament_selector"}}

<script type="text/javascript">

var Prescription = {
  close : function() {
    var url = new Url;
    url.setModuleTab("{{$m}}", "{{$tab}}");
    url.addParam("prescription_id", 0);
    url.addParam("object_class", "{{$prescription->object_class}}");
    url.addParam("object_id", {{$prescription->object_id}});
    url.redirect();
  },
  search: function(produit) {
  },
  addLine: function(code) {
    var oForm = document.addLine;
    oForm.code_cip.value = code;
    submitFormAjax(oForm, 'systemMsg', { onComplete : Prescription.reload });
  },
  delLine: function(line_id) {
    var oForm = document.addLine;
    oForm.prescription_line_id.value = line_id;
    oForm.del.value = 1;
    submitFormAjax(oForm, 'systemMsg', { onComplete : Prescription.reload });
  },
  reload: function() {
    var url = new Url;
    {{if $prescription->_id}}
    url.setModuleAction("dPprescription", "httpreq_vw_prescription");
    url.addParam("prescription_id", {{$prescription->_id}});
    url.requestUpdate("prescription", { waitingText : null });
    {{/if}}
  }
};

// UpdateFields de l'autocomplete
function updateFields(selected) {
  Element.cleanWhitespace(selected);
  dn = selected.childNodes;
  Prescription.addLine(dn[0].firstChild.nodeValue);
  $('searchProd_produit').value = "";
}

</script>

<table class="main">
{{if $prescription->object_id}}
  {{if $prescription->_id}}
  <tr>
    <td />
    <td>
        <button type="button" class="trash" onclick="Prescription.close()">
          Fermer
        </button>
    </td>
  </tr>
  {{/if}}
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th>Patient</th>
        </tr>
        <tr>
          <td>{{$prescription->_ref_object->_ref_patient->_view}}</td>
        </tr>
      </table>
    </td>
    <td rowspan="3" class="greedyPane">
      <div id="prescription">
      {{include file="inc_vw_prescription.tpl"}}
      </div>
    </td>
  </tr>
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th>Sejour</th>
        </tr>
        <tr>
          <td>{{$prescription->_ref_object->_ref_sejour->_view}}</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <table class="tbl">
        <tr>
          <th>Liste des ordonnances</th>
        </tr>
        {{foreach from=$prescription->_ref_object->_ref_prescriptions item=curr_prescription}}
        <tr>
          <td>
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;prescription_id={{$curr_prescription->_id}}" >
              {{$curr_prescription->_view}}
            </a>
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
{{else}}
  <tr>
    <td>Veuillez choisir un séjour ou une consultation (&object_class=CSejour&object_id=35976)</td>
  </tr>
{{/if}}
</table>