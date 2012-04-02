<script type="text/javascript">
  Main.add(function() {
    Grossesse.formFrom = getForm("bindFormGrossesse");
  });
</script>
<form name="bindFormGrossesse" method="get">
  <table class="tbl">
    <tr>
      <th colspan="2" class="category">
        Liste des grossesses
      </th>
    </tr>
    {{foreach from=$grossesses item=_grossesse}}
      <tr>
        <td class="narrow">
          <input type="radio" name="unique_grossesse_id"
          {{if !$_grossesse->active}}disabled="disabled"{{/if}}
          {{if $_grossesse->_id == $object->grossesse_id || $_grossesse->_id == $grossesse_id_form}}checked="checked"{{/if}} value="{{$_grossesse->_id}}"
          data-view_grossesse="{{$_grossesse}}"/>
        <td>
          <a href="#1" onclick="Grossesse.editGrossesse('{{$_grossesse->_id}}')">{{$_grossesse}}</a>
        </td>
      </tr>
    {{foreachelse}}
      <tr>
        <td class="empty">{{tr}}CGrossesse.none{{/tr}}</td>
      </tr>
    {{/foreach}}
  </table>
</form>