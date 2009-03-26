<script type="text/javascript">
refreshtransmissions = function(){
  var oForm = document.filter_trans;
  viewTransmissions($V(document.selService.service_id), $V(oForm.user_id), $V(oForm.degre), $V(oForm.observations), $V(oForm.transmissions), true);
}
</script>

<table class="form">
  <tr>
    <th class="category">
      <form name="filter_trans">
	      <span style="float: right">
	        <input type="checkbox" name="observations" onclick="refreshtransmissions();" checked="checked" /> Observations         
          <input type="checkbox" name="transmissions" onclick="refreshtransmissions();" checked="checked" /> Transmissions
        
					{{mb_field object=$filter_trans field=degre defaultOption="&mdash; Tous" onchange="refreshtransmissions();"}}
		      <select name="user_id" onchange="refreshtransmissions();">
		        <option value="">&mdash; Tous les utilisateurs</option>
					  {{foreach from=$users item=_user}}
					  <option class="mediuser" style="border-color: #{{$_user->_ref_function->color}};" value="{{$_user->_id}}" {{if $_user->_id == $filter_trans->user_id}}selected="selected"{{/if}}>{{$_user->_view}}</option>
					  {{/foreach}}
		      </select>
	      </span>
      </form>
      Transmissions des dernieres 24 heures
    </th>
  </tr>
</table>
<div id="_transmissions">
  {{include file="../../dPprescription/templates/inc_vw_transmissions.tpl"}}
</div>