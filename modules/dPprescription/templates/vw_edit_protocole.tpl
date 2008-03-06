{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPmedicament" script="equivalent_selector"}}
{{mb_include_script module="dPprescription" script="element_selector"}}
{{mb_include_script module="dPprescription" script="prescription"}}
{{mb_include_script module="dPprescription" script="protocole"}}

<script type="text/javascript">

function viewProduit(cip){
  var url = new Url;
  url.setModuleAction("dPmedicament", "vw_produit");
  url.addParam("CIP", cip);
  url.popup(900, 640, "Descriptif produit");
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