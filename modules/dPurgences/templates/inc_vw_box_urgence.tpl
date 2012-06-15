<!-- Formulaire d'un service -->

{{mb_script module="mediusers" script="color_selector" ajax=true}}

<form name="Edit-CBoxUrgence" action="" method="post" onsubmit="return PlanEtage.onSubmit(this)">
  {{mb_class object=$box}}
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$box}}
  
  <table class="form">
    <tr>
      <th colspan="2" class="title" {{if $box->_id}}style="color:#FD4;"{{/if}}>
        {{if $box->_id}}
          Modification du box:<br/> '{{$box->nom}}'
        {{else}}
          Création d'un box d'urgence
        {{/if}}
      </th>
    <tr>
      <th>{{mb_label object=$box field=nom}}</th>
      <td>{{mb_field object=$box field=nom}}</td>
    </tr>       
    <tr>
      <th>{{mb_label object=$box field=description}}</th>
      <td>{{mb_field object=$box field=description}}</td>
    </tr> 
    
    <tr>
      <th>{{mb_label object=$box field=type}}</th>
      <td>{{mb_field object=$box field=type}}</td>
    </tr> 
    
    <tr>
      <script>
        ColorSelector.init = function(){
          this.sForm  = "Edit-CBoxUrgence";
          this.sColor = "color";
          this.sColorView = "color-view";
          this.pop();
        };
      </script>
      <th>{{mb_label object=$box field="color"}}</th>
      <td>
        <span class="color-view" id="color-view" style="background: #{{if $box->color}}{{$box->color}}{{else}}ABE{{/if}};">
          {{tr}}Choose{{/tr}}
        </span>
        <button type="button" class="search notext" onclick="ColorSelector.init()">
          {{tr}}Choose{{/tr}}
        </button>
        {{mb_field object=$box field="color" hidden=1}}
      </td>
    </tr>    
  
    <tr>
      <th>{{mb_label object=$box field=hauteur}}</th>
      <td>{{mb_field object=$box field=hauteur}}</td>
    </tr>
  
    <tr>
      <th>{{mb_label object=$box field=largeur}}</th>
      <td>{{mb_field object=$box field=largeur}}</td>
    </tr>
    
    <tr>
      <td class="button" colspan="2">
        {{if $box->_id}}
        <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
        <button class="trash" type="delete" onclick="confirmDeletion(this.form,{typeName:'le box ',objName: $V(this.form.nom)}, true)">
          {{tr}}Delete{{/tr}}
        </button>
        {{else}}
        <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>