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
		  Quantité: 
		  {{mb_field object=$prise_posologie field=quantite size=3 increment=1 form=addPriseFoisPar$type$line_id}}
		  {{if $line->_class_name == "CPrescriptionLineMedicament"}}
		    {{$line->_unite_prise}}(s)
		  {{else}}
		    soins
		  {{/if}}
		  {{mb_field object=$prise_posologie field=nb_fois size=3 increment=1 form=addPriseFoisPar$type$line_id}} fois par 
		  {{mb_field object=$prise_posologie field=unite_fois}}
	    <button type="button" class="submit notext" onclick="submitPrise(this.form,'{{$type}}');">Enregistrer</button>
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
		  
	  Quantité: 
	  {{mb_field object=$prise_posologie field=quantite size=3 increment=1 form=addPriseMoment$type$line_id}}
	  {{if $line->_class_name == "CPrescriptionLineMedicament"}}
		    {{$line->_unite_prise}}(s)
		  {{else}}
		    soins
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
    <button type="button" class="submit notext" onclick="submitPrise(this.form,'{{$type}}');">Enregistrer</button>
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
		  
        Quantité: 
		  {{mb_field object=$prise_posologie field=quantite size=3 increment=1 form=addPriseTousLes$type$line_id}}
      {{if $line->_class_name == "CPrescriptionLineMedicament"}}
		    {{$line->_unite_prise}}(s)
		  {{else}}
		    soins
		  {{/if}}
         tous les
		  {{mb_field object=$prise_posologie field=nb_tous_les size=3 increment=1 form=addPriseTousLes$type$line_id}}				   
		  {{mb_field object=$prise_posologie field=unite_tous_les}}
      <button type="button" class="submit notext" onclick="submitPrise(this.form,'{{$type}}');">Enregistrer</button>
    </form>  
  </div>
