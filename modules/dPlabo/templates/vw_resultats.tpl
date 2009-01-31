{{mb_include_script module="dPpatients" script="pat_selector"}}

<script type="text/javascript">

var Prescription = {
  select : function(prescription_id) {
  }
}

function printResultats(prescription_id){
  var url = new Url;
  url.setModuleAction("dPlabo", "vw_resultat_pdf");
  url.addParam("suppressHeaders", "1");
  url.addParam("prescription_id", prescription_id);
  url.popup(800, 700, "Resultats");
}

var Anteriorite = {
 url: new Url,
 viewItem: function(item_id) {
   this.url.setModuleAction("dPlabo", "httpreq_graph_resultats");
   this.url.addParam("prescription_labo_examen_id", item_id);
   this.url.popup(370, 700, ["Anteriorite", item_id].join(), "Anteriorite");
 }
}

var IMeds = {
  viewPatient: function(patient_id, div) {
    var url = new Url;
    url.setModuleAction("dPImeds", "httpreq_vw_patient_results");
    url.addParam("patient_id", patient_id);
    url.requestUpdate(div, { waitingText : null });
  },
  
  viewPrescription: function(prescription_id, div) {
    var url = new Url;
    url.setModuleAction("dPImeds", "httpreq_vw_prescription_results");
    url.addParam("prescription_id", prescription_id);
    url.requestUpdate(div, { waitingText : null });
  }
}

Main.add(function () {
  {{if $prescription->_id}}
    ViewPort.SetAvlHeight("resultats-internes", 0.5);
    ViewPort.SetAvlHeight("resultats-externes", 1);
  
    IMeds.viewPrescription({{$prescription->_id}}, "resultats-externes");
  {{/if}}
});

</script>

<table class="main">
  <tr>
  
    <!-- Choose a patient -->
    <td class="halfPane">
      <form name="Patient" action="?" method="get">

      <input type="hidden" name="m" value="dPlabo" />
      <input type="hidden" name="patient_id" value="{{$patient->_id}}" onchange="this.form.submit()" />

      <table class="form">
        <tr>
          <th>
            <label for="_view" title="Merci de choisir un patient pour voir son dossier">Choix du patient</label>
          </th>
          <td class="readonly">
            <span class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
            <input type="text" readonly="readonly" name="_view" value="{{$patient->_view}}" />
            </span>
            <button class="search" type="button" onclick="PatSelector.init()">{{tr}}Search{{/tr}}</button>
            <script type="text/javascript">
            PatSelector.init = function(){
              this.sForm = "Patient";
              this.sId   = "patient_id";
              this.sView = "_view";
              this.pop();
            }
            </script>
          </td>
        </tr>
      </table>

      </form>

    </td>
    
    {{if $patient->_id}}
    <!-- Choose a prescription -->
    <td class="halfPane">
      
      <form name="Prescription" action="?" method="get">

      <input type="hidden" name="m" value="dPlabo" />
      
      <table class="form">
        <tr>
          <th>
            <label for="prescription_id" title="Merci de choisir une prescription à afficher">Prescription</label>
          </th>
          <td>
            <select name="prescription_id" onchange="this.form.submit()">
              <option value="">&mdash; Choisir une prescription</option>
              {{foreach from=$patient->_ref_prescriptions item="_prescription"}}
              <option value="{{$_prescription->_id}}" {{if $_prescription->_id == $prescription->_id}}selected="selected"{{/if}}>
                {{$_prescription->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
          {{if $prescription->_id}}
          <td>
            <button class="print notext" type="button" onclick="printResultats('{{$prescription->_id}}')">
              {{tr}}Print{{/tr}}
            </button>
          </td>
          {{/if}}
        </tr>
      </table>
      </form>
    </td>
    {{/if}}
  </tr>
  
  {{if $prescription->_id}}
  <!-- Show results for selected prescription -->
  <tbody class="viewported">
  <tr>
    <td class="viewport" colspan="2">
      <div id="resultats-internes">
        <table class="tbl">
        {{foreach from=$prescription->_ref_classification_roots item=_catalogue}}
        {{include file="tree_resultats.tpl"}}
        {{/foreach}}
        </table>
      </div>
    </td>
  </tr>
  <tr>
    <td class="viewport" colspan="2">
      <div id="resultats-externes">
      </div>
    </td>
  </tr>
  {{/if}}
  
</table>