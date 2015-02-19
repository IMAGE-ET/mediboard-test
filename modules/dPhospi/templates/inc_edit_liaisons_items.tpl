<script type="text/javascript">
  Main.add(function() {
    var form = getForm("addItemPrestation{{$lit_id}}");
    var input = form.elements._item_prestation_view;
    var url = new Url("dPhospi", "ajax_lit_liaison_item_autocomplete");
    url.addParam("keywords", input );
    url.addParam("where[object_class]", "CPrestationJournaliere");
    url.addParam("lit_id", "{{$lit_id}}");
    url.autoComplete(input, null, {
      minChars: 3,
      method: "get",
      select: "view",
      dropdown: true,
      afterUpdateElement: function(field,selected){
        var id = selected.get("id");
        if (!id) {
          return;
        }
        $V(field.form["item_prestation_id"], id.split("-")[1]);
        field.form.onsubmit();
      }
    });
  });
</script>


<form name="addItemPrestation{{$lit_id}}" method="post"
      onsubmit="return onSubmitFormAjax(this, {onComplete: Infrastructure.editLitLiaisonItem.curry('{{$lit_id}}')});">
  <input type="hidden" name="m" value="dPhospi"/>
  <input type="hidden" name="dosql" value="do_lit_liaison_item_aed" />
  <input type="text"   name="_item_prestation_view" class="autocomplete" placeholder="&mdash; Choisir une prestation"/>
  <input type="hidden" name="lit_id" value="{{$lit_id}}" />
  <input type="hidden" name="item_prestation_id" value="" />
</form>

<ul id="itemTags" class="tags" style="float: none">
  {{foreach from=$lits_liaisons_items item=_lit_liaison_item}}
    {{assign var=_item_prestation value=$_lit_liaison_item->_ref_item_prestation}}
    <li class="tag">
      <form name="delLitLiaisonItem-{{$_item_prestation->_id}}" method="post">
        <input type="hidden" name="m" value="dPhospi"/>
        <input type="hidden" name="dosql" value="do_lit_liaison_item_aed" />
        <input type="hidden" name="del" value="1" />
        {{mb_key object=$_lit_liaison_item}}
        <button type="button" class="delete"  style="display: inline-block !important;"
                onclick="confirmDeletion(this.form, {
                  typeName:'l\'item de prestation',
                  objName:'{{$_item_prestation->_shortview|smarty:nodefaults|JSAttribute}}', ajax: 1},
                  { onComplete: Infrastructure.editLitLiaisonItem.curry('{{$lit_id}}') })">
        </button>
      </form>
      <span>{{$_item_prestation->_shortview}}<br/></span>
    </li>
  {{foreachelse}}
    <span class="empty">{{tr}}CItemPrestation.none{{/tr}}</span>
  {{/foreach}}
</ul>
