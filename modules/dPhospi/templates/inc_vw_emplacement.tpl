<!-- Formulaire d'un emplacement -->
{{mb_script module="mediusers" script="color_selector" ajax=true}}

<form name="Edit-CEmplacement" action="" method="post" onsubmit="return PlanEtage.onSubmit(this)">
  {{mb_class object=$emplacement}}
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$emplacement}}
  
  <table class="form">
    <tr>
      <th colspan="2" class="title" {{if $emplacement->_id}}style="color:#FD4;"{{/if}}>
        {{if $emplacement->_id}}
          Modification de l'emplacement<br/> de la chambre: '{{$emplacement->_ref_chambre->nom}}'
        {{else}}
          Création d'un box d'urgence
        {{/if}}
      </th>
    </tr>
    <tr>
      <th>{{mb_label object=$emplacement field=chambre_id}}</th>
      <td>{{mb_value object=$emplacement field=chambre_id}}</td>
    </tr>  

    <tr>
      <script>
        ColorSelector.init = function(){
          this.sForm  = "Edit-CEmplacement";
          this.sColor = "color";
          this.sColorView = "color-view";
          this.pop();
        };
      </script>
      <th>{{mb_label object=$emplacement field="color"}}</th>
      <td>
        <span class="color-view" id="color-view" style="background: #{{if $emplacement->color}}{{$emplacement->color}}{{else}}DDDDDD{{/if}};">
          {{tr}}Choose{{/tr}}
        </span>
        <button type="button" class="search notext" onclick="ColorSelector.init()">
          {{tr}}Choose{{/tr}}
        </button>
        {{mb_field object=$emplacement field="color" hidden=1}}
      </td>
    </tr>    
  
    <tr>
      <th>{{mb_label object=$emplacement field=hauteur}}</th>
      <td>{{mb_field object=$emplacement field=hauteur increment=true form="Edit-CEmplacement"}}</td>
    </tr>
  
    <tr>
      <th>{{mb_label object=$emplacement field=largeur}}</th>
      <td>{{mb_field object=$emplacement field=largeur increment=true form="Edit-CEmplacement"}}</td>
    </tr>
    
    <tr>
      <td class="button" colspan="2">
        {{if $emplacement->_id}}
        <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
        <button class="trash" type="delete" onclick="confirmDeletion(this.form,{typeName:'l\'emplacement de la chambre',objName: '{{$emplacement->_ref_chambre->nom}}'}, true)">
          {{tr}}Delete{{/tr}}
        </button>
        {{else}}
        <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>