{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $category->_id}}
	<hr />
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
		ViewPort.SetAvlHeight('elements-list-content', 0.45);
  </script>
{{/if}}