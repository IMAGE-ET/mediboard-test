{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
	
toggleCancelled = function(){
	$$(".cancelled").invoke("toggle");
}
	
</script>

<table class="main">
  <tr>    
    <td>
      <form name="selCat" method="get" action="?">
        <input type="hidden" name="tab" value="vw_edit_element" />
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="element_prescription_id" value="" />
        <select name="category_id" onchange="this.form.submit()">
         <option value="">&mdash; Sélection d'une catégorie</option>
         {{foreach from=$categories key=chapitre item=_categories}}
         {{$chapitre}}
         {{if $categories}}
         <optgroup label="{{tr}}CCategoryPrescription.chapitre.{{$chapitre}}{{/tr}}">
           {{foreach from=$_categories item=_category}}
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
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;element_prescription_id=0" class="button new">
			  Créer un élément
			</a>
			<a href="?m={{$m}}&amp;tab={{$tab}}&amp;mode_duplication=1" class="button new">
			  Dupliquer des elements
			</a>
    </td>
  </tr>
  <tr>
    <td class="halfPane">
		  <table class="tbl">
	      <tr>
	        <th colspan="2">
	          <span style="float: right">
              Afficher les annulés <input type="checkbox" id="show_canceled" onclick="toggleCancelled();" />
            </span>	
						Elements de la catégorie {{$category->_view}}
          </th>
	      </tr>
	      <tr>
	        <th>Libelle</th>
	        <th>Description</th>
	      </tr>
	      {{foreach from=$elements_prescription item=_element}}
	        <tr {{if $_element->cancelled}}class="cancelled" style="display: none; opacity: 0.5"{{/if}}>
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
		 {{if $mode_duplication}}
		 <td class="halfPane">
		   <table class="form">
		     <tr>
		       <th class="category">Duplication</th>
		     </tr>
		     <tr>
		       <td>
		         <form name="duplicationElts" action="?" method="post">
		           <input type="hidden" name="m" value="dPprescription" />
		           <input type="hidden" name="dosql" value="do_duplicate_cat_elements_aed" />
		           <input type="hidden" name="category_id" value="{{$category_id}}" />
		           Dupliquer les elements de <strong>{{$category->_view}}</strong> vers
		           <select name="category_dest_id">
				         <option value="">&mdash; Sélection d'une catégorie</option>
				         {{foreach from=$categories key=chapitre item=_categories}}
				         {{if $categories}}
				         <optgroup label="{{tr}}CCategoryPrescription.chapitre.{{$chapitre}}{{/tr}}">
				           {{foreach from=$_categories item=_category}}
				           <option value="{{$_category->_id}}" {{if $_category->_id == $category->_id}}disabled="disabled"{{/if}}>{{$_category->nom}}</option>
				           {{/foreach}}
				         </optgroup>
				         {{/if}}
				         {{/foreach}}
				       </select>
				       <button type="button" class="submit" onclick="if(this.form.category_dest_id.value) { this.form.submit(); }">Valider</button>
		       </td>
		     </tr>
		   </table>
		 </td>
		 {{else}}
		 <td class="halfPane">
		   <form name="group" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
		     <input type="hidden" name="dosql" value="do_element_prescription_aed" />
		     <input type="hidden" name="element_prescription_id" value="{{$element_prescription->_id}}" />
		     <input type="hidden" name="category_prescription_id" value="{{$category_id}}" />
		     <input type="hidden" name="del" value="0" />
				 <input type="hidden" name="cancelled" value="{{$element_prescription->cancelled}}" />
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
							 {{if $element_prescription->cancelled}}
							   <button class="tick" type="submit" name="restore" onclick="$V(this.form.cancelled, '0');">
                   {{tr}}Restore{{/tr}}
                 </button>
							 {{else}}
			           <button class="cancel" type="submit" name="cancel" onclick="$V(this.form.cancelled, '1');">
	                 {{tr}}Cancel{{/tr}}
	               </button>
					     {{/if}}
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
      </td>
     {{/if}}
  {{/if}}
  </tr>
</table>