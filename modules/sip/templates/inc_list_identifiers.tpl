<table class="main tbl">
  <tr>
    <th>ID</th>
    <th>Domain</th>
  </tr>

  {{foreach from=$objects item=_patient key=_i}}
    <tr>
      <th colspan="2" class="title">Patient #{{$_i}}</th>
    </tr>
    {{foreach from=$_patient item=_id}}
      <tr>
        <td>{{$_id.id}}</td>
        <td>
          {{foreach from=$_id.domain item=_part name=_domain}}
            <span style="background-color: #ccc">{{$_part}}</span>
            {{if !$smarty.foreach._domain.last}}&{{/if}}
          {{/foreach}}
        </td>
      </tr>
    {{/foreach}}
  {{/foreach}}
</table>