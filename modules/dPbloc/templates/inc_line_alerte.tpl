<tr {{if $is_alerte}}id="{{$_alerte->_guid}}"{{/if}}>
  <td>{{$_operation->_ref_plageop->date|date_format:$dPconfig.date}}</td>
  <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_operation->_ref_chir}}</td>
  <td>{{$_operation->_ref_salle->_view}}</td>
  <td class="text">
    {{mb_include module=dPplanningOp template=inc_vw_operation}}
    <br />
    {{if $is_alerte}}
    {{$_alerte->comments}}
    <br />
    <form name="removeFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="system" />
      <input type="hidden" name="dosql" value="do_alert_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="alert_id" value="{{$_alerte->_id}}" />
      <input type="hidden" name="tag" value="{{$_alerte->tag}}" />
      <input type="hidden" name="level" value="{{$_alerte->level}}" />
      <input type="hidden" name="comments" value="{{$_alerte->comments}}" />
      <input type="hidden" name="handled" value="1" />
      <button type="button" class="tick" onclick="submitFormAjax(this.form, 'systemMsg'); $('{{$_alerte->_guid}}').hide();">Traité</button>
    </form>
    {{/if}}
  </td>
</tr>