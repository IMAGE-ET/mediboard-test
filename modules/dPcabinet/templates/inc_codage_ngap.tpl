<script type="text/javascript">

refreshTarif = function(){
  var oForm = document.editNGAP;
  var url = new Url("dPcabinet", "httpreq_vw_tarif_code_ngap");
  url.addParam("quantite", oForm.quantite.value);
  url.addParam("coefficient", oForm.coefficient.value);
  url.addParam("demi", oForm.demi.value);
  url.addParam("code", oForm.code.value);
  url.addParam("complement", oForm.complement.value);
  url.requestUpdate('tarifActe', { waitingText: null } );
}
  
ActesNGAP = {
	refreshList: function() {
	  
	  //(refreshFdr || Prototype.emptyFunction)("{{$object->_id}}");
	  //refreshFdr("{{$object->_id}}") || Prototype.emptyFunction;
	  
	  {{if $object->_class_name == "CConsultation"}}
	    refreshReglement("{{$object->_id}}");
	  {{/if}}
	  
	  var url = new Url("dPcabinet", "httpreq_vw_actes_ngap");
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
	
	changeExecutant: function(acte_ngap_id, executant_id){
	  var oForm = document.changeExecutant;
	  $V(oForm.acte_ngap_id, acte_ngap_id); 
		$V(oForm.executant_id, executant_id);
		
		submitFormAjax(oForm, 'systemMsg', { 
      onComplete: ActesNGAP.refreshList
    } );
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
      <td colspan="10">
        <div class="small-info">
        La cotation des actes est terminée.<br />
        Pour pouvoir coder des actes, veuillez dévalider la consultation.
        </div>
      </td>
    </tr> 
    {{/if}}
    
    <tr>
      <th class="category" colspan="10">Codages des actes NGAP</th>
    </tr>

    <tr>
      <th class="category">{{mb_title object=$acte_ngap field=quantite}}</th>
      <th class="category">{{mb_title object=$acte_ngap field=code}}</th>
      <th class="category">{{mb_title object=$acte_ngap field=coefficient}}</th>
      <th class="category">{{mb_title object=$acte_ngap field=demi}}</th>
      <th class="category">{{mb_title object=$acte_ngap field=montant_base}}</th>
      <th class="category">{{mb_title object=$acte_ngap field=montant_depassement}}</th>
      <th class="category">{{mb_title object=$acte_ngap field=complement}}</th>
      <th class="category">{{mb_title object=$acte_ngap field=executant_id}}</th>
      {{if !$object->_coded}}
      <th class="category">Action</th>
      {{/if}}
    </tr>
    
    {{if !$object->_coded}}
    <tr>
      <td>{{mb_field object=$acte_ngap field="quantite" onchange="refreshTarif()"}}</td>
      <td>
        {{mb_field object=$acte_ngap field="code" onchange="refreshTarif()"}}
        <div style="display: none; width: 300px;" class="autocomplete" id="code_auto_complete"></div>
      </td>
      <td>{{mb_field object=$acte_ngap field="coefficient" size="3" onchange="refreshTarif()"}}</td>
      <td>{{mb_field object=$acte_ngap field="demi" onchange="refreshTarif()"}}</td>
      <td id="tarifActe">
        {{include file="../../dPcabinet/templates/inc_vw_tarif_ngap.tpl" tarif=0}}
      </td>
      <td>{{mb_field object=$acte_ngap field="montant_depassement"}}</td>
      <td>{{mb_field object=$acte_ngap field="complement" onchange="refreshTarif()" defaultOption="&mdash; Aucun"}}</td>
      <td>
        <select name="executant_id" style="width: 120px;" class="{{$acte_ngap->_props.executant_id}}">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{foreach from=$acte_ngap->_list_executants item=curr_executant}}
          <option class="mediuser" style="border-color: #{{$curr_executant->_ref_function->color}};" value="{{$curr_executant->user_id}}" {{if $acte_ngap->executant_id == $curr_executant->user_id}} selected="selected" {{/if}}>
            {{$curr_executant->_view}}
          </option>
          {{/foreach}}
        </select>
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
      <td>{{mb_value object=$_acte_ngap field="quantite"}}</td>
      <td>{{mb_value object=$_acte_ngap field="code"}}</td>
      <td>{{mb_value object=$_acte_ngap field="coefficient"}}</td>
      <td>{{mb_value object=$_acte_ngap field="demi"}}</td>
      <td>{{mb_value object=$_acte_ngap field="montant_base"}}</td>
      <td>{{mb_value object=$_acte_ngap field="montant_depassement"}}</td>
      <td>
        {{if $_acte_ngap->complement}}
          {{mb_value object=$_acte_ngap field="complement"}}
        {{else}}
          Aucun
        {{/if}}
      </td>

      {{assign var="executant" value=$_acte_ngap->_ref_executant}}
			<td>
				{{if !$object->_coded}}
				<select onchange="ActesNGAP.changeExecutant('{{$_acte_ngap->_id}}', $V(this))" name="executant_id" style="width: 150px;" class="{{$acte_ngap->_props.executant_id}}">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{foreach from=$acte_ngap->_list_executants item=_executant}}
          <option class="mediuser" {{if $executant == $_executant->_view}}selected="selected"{{/if}} style="border-color: #{{$_executant->_ref_function->color}};" value="{{$_executant->user_id}}" {{if $acte_ngap->executant_id == $_executant->user_id}} selected="selected" {{/if}}>
            {{$_executant}}
          </option>
          {{/foreach}}
        </select>
				{{else}}
				<div class="mediuser" style="border-color: #{{$executant->_ref_function->color}};">
         {{$executant}}
        </div>
				{{/if}}
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


<form name="changeExecutant" method="post" action=""> 
  <input type="hidden" name="acte_ngap_id" value="" />
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="dosql" value="do_acte_ngap_aed" />
	
	<input type="hidden" name="executant_id" value="" />
</form>

<script type="text/javascript">

{{if !$object->_coded}}

// Preparation du formulaire
prepareForm(document.editNGAP);

// UpdateFields de l'autocomplete
function updateFields(selected) {
  $V(document.editNGAP.code, selected.select('.code')[0].innerHTML, true);
}

// Autocomplete
var url = new Url("dPcabinet", "httpreq_do_ngap_autocomplete");
url.addParam("object_id", "{{$object->_id}}");
url.addParam("object_class", "{{$object->_class_name}}");
url.autoComplete('editNGAP_code', 'code_auto_complete', {
    minChars: 1,
    updateElement: updateFields
} );

{{/if}}
  
</script>