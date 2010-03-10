{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
	<td>
		
<div class="draggable" id="{{$sejour->_guid}}">
<script type="text/javascript">Repartition.draggableSejour('{{$sejour->_guid}}')</script>

{{assign var=bilan value=$sejour->_ref_bilan_ssr}}
{{if $bilan->_id}} 
  <span style="float: right;" onmouseover="ObjectTooltip.createEx(this, '{{$bilan->_guid}}')">
	  Bilan
	</span> 
{{/if}}

{{assign var=patient value=$sejour->_ref_patient}}
<span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
  {{$patient}}        
</span> 

<br/> 
<span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
	<strong>pour {{$sejour->_sortie_relative}}j</strong> 
  (arrivée {{$sejour->_entree_relative}}j)
</span>
</div>

  </td>
</tr>
