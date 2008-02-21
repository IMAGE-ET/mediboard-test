{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPmedicament" script="equivalent_selector"}}

<script type="text/javascript">

var Prescription = {
  addEquivalent: function(code, line_id){
    Prescription.delLineWithoutRefresh(line_id);
    // Suppression des champs de addLine
    var oForm = document.addLine;
    oForm.prescription_line_id.value = "";
    oForm.del.value = "";
    Prescription.addLine(code);
  },
  popup : function() {
    alert("vous etes déjà en dialogue");
  },
  close : function() {
    var url = new Url;
    url.setModuleTab("{{$m}}", "{{$tab}}");
    url.addParam("prescription_id", 0);
    url.addParam("object_class", {{$prescription->object_class|json}});
    url.addParam("object_id", {{$prescription->object_id|json}});
    url.redirect();
  },
  addProtocole: function(code) {
    //var oForm = document.addProtocole;
    //oForm.protocole_id.value = code;
    //submitFormAjax(oForm, 'systemMsg', { onComplete : Prescription.reload });
    alert("Protocole selectionné");
  },
  addOther: function(code) {
    alert("Element selectionné");
  },
  addLine: function(code) {
    var oForm = document.addLine;
    oForm.code_cip.value = code;
    submitFormAjax(oForm, 'systemMsg', { onComplete : Prescription.reload });
  },
  delLineWithoutRefresh: function(line_id) {
    var oForm = document.addLine;
    oForm.prescription_line_id.value = line_id;
    oForm.del.value = 1;
    submitFormAjax(oForm, 'systemMsg');
  },
  delLine: function(line_id) {
    var oForm = document.addLine;
    oForm.prescription_line_id.value = line_id;
    oForm.del.value = 1;
    submitFormAjax(oForm, 'systemMsg', { 
      onComplete : Prescription.reload 
    });
  },
  reload: function() {
    {{if $prescription->_id}}
    var urlPrescription = new Url;
    urlPrescription.setModuleAction("dPprescription", "httpreq_vw_prescription");
    urlPrescription.addParam("prescription_id", {{$prescription->_id}});
    urlPrescription.requestUpdate("prescription", { waitingText : null });
    {{/if}}
  },
  reloadAlertes: function() {
    {{if $prescription->_id}}
    return true;
    {{else}}
    alert('Pas de prescription en cours');
    {{/if}}
  },
  print: function() {
    var url = new Url;
    url.setModuleAction("dPprescription", "print_prescription");
    url.addParam("prescription_id", {{$prescription->_id}});
    url.popup(700, 600, "print_prescription");
  }
};

// Visualisation du produit
function viewProduit(cip){
  var url = new Url;
  url.setModuleAction("dPmedicament", "vw_produit");
  url.addParam("CIP", cip);
  url.popup(900, 640, "Descriptif produit");
}

// UpdateFields de l'autocomplete
function updateFields(selected) {
  Element.cleanWhitespace(selected);
  dn = selected.childNodes;
  Prescription.addLine(dn[0].firstChild.nodeValue);
  $('searchProd_produit').value = "";
}

</script>

<table class="main">
  {{if $prescription->_id}}
    <td class="greedyPane">
      {{assign var=httpreq value=0}}
      <div id="prescription">
        {{include file="inc_vw_prescription.tpl"}}
      </div>
    </td>
  </tr>
  {{else}}
  <tr>
    <td>
      <div class="big-info">
        Veuillez choisir un contexte (séjour ou consultation) pour la prescription
      </div>
    </td>
  </tr>
  {{/if}}
</table>