<script type="text/javascript">

Main.add( function(){
  prepareForm(document.forms['addPriseFoisPar{{$type}}{{$line->_id}}']);
  prepareForm(document.forms['addPriseMoment{{$type}}{{$line->_id}}']); 
  prepareForm(document.forms['addPriseTousLes{{$type}}{{$line->_id}}']);
} );

// Affichage des div d'ajout de posologies
selDivPoso = function(type, line_id, type_elt){
  if(!type){
    type = "foisPar"+type_elt;
  }
  
  oDivMoment = $('moment'+type_elt+line_id);
  oDivFoisPar = $('foisPar'+type_elt+line_id);
  oDivTousLes = $('tousLes'+type_elt+line_id);
  
  oFormFoisPar = document.forms['addPriseFoisPar'+type_elt+line_id];
  oFormTousLes = document.forms['addPriseTousLes'+type_elt+line_id];
  oFormMoment = document.forms['addPriseMoment'+type_elt+line_id];
   
  // Copie des moments
  if(type == "moment"+type_elt){ 
    if(type == "momentmode_grille"){
      var selectMoments = window.opener.document.moment_unitaire.moment_unitaire_id;  
    } else {
      var selectMoments = document.moment_unitaire.moment_unitaire_id;
    }
  	$A(selectMoments.childNodes).each(function (optgroup) {
 	    oFormMoment.moment_unitaire_id.appendChild(optgroup.cloneNode(true));
	  } );
  }
  
  // Copie des moments
  if(type == "tousLes"+type_elt){ 
    if(type == "tousLesmode_grille"){
      var selectMoments = window.opener.document.moment_unitaire.moment_unitaire_id;  
    } else {
      var selectMoments = document.moment_unitaire.moment_unitaire_id;
    }
  	$A(selectMoments.childNodes).each(function (optgroup) {
 	    oFormTousLes.moment_unitaire_id.appendChild(optgroup.cloneNode(true));
	  } );
  }
  
  if(oFormFoisPar.quantite.value != ''){
    var quantite = oFormFoisPar.quantite.value;
  }
  if(oFormFoisPar.nb_fois.value != ''){
    var nb = oFormFoisPar.nb_fois.value;
  }
  if(oFormTousLes.quantite.value != ''){
    var quantite = oFormTousLes.quantite.value;
  }
  if(oFormTousLes.nb_tous_les.value != ''){
    var nb = oFormTousLes.nb_tous_les.value;
  }  
  if(oFormMoment.quantite.value != ''){
    var quantite = oFormMoment.quantite.value;
  }
  if(quantite){
    oFormFoisPar.quantite.value = quantite;
    oFormTousLes.quantite.value = quantite;
    oFormMoment.quantite.value = quantite;
  }
  if(nb){
    oFormFoisPar.nb_fois.value = nb;
    oFormTousLes.nb_tous_les.value = nb;
  }  
  oDivMoment.hide();
  oDivFoisPar.hide();
  oDivTousLes.hide();
  $(type+line_id).show();
}

reloadPrises = function(prescription_line_id, type){
  url = new Url;
  url.setModuleAction("dPprescription", "httpreq_vw_prises");
  url.addParam("prescription_line_id", prescription_line_id);
  url.addParam("type", type);
  url.requestUpdate('prises-'+type+prescription_line_id, { waitingText: null });
}

onSubmitPrise = function(oForm, type){
  if (!checkForm(oForm)){
    return;
  }
  if (!oForm.object_id.value){
    return;
  }
  return onSubmitFormAjax(oForm, { onComplete:
    function(){
      reloadPrises(oForm.object_id.value, type);
      oForm.quantite.value = 1;
      oForm.moment_unitaire_id.value = "";
  } });
}

</script>

