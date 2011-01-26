<button type="button" class="new" onclick="ExClass.edit('0')">
  {{tr}}CExClass-title-create{{/tr}}
</button>

<table class="main tbl">
  <tr>
    <th class="title" colspan="3">Classes étendues</th>
  </tr>
  <tr>
    <th>{{mb_title class=CExClass field=host_class}}</th>
    <th>{{mb_title class=CExClass field=event}}</th>
    <th>{{mb_title class=CExClass field=name}}</th>
  </tr>
  {{foreach from=$list_ex_class item=_ex_class}}
    <tr>
      <td>
        <a href="#1" onclick="ExClass.edit({{$_ex_class->_id}})">
          <strong>{{tr}}{{$_ex_class->host_class}}{{/tr}}</strong>
        </a>
      </td>
      <td>{{mb_value object=$_ex_class field=event}}</td>
      <td>{{mb_value object=$_ex_class field=name}}</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="4">{{tr}}CExClass.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>
<hr />