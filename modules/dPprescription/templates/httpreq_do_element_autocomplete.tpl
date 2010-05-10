{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<ul>
  {{foreach from=$elements item=element}}
    <li>
      <small style="display: none;">{{$element->_id}}</small>
      <small style="display: none;">{{$element->_ref_category_prescription->chapitre}}</small>
      
			<strong {{if $element->color}}class="mediuser" style="border-left-color: #{{$element->_color}};"{{/if}}>{{$element->libelle|emphasize:$libelle}}</strong><br />
			{{if !$category_id}}
			  <span style="opacity: 0.7">
			 	  {{$element->_ref_category_prescription}}
			  </span>
			{{else}}
			  <span style="opacity: 0.7">
				  {{$element->description|truncate:60:"...":false}}
			  </span>
      {{/if}}
    </li>
   {{foreachelse}}
    <li style="text-align: left;"><span class="informal">Aucun résultat</span></li>
  {{/foreach}}
</ul>