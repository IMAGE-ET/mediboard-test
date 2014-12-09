<script>
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
    <th colspan="4" class="title">{{tr}}CItemPrestation.all{{/tr}}</th>
  </tr>
  <tr>
    {{if $is_journaliere}}
    <th class="category narrow" colspan="2">{{mb_label class=CItemPrestation field=rank}}</th>
    {{/if}}
    <th class="category">{{mb_label class=CItemPrestation field=nom}}</th>
    {{if $is_journaliere}}
    <th class="category">{{tr}}CItemPrestation-back-sous_items{{/tr}}</th>
    {{/if}}
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
      <td>
        <a href="#1" onclick="updateSelected('{{$_item->_id}}', 'item'); editItem('{{$_item->_id}}')" class="mediuser"
           style="border-left-color: #{{$_item->color}}">
          {{mb_value object=$_item field=nom}}
        </a>
      </td>
      {{if $is_journaliere}}
        <td style="vertical-align: top;">
          <button type="button" class="add notext" style="float: right;" onclick="editSousItem('', '{{$_item->_id}}')"></button>

          {{foreach from=$_item->_refs_sous_items item=_sous_item}}
            <div>
              <button class="remove notext" onclick="delSousItem('{{$_sous_item->_id}}', '{{$_item->object_class}}', '{{$_item->object_id}}', '{{$_item->_id}}')"></button>
              <a style="display: inline-block;" href="#1" onclick="editSousItem('{{$_sous_item->_id}}')">{{$_sous_item}} ({{$_sous_item->niveau}})</a>
            </div>
          {{/foreach}}
        </td>
      {{/if}}
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="2" class="empty">{{tr}}CItemPrestation.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>