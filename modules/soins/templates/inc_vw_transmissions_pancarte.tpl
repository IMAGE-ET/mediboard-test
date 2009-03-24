<script type="text/javascript">
refreshtransmissions = function(){
  viewTransmissions($V(document.selService.service_id), $V(document.filter_trans.user_id), $V(document.filter_trans.degre));
}
</script>

<table class="form">
  <tr>
    <th class="category">
      <form name="filter_trans">
	      <span style="float: right">
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