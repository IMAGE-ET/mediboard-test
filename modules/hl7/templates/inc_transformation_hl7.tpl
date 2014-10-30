{{mb_script module=hl7 script=hl7_transformation ajax=true}}

<script>
  Main.add(function(){
    var tree = new TreeView("hl7-transformation-tree");
    tree.collapseAll();
  });
</script>

<div id="hl7-transformation-edit">
  <div id="hl7-transformation">
    <div class="small-info">
      L'arbre ci-dessous se base sur la version <strong>HL7 v.{{$version}} {{if $extension}}({{$extension}}){{/if}}</strong> et
      concerne le message <strong>{{$message}}</strong>. <br />
      Sélectionnez un segment pour sélectionner les champs à exclure. <br />

      {{if $actor->_ref_hl7_transformations|@count > 0}}
        Les champs déjà altérés sont :
      {{/if}}
      {{foreach from=$actor->_ref_hl7_transformations item=_hl7_transformation}}
        <span class="circled">
          <span class="hl7-transformation-{{$_hl7_transformation->action}}" title="{{mb_value object=$_hl7_transformation field="action"}}"></span>
  {{$_hl7_transformation->component}}
        </span>
      {{/foreach}}
    </div>

    <ul id="hl7-transformation-tree" class="hl7-tree">
      {{mb_include module=hl7 template=inc_segment_tree}}
    </ul>
  </div>
</div>