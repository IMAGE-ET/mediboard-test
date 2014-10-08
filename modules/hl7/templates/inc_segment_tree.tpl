{{mb_script module=hl7 script=hl7_transformation ajax=true}}

<script>
  Main.add(function(){
    var tree = new TreeView("hl7-transformation-tree");
    tree.collapseAll();
  });
</script>

<div class="small-info">
  L'arbre ci-dessous se base sur la version <strong>HL7 v.{{$version}} {{if $extension}}({{$extension}}){{/if}}</strong>.
  Sélectionnez un segment pour sélectionner les champs à exclure.
</div>

<div id="hl7-transformation" >
  <ul id="hl7-transformation-tree" class="hl7-tree">
    {{foreach from=$tree item=_subtree}}
      <li>
        <a href="#" onclick="HL7_Transformation.viewFields('{{$actor_guid}}', '{{$_subtree.name}}')"><span class="type">{{$_subtree.name}}</span></a>
        <strong class="field-description">{{$_subtree.description}}</strong>
      </li>
    {{/foreach}}
  </ul>
</div>