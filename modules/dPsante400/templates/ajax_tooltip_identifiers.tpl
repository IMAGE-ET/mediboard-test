<table class="tbl">
  {{foreach from=$identifiers item=_identifier}}
  <tr>
    <td>{{mb_value object=$_identifier field=id400}}</td>
    {{if $can->admin && $_identifier->_type}}
      <td><strong>{{mb_value object=$_identifier field=tag}}</strong></td>
    {{/if}}
    <td>
      {{if $_identifier->_type}}
        <span class="idex-special idex-special-{{$_identifier->_type}}">
          {{$_identifier->_type}}
        </span>
      {{else}}
        <strong>{{mb_value object=$_identifier field=tag}}</strong>
      {{/if}}
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="3" class="empty">{{tr}}CIdSante400.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>
