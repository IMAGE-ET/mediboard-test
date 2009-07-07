{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<li>{{if !$praticien->_id && $perf->_ref_prescription->object_id}}({{$perf->_ref_praticien->_view}}){{/if}}
	<strong>{{$perf->_view}}
	{{if $perf->duree}}
	  pendant {{$perf->duree}} heures
	{{/if}}
	{{if $perf->_protocole}}
	  {{if $perf->decalage_interv != NULL}}
      à partir de I 
      {{if $perf->decalage_interv >= 0}}+{{/if}}
        {{mb_value object=$perf field=decalage_interv}}
         heures
     {{/if}}
	{{else}}
	  {{if $perf->date_debut}}
	    à partir du {{mb_value object=$perf field="date_debut"}}
	    {{if $perf->time_debut}}
	      à {{mb_value object=$perf field="time_debut"}}
	    {{/if}}
	  {{/if}}
	{{/if}}
	</strong>
	<ul>
	  {{foreach from=$perf->_ref_lines item=_line}}
	  <li>
	    {{$_line->_view}}
	  </li>  
	  {{/foreach}}
	</ul>
</li>