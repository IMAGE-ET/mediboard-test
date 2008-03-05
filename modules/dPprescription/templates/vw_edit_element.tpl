<table class="main">
  <tr>    
    <td>
      <form name="selCat" method="get" action="?">
        <input type="hidden" name="tab" value="vw_edit_element" />
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="element_prescription_id" value="" />
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
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;element_prescription_id=0" class="buttonnew">
			  Créer un élément
			</a>
    </td>
  </tr>
  <tr>
    <td class="halfPane">
		  <table class="tbl">
	      <tr>
	        <th colspan="2">Elements de la catégorie {{$category->_view}}</th>
	      </tr>
	      <tr>
	        <th>Libelle</th>
	        <th>Description</th>
	      </tr>
	      {{foreach from=$elements_prescription item=_element}}
	        <tr>
	          <td>
	            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;element_prescription_id={{$_element->_id}}">
	              {{$_element->libelle}}
	            </a>
	          </td>
	          <td>
	            {{$_element->description}}
	          </td>
	        </tr>
	      {{/foreach}}
       </table>
		 </td>
		 <td class="halfPane">
		     <form name="group" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
		     <input type="hidden" name="dosql" value="do_element_prescription_aed" />
		    <input type="hidden" name="element_prescription_id" value="{{$element_prescription->_id}}" />
		     <input type="hidden" name="category_prescription_id" value="{{$category_id}}" />
		     <input type="hidden" name="del" value="0" />
		     <table class="form">
		       <tr>
		         <th class="category" colspan="2">
		         {{if $element_prescription->_id}}
		           <div class="idsante400" id="CElementPrescription-{{$element_prescription->_id}}"></div>
		           <a style="float:right;" href="#" onclick="view_log('CElementPrescription',{{$element_prescription->_id}})">
		             <img src="images/icons/history.gif" alt="historique" />
		           </a>
		           Modification de l'element &lsquo;{{$element_prescription->libelle}}&rsquo;
		         {{else}}
		           Création d'un libellé
		         {{/if}}
		         </th>
		       </tr>
		       <tr>
		         <th>{{mb_label object=$element_prescription field="libelle"}}</th>
		         <td>{{mb_field object=$element_prescription field="libelle"}}</td>
		       </tr>
		       <tr>
		         <th>{{mb_label object=$element_prescription field="description"}}</th>
		         <td>{{mb_field object=$element_prescription field="description"}}</td>
		       </tr>
		       <tr>
		         <td class="button" colspan="2">
		         {{if $element_prescription->_id}}
		           <button class="modify" type="submit" name="modify">
		             {{tr}}Modify{{/tr}}
		           </button>
		           <button class="trash" type="button" name="delete" onclick="confirmDeletion(this.form,{typeName:'l\'element',objName:'{{$element_prescription->libelle|smarty:nodefaults|JSAttribute}}'})">
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