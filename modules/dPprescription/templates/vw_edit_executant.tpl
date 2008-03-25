<table class="main">
  <tr>    
    <td>
      <form name="selCat" method="get" action="?">
        <input type="hidden" name="tab" value="vw_edit_executant" />
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="executant_id" value="" />
        <select name="category_id" onchange="this.form.submit()">
         <option value="">&mdash; Sélection d'une catégorie</option>
         {{foreach from=$categories key=chapitre item=categories}}
         {{if $categories}}
         <optgroup label="{{tr}}CCategoryPrescription.chapitre.{{$chapitre}}{{/tr}}">
           {{foreach from=$categories item=_category}}
           <option value="{{$_category->_id}}" {{if $category_id == $_category->_id}}selected="selected"{{/if}}>{{$_category->nom}}</option>
           {{/foreach}}
         </optgroup>
         {{/if}}
         {{/foreach}}
       </select>
      </form>
    </td>
  </tr>
  {{if $category_id}}
  <tr>
    <td>
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;executant_prescription_line_id=0" class="buttonnew">
			  Créer un exécutant
			</a>
    </td>
  </tr>
  <tr>
    <td class="halfPane">
		  <table class="tbl">
	      <tr>
	        <th colspan="2">Executants de la catégorie {{$category->_view}}</th>
	      </tr>
	      <tr>
	        <th>Nom</th>
	        <th>Description</th>
	      </tr>
	      {{foreach from=$executants item=_executant}}
	        <tr>
	          <td>
	            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;executant_prescription_line_id={{$_executant->_id}}">
	              {{$_executant->nom}}
	            </a>
	          </td>
	          <td>
	            {{$_executant->description}}
	          </td>
	        </tr>
	      {{/foreach}}
       </table>
		 </td>
		 <td class="halfPane">
		     <form name="group" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
		     <input type="hidden" name="dosql" value="do_executant_prescription_line_aed" />
		    <input type="hidden" name="executant_prescription_line_id" value="{{$executant_prescription_line->_id}}" />
		     <input type="hidden" name="category_prescription_id" value="{{$category_id}}" />
		     <input type="hidden" name="del" value="0" />
		     <table class="form">
		       <tr>
		         <th class="category" colspan="2">
		         {{if $executant_prescription_line->_id}}
		           <div class="idsante400" id="CExecutantPrescriptionLine-{{$executant_prescription_line->_id}}"></div>
		           <a style="float:right;" href="#" onclick="view_log('CExecutantPrescriptionLine',{{$executant_prescription_line->_id}})">
		             <img src="images/icons/history.gif" alt="historique" />
		           </a>
		           Modification de l'executant &lsquo;{{$executant_prescription_line->nom}}&rsquo;
		         {{else}}
		           Création d'un exécutant
		         {{/if}}
		         </th>
		       </tr>
		       <tr>
		         <th>{{mb_label object=$executant_prescription_line field="nom"}}</th>
		         <td>{{mb_field object=$executant_prescription_line field="nom"}}</td>
		       </tr>
		       <tr>
		         <th>{{mb_label object=$executant_prescription_line field="description"}}</th>
		         <td>{{mb_field object=$executant_prescription_line field="description"}}</td>
		       </tr>
		       <tr>
		         <td class="button" colspan="2">
		         {{if $executant_prescription_line->_id}}
		           <button class="modify" type="submit" name="modify">
		             {{tr}}Modify{{/tr}}
		           </button>
		           <button class="trash" type="button" name="delete" onclick="confirmDeletion(this.form,{typeName:'l\'executant',objName:'{{$executant_prescription_line->nom|smarty:nodefaults|JSAttribute}}'})">
		             {{tr}}Delete{{/tr}}
		           </button>
		         {{else}}
		           <button class="new" type="submit" name="create">
		             {{tr}}Create{{/tr}}
		           </button>
		         {{/if}}
		         </td>
		       </tr>
		     </table>
		   </form> 
      {{/if}}
    </td>
  </tr>
</table>