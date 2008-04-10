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
  
  <!-- Cas d'une prescription de sejour -->
  {{if $prescription->object_class == "CSejour"}}
  {{assign var=sejour value=$prescription->_ref_object}}
  {{assign var=prescriptions value=$sejour->_ref_prescriptions}}
  <tr>
    <td>
      <table style="width: 100%">
        <tr>
        
        <!-- 
          <td id="traitement" class="step">
          {{if $prescriptions.traitement}}
	          {{assign var=prescription_traitement value=$prescriptions.traitement.0}}
	          {{if $prescription_traitement->_id}}
	          {{if $prescription->type == "traitement" && !$prescriptions.pre_admission}}
	            <form name="addPrescriptionPreAdmission" method="post" action="">
	              <input type="hidden" name="m" value="dPprescription" />
	              <input type="hidden" name="dosql" value="do_duplicate_prescription_aed" />
	              <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
	              <input type="hidden" name="type" value="pre_admission" />
	              <button type="button" class="add" onclick="this.form.submit();">
	                Sortie
	              </button>
	            </form>
	          {{/if}}
	            <a href="?m=dPprescription&amp;a=vw_edit_prescription&amp;dialog=1&amp;prescription_id={{$prescription_traitement->_id}}"
	              {{if $prescription->type == "traitement"}}style="font-size: 200%"{{/if}}>
                {{tr}}CPrescription.type.traitement{{/tr}}
	            </a>
	          {{/if}}
          {{/if}}
          </td>
         -->
           
          <td id="pre_admission" class="step">
          {{if $prescriptions.pre_admission}}
          {{assign var=prescription_pre_admission value=$prescriptions.pre_admission.0}}
          {{if $prescription_pre_admission->_id}}
            <a href="?m=dPprescription&amp;a=vw_edit_prescription&amp;dialog=1&amp;prescription_id={{$prescription_pre_admission->_id}}"
              {{if $prescription->type == "pre_admission"}}style="font-size: 200%"{{/if}}>
              {{tr}}CPrescription.type.pre_admission{{/tr}}
            </a>
            {{if $prescription->type == "pre_admission" && !$prescriptions.sejour}}
              <form name="addPrescriptionSejour" method="post" action="">
	              <input type="hidden" name="m" value="dPprescription" />
			          <input type="hidden" name="dosql" value="do_duplicate_prescription_aed" />
			          <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
			          <input type="hidden" name="type" value="sejour" />
			          <button type="button" class="add" onclick="this.form.submit();">
			            Séjour
			          </button> 
		          </form>   
            {{/if}}
          {{/if}}
          {{/if}}
          </td>
          
          <td id="sejour" class="step">
          {{if $prescriptions.sejour}}
          {{assign var=prescription_sejour value=$prescriptions.sejour.0}}
          {{if $prescription_sejour->_id}}
          
            <a href="?m=dPprescription&amp;a=vw_edit_prescription&amp;dialog=1&amp;prescription_id={{$prescription_sejour->_id}}"
              {{if $prescription->type == "sejour"}}style="font-size: 200%"{{/if}}>
              {{tr}}CPrescription.type.sejour{{/tr}}
            </a>
          {{if $prescription->type == "sejour"}}
          <form name="addPrescriptionSejour" method="post" action="">
	          <input type="hidden" name="m" value="dPprescription" />
	          <input type="hidden" name="dosql" value="do_duplicate_prescription_aed" />
	          <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
	          <input type="hidden" name="type" value="sortie" />
	          <button type="button" class="add" onclick="this.form.submit();">
	          Sortie
	          </button>
          </form>
          {{/if}}
          {{/if}}
          {{/if}}
          </td>
         
          <td id="sortie" class="step">
          {{if $prescriptions.sortie}}
            <span {{if $prescription->type == "sortie"}}style="font-size: 200%"{{/if}}>
		          {{tr}}CPrescription.type.sortie{{/tr}}
		        </span>
          <form name="selSortie" method="get" action="">
          <select name="selPrescriptionSortie" onchange="if(this.value != ''){ window.location.href='?m=dPprescription&amp;a=vw_edit_prescription&amp;dialog=1&amp;prescription_id='+this.value} ">
            <option value="">&mdash; Praticien</option>
          {{foreach from=$prescriptions.sortie item=prescription_sortie}}
            <option {{if $prescription_sortie->_id == $prescription->_id}}selected="selected"{{/if}}value="{{$prescription_sortie->_id}}">{{$prescription_sortie->_ref_praticien->_view}}</option>
          {{/foreach}}
          </select>
          </form>
          {{/if}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
  {{else}}
  <!-- Cas d'une prescription de consultation -->
  <tr>
    <th style="background-color: #ccc">
      Prescription Externe
    </th>
  </tr>
  {{/if}}
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