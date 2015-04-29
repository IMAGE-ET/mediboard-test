{{mb_script module=forms script=ex_class_editor}}

<button class="new" onclick="ExClassCategory.create()">
  {{tr}}CExClassCategory-title-create{{/tr}}
</button>

<table class="main tbl">
  <tr>
    <th style="width: 1px;"></th>
    <th>{{mb_title class=CExClassCategory field=title}}</th>
    <th>{{mb_title class=CExClassCategory field=description}}</th>
  </tr>

  {{foreach from=$categories item=_category}}
    <tr>
      <td style="background: #{{$_category->color}};"></td>
      <td>
        <a href="#1" onclick="ExClassCategory.edit({{$_category->_id}}); return false;">
          {{mb_value object=$_category field=title}}
        </a>
      </td>
      <td class="compact">{{mb_value object=$_category field=description}}</td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="2" class="empty">{{tr}}CExClassCategory.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>