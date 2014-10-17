{{mb_script module=hl7 script=hl7_transformation ajax=true}}

<script>
  Main.add(function(){
    var tree = new TreeView("hl7-transformation-tree");
    tree.collapseAll();

    var cont = $('hl7-transformation'),
      element = getForm("editHL7Transformation").elements.components,
      tokenField = new TokenField(element);

    cont.select('input[type=checkbox]').invoke('observe', 'click', function(event){
      var elt = Event.element(event);
      tokenField.toggle(elt.value, elt.checked);

      var values = tokenField.getValues();
      var container = $('ignored_fields_text').update("");
      values.each(function(v) {
        container.insert(DOM.span({className:'circled'}, v));
      })
    });
  });
</script>

<div class="small-info">
  Sélectionnez les champs à exclure.
  <div id="ignored_fields_text">

  </div>
</div>

<ul id="hl7-transformation-tree" class="hl7-tree">
  {{mb_include module=hl7 template=inc_hl7v2_transformation tree=$tree_segments}}
</ul>

<div>
  <form name="editHL7Transformation" method="post" onsubmit="return onSubmitFormAjax(this)">
    <input type="hidden" name="dosql" value="do_hl7_transformation" />
    <input type="hidden" name="m"     value="hl7" />

    <input type="hidden" name="components" value="" />
    <input type="hidden" name="action"     value="delete" />
    <input type="hidden" name="profil"     value="{{$profil}}" />
    <input type="hidden" name="version"    value="{{$version}}" />
    <input type="hidden" name="extension"  value="{{$extension}}" />
    <input type="hidden" name="message"    value="{{$message}}" />
    <input type="hidden" name="actor_guid" value="{{$actor_guid}}" />

    <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
  </form>
</div>