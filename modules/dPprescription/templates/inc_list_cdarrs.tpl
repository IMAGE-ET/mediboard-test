{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $element_prescription->_id}}
  <hr />
	<a href="#1" onclick="onSelectCdarr('0','{{$element_prescription->_id}}');" class="button new">
	  Ajouter un code CdARR
	</a>
	<table class="tbl">
		<tr>
      <th colspan="2" class="title">Codes CdARR de '{{$element_prescription->_view}}'</th>
    </tr>
  </table>
	
	<div id="cdarrs-list-content">
		<table class="tbl">	
	    <tr>
	      <th>{{mb_label class=CElementPrescriptionToCdarr field=code}}</th>
	      <th>{{mb_label class=CElementPrescriptionToCdarr field=commentaire}}</th>
	    </tr>
		  {{foreach from=$cdarrs key=type_cdarr item=_cdarrs}}
			<tr>
				<th colspan="2">{{$type_cdarr}}</th>
			</tr>
			{{foreach from=$_cdarrs item=_element_to_cdarr}}
			<tr {{if $element_prescription_to_cdarr_id == $_element_to_cdarr->_id}}class="selected"{{/if}}>
	        <td>
	           <a href="#1" onclick="onSelectCdarr('{{$_element_to_cdarr->_id}}','{{$element_prescription->_id}}',this.up('tr'));">
	            {{mb_value object=$_element_to_cdarr field=code}}
	           </a>
	        </td>
	        <td>{{mb_value object=$_element_to_cdarr field=commentaire}}</td>
	      </tr>
			{{/foreach}}
			{{foreachelse}}
			<tr>
				<td colspan="2">
					Aucun code CdARR
				</td>
			</tr>
			{{/foreach}}	
	  </table>
	</div>
  <script type="text/javascript">
  	//ViewPort.SetAvlHeight('cdarrs-list-content', 0.35);
	</script>
{{/if}}