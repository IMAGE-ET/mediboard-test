{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<a href="#1" onclick="refreshFormCdarr('{{$element_prescription->_id}}');" class="button new">
  Ajouter un code CdARR
</a>

<table class="tbl">
  {{if $element_prescription->_back.cdarrs|@count}}
    <tr>
      <th colspan="2" class="title">Codes CdARR</th>
    </tr>
    <tr>
      <th>{{mb_label class=CElementPrescriptionToCdarr field=code}}</th>
      <th>{{mb_label class=CElementPrescriptionToCdarr field=commentaire}}</th>
    </tr>
	  {{foreach from=$cdarrs key=type_cdarr item=_cdarrs}}
		<tr>
			<th colspan="2">{{$type_cdarr}}</th>
		</tr>
		{{foreach from=$_cdarrs item=_element_to_cdarr}}
		<tr {{if $element_prescription_to_cdarr->_id == $_element_to_cdarr->_id}}class="selected"{{/if}}>
        <td>
           <a href="#1" onclick="refreshFormCdarr('','{{$_element_to_cdarr->_id}}'); this.up('tr').addUniqueClassName('selected')">
            {{mb_value object=$_element_to_cdarr field=code}}
           </a>
        </td>
        <td>{{mb_value object=$_element_to_cdarr field=commentaire}}</td>
      </tr>
		{{/foreach}}
		{{/foreach}}	
  {{/if}}
</table>
