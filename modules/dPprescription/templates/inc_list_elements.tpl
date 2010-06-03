{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $category->_id}}
	<script type="text/javascript">
		Main.add( function(){
		  elements_executants_tab = new Control.Tabs.create('elements_executants_tab');
		});
	</script>
	
	<hr />	
	<ul class="control_tabs small" id="elements_executants_tab">
	  <li>
	   	<a href="#elements">
	      Eléments <small>({{$category->_ref_elements_prescription|@count}})</small>
			</a>
		</li>
		<li>
			<a href="#executants_function">
				Exécutants - Fonctions <small>({{$associations|@count}})</small>
			</a>
		</li>
    <li>
    	<a href="#executants">
    		Exécutants <small>({{$executants|@count}})</small>
			</a>
		</li>
	</ul>
	<hr class="control_tabs" />

  <div id="elements">
		<a href="#1" onclick="onSelectElement('0','{{$category->_id}}');" class="button new">
		  Créer un élément
		</a>
		<a href="#1" onclick="refreshFormElement('','{{$category->_id}}',true);" class="button new">
		  Dupliquer des elements
		</a>
		<table class="tbl">
		  <tr>  
		    <th colspan="10" class="title text">
		        <button class="search" style="float: right" onclick="$$('.cancelled').invoke('toggle')">
		          Afficher les annulés
		        </button> 
		        Elements de la catégorie '{{$category}}'
		      </th>
		    </tr>
		</table>
			
	  <div id="elements-list-content">
			<table class="tbl">
				<tr>
		      <th>Libelle</th>
		      <th>Description</th>
		      {{if @$modules.ssr->mod_active}}
		      <th>CdARR</th>
		      {{/if}}
		      <th></th>
		    </tr>
				{{foreach from=$category->_ref_elements_prescription item=_element}}
		    <tr class="
		       {{if $_element->_id == $element_prescription_id}}selected{{/if}}
		       {{if $_element->cancelled}}cancelled{{/if}}"
					 {{if $_element->cancelled}}style="display: none; opacity: 0.6"{{/if}}>
		      <td class="text">
		        <a href="#1" onclick="onSelectElement('{{$_element->_id}}', '{{$category->_id}}', this.up('tr'));">
		          {{$_element->libelle}}
		        </a>
		      </td>
		      <td class="text">
		        {{$_element->description}}
		      </td>
		      {{if  @$modules.ssr->mod_active}}
		      <td style="text-align: right">
		      	{{if $_element->_count.cdarrs}} 
						{{$_element->_count.cdarrs}}
({{foreach from=$_element->_count_cdarr_by_type key=_type_cdarr item=_count_elt name=counts_cdarr}}
{{$_count_elt}} {{$_type_cdarr}}{{if !$smarty.foreach.counts_cdarr.last}},{{/if}}
{{/foreach}})
		      	{{/if}}
		      </td>
		      {{/if}}
		      <td style="width: 1em; {{if $_element->color}}background-color: #{{$_element->color}}{{/if}}">
		    </tr>
		    {{foreachelse}}
		     <tr>
		      <td colspan="4">
		        Aucun element dans cette catégorie
		      </td>
		     </tr>
		    {{/foreach}}
		  </table>
		</div>
		<script type="text/javascript">
			ViewPort.SetAvlHeight('elements-list-content', 0.5);
	  </script>
	</div>
	
	<div id="executants_function">
		<a href="#1" onclick="refreshFormExecutantFunction('0','{{$category->_id}}');" class="button new">
      Créer un éxécutant
    </a>
		<table class="tbl">
      <tr>
        <th class="title">Exécutants de la catégorie '{{$category->_view}}'</th>
      </tr>
			<tr>
				<th>
					{{mb_label class=CFunctionCategoryPrescription field=function_id}}
        </th>
			</tr>	
	    {{foreach from=$associations item=_association}} 
        <tr id="tr-{{$_association->_guid}}">
          <td>
          	<a href="#1" onclick="refreshFormExecutantFunction('{{$_association->_id}}','{{$category->_id}}'); this.up('tr').addUniqueClassName('selected');">
             {{$_association->_ref_function->_view}}
            </a>
          </td>
        </tr>
      {{foreachelse}}
			  <tr>
			  	<td>Aucun exécutant</td>
			  </tr>
			{{/foreach}}
    </table>
	</div>  
  
	<div id="executants">
		<a href="#1" onclick="refreshFormExecutant('0','{{$category->_id}}');" class="button new">
      Créer un éxécutant
    </a>
		<table class="tbl">
      <tr>
        <th colspan="2" class="title">Executants de la catégorie '{{$category->_view}}'</th>
      </tr>
      <tr>
        <th>Nom</th>
        <th>Description</th>
      </tr>
      {{foreach from=$executants item=_executant}}
        <tr id="tr-{{$_executant->_guid}}">
          <td>
            <a href="#1" onclick="refreshFormExecutant('{{$_executant->_id}}','{{$category->_id}}'); this.up('tr').addUniqueClassName('selected');">
              {{$_executant->nom}}
            </a>
          </td>
          <td>
            {{$_executant->description}}
          </td>
        </tr>
      {{foreachelse}}
			  <tr>
			  	<td colspan="2">Aucun exécutant</td>
			  </tr>
			{{/foreach}}
     </table>
	</div>
{{/if}}