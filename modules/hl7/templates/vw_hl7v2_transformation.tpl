<script>
  Main.add(function(){
    var tree = new TreeView("hl7-transformation-tree");
    tree.collapseAll();

    var cont = $('hl7-transformation'),
      element = cont.down('input[type=hidden]'),
      tokenField = new TokenField(element);

    cont.select('input[type=checkbox]').invoke('observe', 'click', function(event){
      var elt = Event.element(event);
      tokenField.toggle(elt.value, elt.checked);
    });

  });

</script>

<div id="hl7-transformation" >
  <input type="hidden" name="ignored_fields" value="" />

  <ul id="hl7-transformation-tree" class="hl7-tree">
  {{foreach from=$tree item=_subtree}}
    <li class="address-{{$_subtree.type}}">
      <input type="checkbox" name="address" value="{{$_subtree.fullpath}}" />
      <span class="field-name">{{$_subtree.name}}</span>

      {{mb_include template="inc_hl7v2_transformation_group" component=$_subtree}}
    </li>
  {{/foreach}}
  </ul>
</div>