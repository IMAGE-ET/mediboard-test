<script type="text/javascript">
  Main.add(function() {
    var form = getForm("addItemPrestation");
    var url = new Url("dPhospi", "ajax_lit_liaison_item_autocomplete");
    url.addParam("keywords", form.elements._item_prestation_view);
    url.addParam("where[object_class]", "CPrestationJournaliere");
    url.addParam("lit_id", "{{$lit_id}}");
    url.autoComplete(form.elements._item_prestation_view, null, {
      minChars: 3,
      method: "get",
      select: "view",
      dropdown: true,
      afterUpdateElement: function(field,selected){
        $V(field.form["item_prestation_id"], selected.get("id").split("-")[1]);
        field.form.onsubmit();
      }
    });
  });
</script>


<table class="tbl">
  <tr>
    <th colspan="2">
      <div style="float: left; text-align: left;">
        <form name="addItemPrestation" method="post"
          onsubmit="return onSubmitFormAjax(this, {onComplete: editLitLiaisonItem.curry('{{$lit_id}}')});">
          <input type="hidden" name="m" value="dPhospi"/>
          <input type="hidden" name="dosql" value="do_lit_liaison_item_aed" />
          <input type="text"   name="_item_prestation_view" class="autocomplete" />
          <input type="hidden" name="lit_id" value="{{$lit_id}}" />
          <input type="hidden" name="item_prestation_id" value="" />
        </form>
      </div>
      {{tr}}CItemPrestation{{/tr}}
    </th>
  </tr>
  {{foreach from=$lits_liaisons_items item=_lit_liaison_item}}
    {{assign var=_item_prestation value=$_lit_liaison_item->_ref_item_prestation}}
    <tr>
      <td class="narrow">
        <form name="delLitLiaisonItem" method="post">
          <input type="hidden" name="m" value="dPhospi"/>
          <input type="hidden" name="dosql" value="do_lit_liaison_item_aed" />
          <input type="hidden" name="del" value="1" />
          {{mb_key object=$_lit_liaison_item}}
          <button type="button" class="cancel notext"
          onclick="confirmDeletion(this.form, {
            typeName:'l\'item de prestation',
            objName:'{{$_item_prestation->nom|smarty:nodefaults|JSAttribute}}', ajax: 1},
          { onComplete: editLitLiaisonItem.curry('{{$lit_id}}') })">
          </button>
        </form>
      </td>
      <td>
        {{$_item_prestation->nom}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty">{{tr}}CItemPrestation.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>