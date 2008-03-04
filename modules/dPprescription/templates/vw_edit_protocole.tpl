{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPmedicament" script="equivalent_selector"}}
{{mb_include_script module="dPprescription" script="element_selector"}}
{{mb_include_script module="dPprescription" script="prescription"}}


<script type="text/javascript">

// Ajout d'un protocole
function addProtocole(){
  var oFormPrat = document.selPrat;
  var oForm = document.addProtocolePresc;
  oForm.praticien_id.value = oFormPrat.praticien_id.value;
  submitFormAjax(oForm, 'systemMsg', {
    onComplete: function() { 
      reloadProtocoles(oFormPrat.praticien_id.value);
    } } );
}

function delProtocole(oForm){
  var oFormPrat = document.selPrat;
  submitFormAjax(oForm, 'systemMsg', {
    onComplete: function(){
      reloadProtocoles(oFormPrat.praticien_id.value);
      //$("vw_protocole").innerHTML = "&nbsp";
  } } );
}


// Rafraichissement de la liste des protocoles
function reloadProtocoles(praticien_id){
  var url = new Url;
  url.setModuleAction("dPprescription", "httpreq_vw_list_protocoles");
  url.addParam("praticien_id", praticien_id);
  url.requestUpdate("protocoles", { waitingText: null } );
}

function viewProtocole(protocole_id){
  Prescription.reload(protocole_id, "", "1");
}

</script>

<table class="main">
  <tr>
    <!-- Affichage de la liste des protocoles pour le praticien selectionné -->
    <td class="halfPane" style="width: 330px;">
	    <form name="selPrat" action="?" method="get">
	      <input type="hidden" name="tab" value="vw_edit_protocole" />
        <input type="hidden" name="m" value="dPprescription" />
        <select name="praticien_id" onchange="this.form.submit()">
          <option value="">&mdash; Sélection d'un praticien</option>
	        {{foreach from=$praticiens item=praticien}}
	        <option class="mediuser" 
	                style="border-color: #{{$praticien->_ref_function->color}};" 
	                value="{{$praticien->_id}}"
	                {{if $praticien->_id == $praticien_id}}selected="selected"{{/if}}>{{$praticien->_view}}
	        </option>
	        {{/foreach}}
	      </select>
	    </form>
	    <a href="?m={{$m}}&amp;tab={{$tab}}" class="buttonnew">
        Créer un protocole
      </a>
	    <div id="protocoles">
		    {{include file="inc_vw_list_protocoles.tpl"}}
	    </div>
    </td>
    <!-- Affichage du protocole sélectionné-->
    <td>
      <div id="vw_protocole">
		    {{include file="inc_vw_prescription.tpl" httpreq=1 mode_protocole=1 prescription=$protocole category="medicament"}}
		  </div>  
    </td>
  </tr>
</table>