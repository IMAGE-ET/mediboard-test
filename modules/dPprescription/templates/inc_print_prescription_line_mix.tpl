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
	  pendant {{$perf->duree}} {{$perf->unite_duree}}(s)
	{{/if}}
	{{if $perf->_protocole}}
	  {{if $perf->jour_decalage}}
			 à partir de
	     {{mb_value object=$perf field="jour_decalage"}} 
	     {{if $perf->decalage_line >= 0}}+{{/if}}
	     {{mb_value object=$perf field=decalage_line}} {{mb_value object=$perf field=unite_decalage}}
		 {{/if}}
		 
     {{if $perf->jour_decalage_fin}}
			 jusqu'à  
	     {{mb_value object=$perf field="jour_decalage_fin"}} 
	     {{if $perf->decalage_line_fin >= 0}}+{{/if}}
	     {{mb_value object=$perf field=decalage_line_fin}} {{mb_value object=$perf field=unite_decalage_fin}}
		 {{/if}}
	{{else}}
	  {{if $perf->date_debut}}
	    à partir du {{mb_value object=$perf field="date_debut"}}
	    {{if $perf->time_debut}}
	      à {{mb_value object=$perf field="time_debut"}}
	    {{/if}}
	  {{/if}}
	{{/if}}
	</strong><br/>
	{{if $perf->commentaire}}
    ({{$perf->commentaire|nl2br}})
  {{/if}}
	<ul>
	  {{foreach from=$perf->_ref_lines item=_line}}
	  <li>
	    <strong>
        {{$_line->_view}}
      </strong>: {{$_line->_posologie}}
	  </li>  
	  {{/foreach}}
	</ul>
</li>