{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
refreshtransmissions = function(){
  var oForm = document.filter_trans;
  viewTransmissions($V(document.selService.service_id), $V(oForm.user_id), $V(oForm._degre), $V(oForm.observations), $V(oForm.transmissions), true);
}

tri_transmissions = function(order_col, order_way){
  var oForm = document.filter_trans;
  viewTransmissions($V(document.selService.service_id), $V(oForm.user_id), $V(oForm._degre), 
  									$V(oForm.observations), $V(oForm.transmissions), true, order_col, order_way);
}

</script>

<table class="form">
  <tr>
    <th class="category">
      <form name="filter_trans">
	      <span style="float: right">
	        <input type="checkbox" name="observations" onclick="refreshtransmissions();" checked="checked" /> Observations         
          <input type="checkbox" name="transmissions" onclick="refreshtransmissions();" checked="checked" /> Transmissions
          <select name="_degre" onchange="refreshtransmissions();">
            <option value="">Toutes</option>
            <option value="urg_normal">Urgentes + normales</option>
            <option value="urg">Urgentes</option>
          </select>
		      <select name="user_id" onchange="refreshtransmissions();">
		        <option value="">&mdash; Tous les utilisateurs</option>
					  {{foreach from=$users item=_user}}
					  <option class="mediuser" style="border-color: #{{$_user->_ref_function->color}};" value="{{$_user->_id}}" {{if $_user->_id == $filter_obs->user_id}}selected="selected"{{/if}}>{{$_user->_view}}</option>
					  {{/foreach}}
		      </select>
	      </span>
      </form>
      Dernieres transmissions (du {{$date_min|date_format:$dPconfig.datetime}} au {{$date_max|date_format:$dPconfig.datetime}})
    </th>
  </tr>
</table>
<div id="_transmissions">
  {{include file="../../dPprescription/templates/inc_vw_transmissions.tpl"}}
</div>