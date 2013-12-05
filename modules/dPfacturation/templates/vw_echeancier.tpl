<button type="button" class="new" onclick="Echeance.edit('{{$echeance->object_id}}', '{{$echeance->object_class}}');">{{tr}}CEcheance-title-create{{/tr}}</button>
<fieldset style="text-align: left;">
  <legend>{{tr}}CEcheance{{/tr}}</legend>
  <table class="main tbl">
    <tr></tr>
    <tr>
      <th class="narrow">{{mb_label object=$echeance field=date}}</th>
      <th>{{mb_label object=$echeance field=montant}}</th>
      <th>{{mb_label object=$echeance field=description}}</th>
      <th class="narrow">Action</th>
    </tr>
    {{foreach from=$echeances item=_echeance}}
      <tr style="text-align:center;">
        <td>{{mb_value object=$_echeance field=date}}</a></td>
        <td>{{mb_value object=$_echeance field=montant}}</td>
        <td>{{mb_value object=$_echeance field=description}}</td>
        <td>
          <button class="edit notext" onclick="Echeance.edit('{{$_echeance->_id}}');"></button>
        </td>
      </tr>
    {{foreachelse}}
    <tr>
      <td class="empty" colspan="4">{{tr}}CEcheance.none{{/tr}}</td>
    </tr>
    {{/foreach}}
  </table>
</fieldset>