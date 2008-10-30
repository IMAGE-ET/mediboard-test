{{if $line->_can_select_executant}}
  {{assign var=category_id value=$category->_id}}
  {{if @$executants.externes.$category_id || @$executants.users.$category_id}}
	  <form name="addExecutant-{{$line->_id}}" method="post" action="">
	    <input type="hidden" name="m" value="dPprescription" />
	    <input type="hidden" name="dosql" value="{{$dosql}}" />
	    <input type="hidden" name="del" value="0" />
	    <input type="hidden" name="executant_prescription_line_id" value="" />
	    <input type="hidden" name="user_executant_id" value="" />
	    
	    <input type="hidden" name="{{$line->_spec->key}}" value="{{$line->_id}}" />
	    <!-- Selection d'un executant -->
	    <select class="executant-{{$category_id}}" name="_executant" onchange="submitFormAjax(this.form, 'systemMsg');">
	      <option value="">&mdash; Sélection d'un exécutant</option>
	      {{if @is_array($executants.externes.$category_id)}}
	        <optgroup label="Externes">  
			      {{foreach from=$executants.externes.$category_id item=_ext_executant}}
			      <option value="{{$_ext_executant->_guid}}" {{if $_ext_executant->_id == $line->executant_prescription_line_id}}selected="selected"{{/if}}>{{$_ext_executant->_view}}</option>
			      {{/foreach}}
	        </optgroup>
	      {{/if}}
		    {{if @is_array($executants.users.$category_id)}}
	        <optgroup label="Praticiens">  
			      {{foreach from=$executants.users.$category_id item=_user_executant}}
			      <option value="{{$_user_executant->_guid}}" {{if $_user_executant->_id == $line->user_executant_id}}selected="selected"{{/if}}>{{$_user_executant->_view}}</option>
			      {{/foreach}}
	        </optgroup>
	      {{/if}}
		    </select>
	  </form>
	  <a href="#" style="display:inline" 
	     onclick="preselectExecutant(document.forms['addExecutant-'+{{$line->_id}}]._executant.value,'{{$category_id}}');">
	  <img src="images/icons/updown.gif" alt="Préselectionner" border="0" />
	  </a>
  {{else}}
    Aucun exécutant disponible
  {{/if}}
{{else}}
  {{if $line->executant_prescription_line_id || $line->user_executant_id}}
    {{$line->_ref_executant->_view}}
  {{else}}
    Aucun exécutant sélectionné
  {{/if}}
{{/if}}