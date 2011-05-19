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
      
			<strong class="mediuser" style="border-left-color: #{{$element->_color}}; white-space: normal;">{{$element->libelle|emphasize:$libelle}}</strong><br />
			{{if !$category_id}}
			  <span class="opacity-70">
			 	  {{$element->_ref_category_prescription}}
			  </span>
			{{else}}
			  <span class="opacity-70">
				  {{$element->description|truncate:60:"...":false}}
			  </span>
      {{/if}}
    </li>
   {{foreachelse}}
    <li style="text-align: left;"><span class="informal">Aucun résultat</span></li>
  {{/foreach}}
</ul>