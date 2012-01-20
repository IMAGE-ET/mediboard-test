<table class="main">
  {{foreach from=$processes item=_process}}
  <tr>
    <td>
      {{$_process.port}}
    </td>
  </tr>
  {{/foreach}}
</table>