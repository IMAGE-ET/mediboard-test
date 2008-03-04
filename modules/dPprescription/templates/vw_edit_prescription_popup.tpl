{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPmedicament" script="equivalent_selector"}}
{{mb_include_script module="dPprescription" script="element_selector"}}
{{mb_include_script module="dPprescription" script="prescription"}}

<script type="text/javascript">

// Visualisation du produit
function viewProduit(cip){
  var url = new Url;
  url.setModuleAction("dPmedicament", "vw_produit");
  url.addParam("CIP", cip);
  url.popup(900, 640, "Descriptif produit");
}

</script>

<table class="main">
  {{if $prescription->_id}}
  <tr>
    <td class="greedyPane">
      {{assign var=httpreq value=0}}
      <div id="prescription">
        {{include file="inc_vw_prescription.tpl" mode_protocole=0}}
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