{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if !$object->_can->read}}
  <div class="small-info">
    {{tr}}{{$object->_class}}{{/tr}} : {{tr}}access-forbidden{{/tr}}
  </div>
  {{mb_return}}
{{/if}}

{{assign var=evenement_ssr value=$object}}
{{assign var=evenement_ssr_id value=$evenement_ssr->_id}}
{{assign var=unique_id value=""|uniqid}}

{{include file=CMbObject_view.tpl}}

<table class="tbl tooltip">
  {{if $evenement_ssr->sejour_id}}
  
  <!-- Actes CdARRs -->
  <tr>
		{{if count($evenement_ssr->_ref_actes_cdarr)}} 
		  <td class="text">
		    <strong>{{tr}}CEvenementSSR-back-actes_cdarr{{/tr}}</strong> :
		    {{foreach from=$evenement_ssr->_ref_actes_cdarr item=_acte}}
		      {{$_acte}}
		    {{/foreach}}
		  </td>
		{{else}}
    	<td>
    		<div class="small-warning">
    			{{tr}}CEvenementSSR-back-actes_cdarr.empty{{/tr}}
    		</div>
    	</td>
		{{/if}}
  </tr>   

  <!-- Actes CdARRs -->
  <tr>
    {{if count($evenement_ssr->_ref_actes_csarr)}} 
      <td class="text">
        <strong>{{tr}}CEvenementSSR-back-actes_csarr{{/tr}}</strong> :
        {{foreach from=$evenement_ssr->_ref_actes_csarr item=_acte}}
          {{$_acte}}
        {{/foreach}}
      </td>
    {{else}}
      <td>
        <div class="small-warning">
          {{tr}}CEvenementSSR-back-actes_cdarr.empty{{/tr}}
        </div>
      </td>
    {{/if}}
  </tr>   

	{{else}}
  <tr>
    <td class="text">
		  <strong>{{mb_label object=$evenement_ssr field="seance_collective_id"}}</strong>
      <ul>
			{{foreach from=$evenement_ssr->_ref_evenements_seance item=_evenement}}
        <li>{{$_evenement->_ref_sejour->_ref_patient}}</li>
      {{/foreach}}
			</ul>
    </td>
  </tr> 
  {{/if}}
</table>
