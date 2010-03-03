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

Main.add( function(){
  categories_tab = new Control.Tabs.create('categories_tab', true);
});
	
</script>

<table class="main">
	<tr>
	  <td class="halfPane">
			<a href="?m={{$m}}&amp;tab={{$tab}}&amp;category_prescription_id=0" class="button new">
			  Créer une catégorie
			</a>
			<ul class="control_tabs" id="categories_tab">
			{{foreach from=$categories key=chapitre item=_categories}}
			   <li>
			   	<a href="#div_{{$chapitre}}">
			   	  {{tr}}CCategoryPrescription.chapitre.{{$chapitre}}{{/tr}} 
						<small>({{$_categories|@count}} - {{$countElements.$chapitre}})</small>
					</a>
				</li>
			{{/foreach}}
			</ul>
			<hr class="control_tabs" />

			{{foreach from=$categories key=chapitre item=_categories}}
			  <table class="tbl" id="div_{{$chapitre}}" style="display: none;">
				  <tr>
				  	<th>{{mb_label class=CCategoryPrescription field=nom}}</th>
						<th>{{mb_label class=CCategoryPrescription field=group_id}}</th>
          </tr>
				  {{foreach from=$_categories item=_cat}}
					  <tr {{if $category->_id == $_cat->_id}}class="selected"{{/if}} >
				      <td>
			          <a href="?m={{$m}}&amp;tab={{$tab}}&amp;category_prescription_id={{$_cat->_id}}">
			            {{$_cat->nom}} ({{$_cat->_count_elements_prescription}})
			          </a>
			        </td>
			        <td>
			          {{if $_cat->group_id}}
			            {{$_cat->_ref_group->_view}}
			          {{else}}
			            Tous
			          {{/if}}
			        </td>
			      </tr>
			    {{/foreach}}
				</table>
			{{/foreach}}
	  </td> 
    <td class="halfPane">
      <form name="editCategory" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_category_prescription_aed" />
      <input type="hidden" name="category_prescription_id" value="{{$category->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $category->_id}}
          <th class="title text modify" colspan="2">
            {{mb_include module=system template=inc_object_idsante400 object=$category}}
            {{mb_include module=system template=inc_object_history object=$category}}
            Modification de la catégorie &lsquo;{{$category}}&rsquo;
          {{else}}
          <th class="title text" colspan="2">
            Création d'une catégorie
          {{/if}}
          </th>
        </tr>
        <tr>
          <th>{{mb_label object=$category field="chapitre"}}</th>
          <td>{{mb_field object=$category field="chapitre" defaultOption="&mdash; Sélection d'un chapitre"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$category field="nom"}}</th>
          <td>{{mb_field object=$category field="nom"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$category field="description"}}</th>
          <td>{{mb_field object=$category field="description"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$category field="header"}}</th>
          <td>{{mb_field object=$category field="header"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$category field="group_id"}}</th>
          <td>
            <select name="group_id">
              <option value="">Tous</option>
            {{foreach from=$groups item=_group}}
              <option value="{{$_group->_id}}" {{if $category->group_id == $_group->_id}}selected="selected"{{/if}}>{{$_group->_view}}</option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
          {{if $category->_id}}
            <button class="modify" type="submit" name="modify">
              {{tr}}Save{{/tr}}
            </button>
            <button class="trash" type="button" name="delete" onclick="confirmDeletion(this.form,{typeName:'la catégorie',objName:'{{$category->nom|smarty:nodefaults|JSAttribute}}'})">
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
			{{if $category->_id}}
		      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;element_prescription_id=0" class="button new">
        Créer un élément
      </a>
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;mode_duplication=1" class="button new">
        Dupliquer des elements
      </a>
			{{if $mode_duplication}}
       <table class="form">
         <tr>
           <th class="category">Duplication</th>
         </tr>
         <tr>
           <td>
             <form name="duplicationElts" action="?m={{$m}}" method="post">
               <input type="hidden" name="m" value="dPprescription" />
               <input type="hidden" name="dosql" value="do_duplicate_cat_elements_aed" />
               <input type="hidden" name="category_prescription_id" value="{{$category->_id}}" />
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
							</form>
           </td>
         </tr>
       </table>
      {{else}}
			 <form name="editElement" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
         <input type="hidden" name="dosql" value="do_element_prescription_aed" />
         <input type="hidden" name="element_prescription_id" value="{{$element_prescription->_id}}" />
         <input type="hidden" name="category_prescription_id" value="{{$category->_id}}" />
         <input type="hidden" name="del" value="0" />
         <input type="hidden" name="cancelled" value="{{$element_prescription->cancelled}}" />
         <table class="form">
           <tr>
             {{if $element_prescription->_id}}
             <th class="title text modify" colspan="2">
               {{mb_include module=system template=inc_object_idsante400 object=$element_prescription}}
               {{mb_include module=system template=inc_object_history object=$element_prescription}}
               Modification de l'element &lsquo;{{$element_prescription->libelle}}&rsquo;
             {{else}}
             <th class="title text" colspan="2">
               Création d'un élément
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
                 {{tr}}Save{{/tr}}
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
			{{/if}} 
				<table class="tbl">
				<tr>	
					<th colspan="2" class="title text">
	            <button class="search" style="float: right" onclick="toggleCancelled();">
	              Afficher les annulés
	            </button> 
	            Elements de la catégorie '{{$category}}'
	          </th>
	        </tr>
	        <tr>
	          <th class="category">Libelle</th>
	          <th class="category">Description</th>
	        </tr>
	         {{foreach from=$category->_ref_elements_prescription item=_element}}
	          <tr {{if $_element->_id == $element_prescription->_id}}class="selected"{{/if}}
						    {{if $_element->cancelled}}class="cancelled" style="display: none; opacity: 0.5"{{/if}}>
	            <td class="text">
	              <a href="?m={{$m}}&amp;tab={{$tab}}&amp;element_prescription_id={{$_element->_id}}">
	                {{$_element->libelle}}
	              </a>
	            </td>
	            <td class="text">
	              {{$_element->description}}
	            </td>
	          </tr>
	        {{foreachelse}}
	         <tr>
	          <td colspan="2">
	            Aucun element dans cette catégorie
	          </td>
	         </tr>
	        {{/foreach}}
				</table>
			{{/if}}
    </td>
  </tr>
</table>