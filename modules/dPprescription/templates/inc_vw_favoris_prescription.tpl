{{* $Id:  $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $chapitre == "med"}}
	  <option value="">&mdash; les plus utilisés</option>
	  <optgroup label="Produits les plus utilisés"></optgroup>
	  {{if array_key_exists("medicament", $favoris)}}
	  {{foreach from=$favoris.medicament item=curr_prod}}
	  <option value="{{$curr_prod->code_cip}}">
	    {{$curr_prod->libelle_abrege}} {{$curr_prod->dosage}} ({{$curr_prod->forme}})
	  </option>
	  {{/foreach}}
	  {{/if}}
	  <optgroup label="Injectables les plus utilisés"></optgroup>
	  {{if array_key_exists("injectable", $favoris)}}
	  {{foreach from=$favoris.injectable item=curr_inj}}
	  <option value="{{$curr_inj->code_cip}}">
	    {{$curr_inj->libelle_abrege}} {{$curr_inj->dosage}} ({{$curr_inj->forme}})
	  </option>
	  {{/foreach}}
	  {{/if}}
{{else}}
		<option value="">&mdash; les plus utilisés</option>
		{{if array_key_exists($chapitre, $favoris)}}
		{{foreach from=$favoris.$chapitre item=curr_element}}
		<option value="{{$curr_element->_id}}">
		  {{$curr_element->libelle}}
		</option>
		{{/foreach}}
		{{/if}}
{{/if}}