{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
  <th colspan="8">
  <div style="float: right">
    {{if !$_prescription_line_mix->substitute_for_id}}
	  <form name="modifProtocole" method="get" action="?">
	    <input type="hidden" name="m" value="dPprescription" />
	    <input type="hidden" name="tab" value="vw_edit_protocole" />
	    <input type="hidden" name="prescription_id" value="{{$_prescription_line_mix->_ref_prescription->_id}}" />
	     <button class="edit">{{$_prescription_line_mix->_ref_prescription->_view}}</button>
	  </form>
	  {{/if}}
	  </div>
    {{$_prescription_line_mix->_view}}
    </th>
  </tr>
  <tr>
  <td>
    <strong>{{mb_label object=$_prescription_line_mix field="type"}}</strong>:
      {{if $_prescription_line_mix->type}}
        {{mb_value object=$_prescription_line_mix field="type"}}
      {{else}}
        -
      {{/if}}
    </td>
    <td>
      <strong>{{mb_label object=$_prescription_line_mix field="vitesse"}}</strong>:
        {{if $_prescription_line_mix->vitesse}}
      {{mb_value object=$_prescription_line_mix field="vitesse"}} ml/h
      {{else}}
       -
      {{/if}}
    </td>
    <td>
      <strong>{{mb_value object=$_prescription_line_mix field="voie"}}</strong>
    </td>
    <td>
      <strong>{{mb_label object=$_prescription_line_mix field="date_debut"}}</strong>:
      {{if $_prescription_line_mix->decalage_interv != NULL}}
      à I 
      {{if $_prescription_line_mix->decalage_interv >= 0}}+{{/if}}
     
        {{mb_value object=$_prescription_line_mix field=decalage_interv}}
         heures
      {{else}}
        -
      {{/if}}
	  </td>
    <td>
		  <strong>{{mb_label object=$_prescription_line_mix field=duree}}</strong>:
			{{mb_value object=$_prescription_line_mix field=duree}}heures
	  </td>	    
	</td>
  </tr>
  <tr>
    <td colspan="8">
    Produits:<br />
			<ul>
			{{foreach from=$_prescription_line_mix->_ref_lines item=_perf_line}}
			  <li>{{$_perf_line->_view}}</li>
			{{/foreach}}
			</ul>
    </td>
  </tr>

