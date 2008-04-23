{{if $perm_edit}}
  {{assign var=category_id value=$category->_id}}
  {{if @$executants.$category_id}}
	  <form name="addExecutant-{{$line->_id}}" method="post" action="">
	    <input type="hidden" name="m" value="dPprescription" />
	    <input type="hidden" name="dosql" value="{{$dosql}}" />
	    <input type="hidden" name="del" value="0" />
	    <input type="hidden" name="{{$line->_tbl_key}}" value="{{$line->_id}}" />
	    <!-- Selection d'un executant -->
	    <select class="executant-{{$category_id}}" name="executant_prescription_line_id" onchange="submitFormAjax(this.form, 'systemMsg');">
	      <option value="">&mdash; Sélection d'un exécutant</option>
	      {{foreach from=$executants.$category_id item=executant}}
	      <option value="{{$executant->_id}}" {{if $executant->_id == $line->executant_prescription_line_id}}selected="selected"{{/if}}>{{$executant->_view}}</option>
	      {{/foreach}}
	    </select>
	  </form>
	  <a href="#" style="display:inline" 
	     onclick="preselectExecutant(document.forms['addExecutant-'+{{$line->_id}}].executant_prescription_line_id.value,'{{$category_id}}');">
	  <img src="images/icons/updown.gif" alt="Préselectionner" border="0" />
	  </a>
  {{else}}
    Aucun exécutant disponible
  {{/if}}
{{else}}
  {{if $line->executant_prescription_line_id}}
    {{$line->_ref_executant->_view}}
  {{else}}
    Aucun exécutant sélectionné
  {{/if}}
{{/if}}