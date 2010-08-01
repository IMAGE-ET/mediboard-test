{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=evenement_ssr value=$object}}
{{assign var=evenement_ssr_id value=$evenement_ssr->_id}}
{{assign var=unique_id value=""|uniqid}}

{{include file=CMbObject_view.tpl}}

<table class="tbl tooltip">
  {{if $evenement_ssr->sejour_id}}
		{{if count($evenement_ssr->_ref_actes_cdarr)}} 
		<tr>
		  <td class="text">
		    <strong>{{tr}}CEvenementSSR-back-actes_cdarr{{/tr}}</strong> :
		    {{foreach from=$evenement_ssr->_ref_actes_cdarr item=_acte_cdarr}}
		      {{$_acte_cdarr}}
		    {{/foreach}}
		  </td>
		</tr>
		{{else}}
    <tr>
    	<td>
    		<div class="small-warning">
    			{{tr}}CEvenementSSR-back-actes_cdarr.empty{{/tr}}
    		</div>
    	</td>
    </tr>		
		{{/if}}
	{{else}}
  <tr>
    <td class="text">
		  <strong>{{mb_label object=$evenement_ssr field="seance_collective_id"}}</strong>
      <ul>
			{{foreach from=$evenement_ssr->_ref_evenements_seance item=_evenement}}
        <li>{{$_evenement->_ref_sejour->_ref_patient->_view}}</li>
      {{/foreach}}
			</ul>
    </td>
  </tr> 
  {{/if}}
</table>