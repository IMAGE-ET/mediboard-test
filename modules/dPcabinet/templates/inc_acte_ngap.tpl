<script type="text/javascript">
  
refreshTarif = function(){
  var oForm = document.editNGAP;
  url = new Url;
  url.setModuleAction("dPcabinet", "httpreq_vw_tarif_code_ngap");
  url.addParam("quantite", oForm.quantite.value);
  url.addParam("coefficient", oForm.coefficient.value);
  url.addParam("code", oForm.code.value);
  url.requestUpdate('tarifActe', { waitingText: null } );
}
  
ActesNGAP = {
	refreshList: function() {
	  
	  //(refreshFdr || Prototype.emptyFunction)("{{$object->_id}}");
	  //refreshFdr("{{$object->_id}}") || Prototype.emptyFunction;
	  
	  {{if $object->_class_name == "CConsultation"}}
	    refreshFdr("{{$object->_id}}");
	  {{/if}}
	  
	  var url = new Url;
	  url.setModuleAction("dPcabinet", "httpreq_vw_actes_ngap");
	  url.addParam("object_id", "{{$object->_id}}");
	  url.addParam("object_class", "{{$object->_class_name}}");
	  url.requestUpdate('listActesNGAP', {
	    waitingText: null
	  } );
	},

	remove: function(acte_ngap_id){
	  var oForm = document.editNGAP;
	  oForm.del.value = 1;
	  oForm.acte_ngap_id.value = acte_ngap_id;
	  this.submit();
	},
	
	submit: function() {
	  var oForm = document.editNGAP;
		submitFormAjax(oForm, 'systemMsg', { 
			onComplete: ActesNGAP.refreshList
		} );
	}
}

</script>

<form name="editNGAP" method="post" action=""> 
  <input type="hidden" name="acte_ngap_id" value="" />
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="dosql" value="do_acte_ngap_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="object_id" value="{{$object->_id}}" />
  <input type="hidden" name="object_class" value="{{$object->_class_name}}" />
  <table class="form">
  {{if $object->_coded}}
    <tr>
      <td colspan="5">
        <div class="big-info">
        La cotation des actes est terminée.<br />
        Pour pouvoir coder des actes, veuillez dévalider la consultation.
        </div>
      </td>
    </tr> 
    {{/if}}
    <tr>
      <th class="category">{{tr}}CActeNGAP-quantite{{/tr}}</th>
      <th class="category">{{tr}}CActeNGAP-code{{/tr}}</th>
      <th class="category">{{tr}}CActeNGAP-coefficient{{/tr}}</th>
      <th class="category">{{tr}}CActeNGAP-montant_base{{/tr}}</th>
      <th class="category">{{tr}}CActeNGAP-montant_depassement{{/tr}}</th>
      {{if !$object->_coded}}
      <th class="category">Action</th>
      {{/if}}
    </tr>
    {{if !$object->_coded}}
    <tr>
      <td>
        {{mb_field object=$acte_ngap field="quantite" onchange="refreshTarif()"}}
      </td>
      <td>
        {{mb_field object=$acte_ngap field="code" style="width: 200px;" onchange="refreshTarif()"}}
        <div style="display:none;" class="autocomplete" id="code_auto_complete"></div>
      </td>
      <td>
        {{mb_field object=$acte_ngap field="coefficient" onchange="refreshTarif()"}}  
      </td>
      <td id="tarifActe">
        {{assign var="tarif" value=0}}
        {{include file="../../dPcabinet/templates/inc_vw_tarif_ngap.tpl"}}
      </td>
      <td>
        {{mb_field object=$acte_ngap field="montant_depassement"}}
      </td>
      <td>
        <button type="button" class="new" onclick="ActesNGAP.submit()">
          {{tr}}Create{{/tr}}
        </button>
      </td>     
    </tr>
    {{/if}}
    {{foreach from=$object->_ref_actes_ngap item="_acte_ngap"}}
    <tr>
      <td>
        {{mb_value object=$_acte_ngap field="quantite"}}
      </td>
      <td>
        {{mb_value object=$_acte_ngap field="code"}}
      </td>
      <td>
        {{mb_value object=$_acte_ngap field="coefficient"}}  
      </td>
      <td>
        {{mb_value object=$_acte_ngap field="montant_base"}}
      </td>
      <td>
        {{mb_value object=$_acte_ngap field="montant_depassement"}}
      </td>
      {{if !$object->_coded}}
      <td>
       	<button type="button" class="trash" onclick="ActesNGAP.remove({{$_acte_ngap->_id}})">
          {{tr}}Delete{{/tr}}
		 	</button>
      </td>
      {{/if}}
   </tr>
   {{/foreach}}
 </table>
</form>

<script type="text/javascript">

// Preparation du formulaire
prepareForm(document.editNGAP);

// UpdateFields de l'autocomplete
function updateFields(selected) {
  Element.cleanWhitespace(selected);
  dn = selected.childNodes;
  $('editNGAP_code').value = dn[0].firstChild.nodeValue;  
  var oForm = document.editNGAP;
  oForm.code.onchange();
}

// Autocomplete
new Ajax.Autocompleter(
  'editNGAP_code',
  'code_auto_complete',
  '?m=dPcabinet&ajax=1&suppressHeaders=1&a=httpreq_do_ngap_autocomplete&object_id={{$object->_id}}&object_class={{$object->_class_name}}',{
    minChars: 1,
    frequency: 0.15,
    updateElement: updateFields
  }
);
  
</script>