{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>  
    <th colspan="10" class="title text">
        <button class="search" style="float: right" onclick="$$('.cancelled').invoke('toggle')">
          Afficher les annulés
        </button> 
        Elements de la catégorie '{{$category}}'
      </th>
    </tr>

    <tr>
      <th>Libelle</th>
      <th>Description</th>
      {{if  @$modules.ssr->mod_active}}
      <th>CdARR</th>
			{{/if}}
      <th></th>
    </tr>
   {{foreach from=$category->_ref_elements_prescription item=_element}}
    <tr class="
       {{if $_element->_id == $element_prescription->_id}}selected{{/if}}
       {{if $_element->cancelled}}cancelled{{/if}}"
			 {{if $_element->cancelled}}style="display: none; opacity: 0.6"{{/if}}
			 >
      <td class="text">
        <a href="?m={{$m}}&amp;tab={{$tab}}&amp;element_prescription_id={{$_element->_id}}&amp;element_prescription_to_cdarr_id=0">
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
      <td colspan="3">
        Aucun element dans cette catégorie
      </td>
     </tr>
    {{/foreach}}
  </table>
