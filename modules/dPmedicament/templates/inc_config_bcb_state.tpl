{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPmedicament
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
 	<th style="vertical-align: middle">
     <input type="radio" name="{{$m}}[{{$class}}][{{$var}}]" value="{{$dsn}}" {{if $dPconfig.$m.$class.$var == $dsn}}checked="checked"{{/if}}/>
     <label for="{{$m}}[{{$class}}][{{$var}}]_{{$dsn}}">{{$dsn}}</label>
   </th>
   <td style="width: 30%">
		{{assign var=state value=$states.$dsn}}
		{{if $state}}
		<div class="small-{{mb_ternary test=$state.rows_count value=success other=warning}}">
		  version <strong>{{$state.version}}</strong>, 
		  <br>comptant <strong>{{$state.rows_count}}</strong> enregistrements,
		  <br>dernièrement modifiée le <strong>{{$state.last_modif}}</strong>.
		</div>
		{{else}}
		<div class="small-warning">
		  Source BCB non disponible !
		</div>
		{{/if}}
	</td>
</tr>
