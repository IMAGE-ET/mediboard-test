{{mb_script module=hl7 script=hl7_transformation ajax=true}}

<script>
  Main.add(function(){
    var tree = new TreeView("hl7-transformation-tree");
    tree.collapseAll();
  });
</script>

<div id="hl7-transformation-edit" style="overflow: auto; height: 500px">
  <div id="hl7-transformation">
    <div class="small-info">
      L'arbre ci-dessous se base sur la version <strong>HL7 v.{{$version}} {{if $extension}}({{$extension}}){{/if}}</strong> et
      concerne le message <strong>{{$message}}</strong>. <br />
      Sélectionnez un segment pour sélectionner les champs à exclure. <br />
    </div>

    <ul id="hl7-transformation-tree" class="hl7-tree">
      {{mb_include module=hl7 template=inc_segment_tree}}
    </ul>
  </div>
</div>