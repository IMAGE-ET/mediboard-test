<table class="tbl">
  {{foreach from=$identifiers item=_identifier}}
  <tr>
    <td><strong>{{mb_value object=$_identifier field=tag}}</strong></td>
    <td>{{mb_value object=$_identifier field=id400}}</td>
    {{if $_identifier->_type}}
      <td>
        <span class="idex-special idex-special-{{$_identifier->_type}}">
          {{$_identifier->_type}}
        </span>
      </td>
    {{/if}}
  </tr>
  {{foreachelse}}
  <tr>
    <td class="empty">{{tr}}CIdSante400.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>
