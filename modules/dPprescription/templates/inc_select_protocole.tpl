{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<ul>
  {{foreach from=$list key=type item=prots}}
     {{foreach from=$prots item=_prot}}
      <li class="{{if $_prot->praticien_id}}user{{elseif $_prot->function_id}}function{{else}}group{{/if}}">
        <small style="display: none;" class="value">{{$type}}-{{$_prot->_id}}</small>
        <span style="display: none;" class="view">{{$_prot->libelle}}</span>
        {{if $type == "prot"}}Protocole{{else}}Pack{{/if}}:
        <strong>{{$_prot->libelle|upper|replace:$token_search:$token_replace}}</strong>
        <br />
        <span style="opacity: 0.5; font-size: 0.8em; padding-left: 1em;">
				  {{if $type == "prot"}}<a href="#" onclick="Prescription.printPrescription('{{$_prot->_id}}');" style="float: right">(Détail)</a>{{/if}}
					{{foreach from=$_prot->_counts_by_chapitre key=chapitre item=_count_chapitre name=chapitres}}
	        {{if $_count_chapitre}}
	          {{$_count_chapitre}} {{tr}}CPrescription._chapitres.{{$chapitre}}{{/tr}}{{if !$smarty.foreach.chapitres.last}}, {{/if}}
	        {{/if}}
	        {{/foreach}}
			  </span>
      </li>
    {{/foreach}}
  {{/foreach}}
</ul>