{{assign var=line_id value=$line->_id}}
<div style="margin-top: 5px; margin-bottom: -14px;">
  <form name="ChoixPrise-{{$line->_id}}" action="" method="post" onsubmit="return false">
	  <input name="typePrise" type="radio" value="moment{{$type}}"   onclick="selDivPoso(this.value,'{{$line->_id}}','{{$type}}');" /><label for="typePrise_moment{{$type}}"> Moment</label>
	  <input name="typePrise" type="radio" value="foisPar{{$type}}"  onclick="selDivPoso(this.value,'{{$line->_id}}','{{$type}}');" /><label for="typePrise_foisPar{{$type}}"> x fois par y</label>
	  <input name="typePrise" type="radio" value="tousLes{{$type}}"  onclick="selDivPoso(this.value,'{{$line->_id}}','{{$type}}');" /><label for="typePrise_tousLes{{$type}}"> Tous les x y</label>
	</form>
	</div>
	<script type="text/javascript">Main.add(function() { prepareForm('ChoixPrise-{{$line->_id}}') } )</script>

  <br />
  <div id="foisPar{{$type}}{{$line->_id}}" style="display: none">
		<form name="addPriseFoisPar{{$type}}{{$line->_id}}" action="?" method="post" onsubmit="testPharma({{$line->_id}}); return onSubmitPrise(this,'{{$type}}');">
	    <input type="hidden" name="dosql" value="do_prise_posologie_aed" />
	    <input type="hidden" name="del" value="0" />
	    <input type="hidden" name="m" value="dPprescription" />
	    <input type="hidden" name="prise_posologie_id" value="" />
	    <input type="hidden" name="object_id" value="{{$line->_id}}" />
	    <input type="hidden" name="object_class" value="{{$line->_class_name}}" />

		  {{mb_field object=$prise_posologie field=quantite size=3 increment=1 min=1 form=addPriseFoisPar$type$line_id}}
		  {{if $line->_class_name == "CPrescriptionLineMedicament" && $type != "mode_grille"}}
		  <select name="unite_prise">
		    {{foreach from=$line->_unites_prise item=_unite}}
		      <option value="{{$_unite}}">{{$_unite}}</option>
		    {{/foreach}}
		  </select>
		  {{/if}}
		  {{if $line->_class_name == "CPrescriptionLineElement"}}
		    {{$line->_unite_prise}}
		  {{/if}}
		  {{mb_field object=$prise_posologie field=nb_fois size=3 increment=1 min=1 form=addPriseFoisPar$type$line_id}} fois par 
		  {{mb_field object=$prise_posologie field=unite_fois}}
	  
      {{if $line->_id}}
      <button type="button" class="submit notext" onclick="this.form.onsubmit()">{{tr}}Save{{/tr}}</button>
		  {{/if}}
		</form>
  </div>
<div id="moment{{$type}}{{$line->_id}}" style="display: none">
  <form name="addPriseMoment{{$type}}{{$line->_id}}" action="?" method="post"  onsubmit="testPharma({{$line->_id}}); return onSubmitPrise(this,'{{$type}}');">
	  <input type="hidden" name="dosql" value="do_prise_posologie_aed" />
	  <input type="hidden" name="del" value="0" />
	  <input type="hidden" name="m" value="dPprescription" />
	  <input type="hidden" name="prise_posologie_id" value="" />
	  <input type="hidden" name="object_id" value="{{$line->_id}}" />
	  <input type="hidden" name="object_class" value="{{$line->_class_name}}" />
		  
    {{mb_field object=$prise_posologie field=quantite size=3 increment=1 min=1 form=addPriseMoment$type$line_id}}
	  {{if $line->_class_name == "CPrescriptionLineMedicament" && $type != "mode_grille"}}
		  <select name="unite_prise">
		    {{foreach from=$line->_unites_prise item=_unite}}
		      <option value="{{$_unite}}">{{$_unite}}</option>
		    {{/foreach}}
		  </select>
		  {{/if}}
		  {{if $line->_class_name == "CPrescriptionLineElement"}}
		    {{$line->_unite_prise}}
		  {{/if}}

	  <!-- Selection du moment -->
	  <select name="moment_unitaire_id" style="width: 150px">  
	
	  </select>	
		 
		 
	  {{if $line->_id}}
    <button type="button" class="submit notext" onclick="this.form.onsubmit()">{{tr}}Save{{/tr}}</button>
    {{/if}}
  </form>
</div>
      <div id="tousLes{{$type}}{{$line->_id}}" style="display: none">
      	<form name="addPriseTousLes{{$type}}{{$line->_id}}" action="?" method="post" onsubmit="testPharma({{$line->_id}}); return onSubmitPrise(this,'{{$type}}');" >
	    <input type="hidden" name="dosql" value="do_prise_posologie_aed" />
	    <input type="hidden" name="del" value="0" />
	    <input type="hidden" name="m" value="dPprescription" />
	    <input type="hidden" name="prise_posologie_id" value="" />
	    <input type="hidden" name="object_id" value="{{$line->_id}}" />
        <input type="hidden" name="object_class" value="{{$line->_class_name}}" />
		  

		  {{mb_field object=$prise_posologie field=quantite size=3 increment=1 min=1 form=addPriseTousLes$type$line_id}}
      {{if $line->_class_name == "CPrescriptionLineMedicament"&& $type != "mode_grille"}}
		    <select name="unite_prise">
		    {{foreach from=$line->_unites_prise item=_unite}}
		      <option value="{{$_unite}}">{{$_unite}}</option>
		    {{/foreach}}
		    </select>
		  {{/if}}
		  {{if $line->_class_name == "CPrescriptionLineElement"}}
		    {{$line->_unite_prise}}
		  {{/if}}
         tous les
		  {{mb_field object=$prise_posologie field=nb_tous_les size=3 increment=1 min=1 form=addPriseTousLes$type$line_id}}				   
		  {{mb_field object=$prise_posologie field=unite_tous_les}}
		  (J+{{mb_field object=$prise_posologie field=decalage_prise size=1 increment=1 min="0" form=addPriseTousLes$type$line_id}})
		  <br />
		  <select name="moment_unitaire_id" style="width: 150px">  
	
	    </select>	
	  
      {{if $line->_id}}
      <button type="button" class="submit notext" onclick="this.form.onsubmit()">{{tr}}Save{{/tr}}</button>
      {{/if}}
    </form>  
  </div>
