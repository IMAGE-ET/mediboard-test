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

submitPrise = function(oForm, type){
  if(!checkForm(oForm)){
    return;
  }
  if(!oForm.object_id.value){
    return;
  }
  submitFormAjax(oForm, 'systemMsg', { onComplete:
    function(){
      reloadPrises(oForm.object_id.value, type);
      oForm.quantite.value = 0;
      oForm.moment_unitaire_id.value = "";
  } });
}


</script>

{{assign var=line_id value=$line->_id}}
  <select name="selShowDivPoso{{$type}}" onchange="selDivPoso(this.value,'{{$line->_id}}','{{$type}}');">
    <option value="">&mdash; Posologies manuelles</option>
    <option value="moment{{$type}}">Moment</option>
    <option value="foisPar{{$type}}">x fois par y</option>
    <option value="tousLes{{$type}}">tous les x y</option>
  </select>
  <br />
  <div id="foisPar{{$type}}{{$line->_id}}">
		<form name="addPriseFoisPar{{$type}}{{$line->_id}}" action="?" method="post" >
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
	      <button type="button" class="submit notext" onclick="testPharma({{$line->_id}}); submitPrise(this.form,'{{$type}}');">Enregistrer</button>
		  {{/if}}
		</form>
  </div>
<div id="moment{{$type}}{{$line->_id}}" style="display: none">
  <form name="addPriseMoment{{$type}}{{$line->_id}}" action="?" method="post" >
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
	  <option value="">&mdash; Sélection du moment</option>
	  {{foreach from=$moments key=type_moment item=_moments}}
	     <optgroup label="{{$type_moment}}">
	     {{foreach from=$_moments item=moment}}
	     <option value="{{$moment->_id}}">{{$moment->_view}}</option>
	     {{/foreach}}
	     </optgroup>
	  {{/foreach}}
	  </select>	
		  
	  {{if $line->_id}}
      <button type="button" class="submit notext" onclick="testPharma({{$line->_id}}); submitPrise(this.form,'{{$type}}');">Enregistrer</button>
    {{/if}}
  </form>
</div>
      <div id="tousLes{{$type}}{{$line->_id}}" style="display: none">
      	<form name="addPriseTousLes{{$type}}{{$line->_id}}" action="?" method="post" >
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
		  
      {{if $line->_id}}
        <button type="button" class="submit notext" onclick="testPharma({{$line->_id}}); submitPrise(this.form,'{{$type}}');">Enregistrer</button>
      {{/if}}
    </form>  
  </div>
