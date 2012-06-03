<script type="text/javascript">
  /* Après avoir réordonné, rafraîchir si nécessaire
    le rank dans le formulaire d'édition d'item */
  Main.add(function() {
    var form = getForm('edit_item');
    if ($V(form.item_prestation_id) == '{{$item->_id}}') {
      $V(form.rank, '{{$item->rank}}');
    }
  });
</script>
{{if $prestation instanceof CPrestationJournaliere}}
  {{assign var=is_journaliere value=1}}
{{else}}
  {{assign var=is_journaliere value=0}}
{{/if}}
<table class="tbl">
  <tr>
    <th colspan="3" class="title">{{tr}}CItemPrestation.all{{/tr}}</th>
  </tr>
  <tr>
    {{if $is_journaliere}}
    <th class="category narrow" colspan="2">{{mb_label class=CItemPrestation field=rank}}</th>
  {{/if}}
    <th class="category">{{mb_label class=CItemPrestation field=nom}}</th>
  </tr>
  {{foreach from=$items item=_item}}
    <tr id="item_{{$_item->_id}}" class="item {{if $_item->_id == $item_id}}selected{{/if}}">
      {{if $is_journaliere}}
        <td>
          <img src="./images/icons/updown.gif" usemap="#map-{{$_item->_id}}" />
          <map name="map-{{$_item->_id}}">
            <area coords="0,0,10,7"  href="#1" onclick="reorderItem('{{$_item->_id}}', 'up', '{{$item_id}}');" />
            <area coords="0,8,10,14" href="#1" onclick="reorderItem('{{$_item->_id}}', 'down', '{{$item_id}}');"/>
          </map>
        </td>
        <td>
          <div class="rank">{{$_item->rank}}</div>          
        </td>
      {{/if}}
      <td><a href="#1" onclick="updateSelected('{{$_item->_id}}', 'item'); editItem('{{$_item->_id}}')">{{mb_value object=$_item field=nom}}</a></td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="2" class="empty">{{tr}}CItemPrestation.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>