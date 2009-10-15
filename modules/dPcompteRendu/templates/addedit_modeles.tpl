<script type="text/javascript">

function popFile(objectClass, objectId, elementClass, elementId, sfn){
  var url = new Url;
  url.ViewFilePopup(objectClass, objectId, elementClass, elementId, sfn);
}

function copie(oForm) {
  oForm = oForm || document.editFrm;
  
  {{if $droit}}
    if(confirm('Vous avez deja accès à ce modèle, souhaitez-vous confirmer la copie de ce modèle ?')){
      oForm.compte_rendu_id.value = "";
      
      {{if $isPraticien}}
      oForm.chir_id.value = "{{$user_id}}";
      oForm.function_id.value = "";
      {{/if}}
      
      oForm.nom.value = "Copie de "+oForm.nom.value;
      oForm.onsubmit(); 
    }
  {{else}}
    oForm.compte_rendu_id.value = "";
    oForm.chir_id.value = "{{$user_id}}";
    oForm.nom.value = "Copie de "+oForm.nom.value;
    oForm.onsubmit();
  {{/if}}
}

function nouveau() {
  var url = new Url;
  url.setModuleTab("dPcompteRendu", "addedit_modeles");
  url.addParam("compte_rendu_id", "0");
  url.redirect();
}

// Taleau des categories en fonction de la classe du compte rendu
var listObjectClass = {{$listObjectClass|@json}};
var aTraducClass = {{$listObjectAffichage|@json}};

function loadObjectClass(value) {
  var form = document.editFrm;
  var select = $(form.elements.object_class);
  var children = select.childElements();
  
  if (children.length > 0)
    children[0].nextSiblings().invoke('remove');
  
  // Insert new ones
  $H(listObjectClass).each(function(pair){
    select.insert(new Element('option', {value: pair.key, selected: pair.key == value}).update(aTraducClass[pair.key]));
  });
  
  // Check null position
  select.fire("ui:change");
 
  loadCategory();
}

function loadCategory(value) {
  var form = document.editFrm;
  var select = $(form.elements.file_category_id);
  var children = select.childElements();
  
  if (children.length > 0)
    children[0].nextSiblings().invoke('remove');
  
  // Insert new ones
  $H(listObjectClass[form.elements.object_class.value]).each(function(pair){
    select.insert(new Element('option', {value: pair.key, selected: pair.key == value}).update(pair.value));
  });
}

function submitCompteRendu(){
  (function(){
    var form = getForm("editFrm");
    if(checkForm(form) && User.id) {
      form.submit();
    }
  }).defer();
}

// Catches Ctrl+s and Command+s
document.observe('keydown', function(e){
  var keycode = Event.key(e);
  if(keycode == 83 && (e.ctrlKey || e.metaKey)){
    submitCompteRendu();
    Event.stop(e);
  }
});

Main.add(function () {
  loadObjectClass('{{$compte_rendu->object_class}}');
  loadCategory('{{$compte_rendu->file_category_id}}');
});

</script>

<form name="editFrm" action="?m={{$m}}" method="post" 
 onsubmit="Url.ping({onComplete: submitCompteRendu}); return false;"
 class="{{$compte_rendu->_spec}}">

<table class="main">
  <tr>
    <td style="width: 0.1%;">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_modele_aed" />
      {{mb_key object=$compte_rendu}}
      {{mb_field object=$compte_rendu field="object_id" hidden=1 prop=""}}
      {{if $compte_rendu->compte_rendu_id}}
      <button class="new" type="button" onclick="nouveau()">
        Créer un modèle
      </button>
      {{/if}}
      <table class="form">
        <tr>
          <th class="category" colspan="2">
            {{if $compte_rendu->_id}}
			      {{mb_include module=system template=inc_object_idsante400 object=$compte_rendu}}
			      {{mb_include module=system template=inc_object_history object=$compte_rendu}}
            {{/if}}
            Informations sur le modèle
          </th>
        </tr>
        
        <tr>
          <th>{{mb_label object=$compte_rendu field="nom"}}</th>
          <td>
          {{if $droit}}
            {{mb_field object=$compte_rendu field="nom"}}
          {{else}}
            {{mb_field object=$compte_rendu field="nom" readonly="readonly"}}
          {{/if}}
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$compte_rendu field="group_id"}}</th>
          <td>
            {{if !$droit}}
               <input type="hidden" name="group_id" />
            {{/if}}
            <select {{if !$droit}}disabled='disabled'{{/if}} name="group_id" class="{{$compte_rendu->_props.group_id}}">
              <option value="">&mdash; Associer à un établissement</option>
              {{foreach from=$listEtab item=curr_etab}}
              <option value="{{$curr_etab->_id}}" {{if $curr_etab->_id == $compte_rendu->group_id}} selected="selected" {{/if}}>
              {{$curr_etab->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>

        <tr>
          <th>{{mb_label object=$compte_rendu field="function_id"}}</th>
          <td>
            {{if !$droit}}
               <input type="hidden" name="function_id" />
            {{/if}}
            <select {{if !$droit}}disabled='disabled'{{/if}} name="function_id" class="{{$compte_rendu->_props.function_id}}">
              <option value="">&mdash; Associer à une fonction</option>
              {{foreach from=$listFunc item=curr_func}}
              <option class="mediuser" style="border-color: #{{$curr_func->color}};" value="{{$curr_func->_id}}" {{if $curr_func->_id == $compte_rendu->function_id}} selected="selected" {{/if}}>
              {{$curr_func->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
                
        <tr>
          <th>{{mb_label object=$compte_rendu field="chir_id"}}</th>
          <td>
            {{if !$droit}}
              <input type="hidden" name="chir_id" value="{{$mediuser->_id}}" />
            {{/if}}
            <select {{if !$droit}}disabled='disabled'{{/if}} name="chir_id" class="{{$compte_rendu->_props.chir_id}}">
              <option value="">&mdash; Associer à un utilisateur</option>
              {{foreach from=$listPrat item=curr_prat}}
              <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->_id}}" {{if $curr_prat->_id == $compte_rendu->chir_id}} selected="selected" {{/if}}>
              {{$curr_prat->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$compte_rendu field=type}}</th>
          <td>
            {{if $droit}}
              {{mb_field object=$compte_rendu field=type onchange="updateType()"}}
            {{else}}
              {{mb_field object=$compte_rendu field=type disabled="disabled"}}
            {{/if}}
          
            <script type="text/javascript">
            function updateType() {
              var oForm = document.editFrm;
              var bBody = oForm.type.value == "body";

              // Height
              $("height").setVisible(!bBody);
              if (bBody) $V(oForm.height, '');

              // Footers
              var oFooter = $("footers");
              if (oFooter) {
                oFooter.setVisible(bBody);
                if (!bBody) $V(oForm.footer_id, '');
              }

              // Headers
              var oHeader = $("headers");
              if (oHeader) {
                oHeader.setVisible(bBody);
                if (!bBody) $V(oForm.header_id, '');
              }
            }
            
            Main.add(updateType);
            </script>
          
          </td>
        </tr>
        
        <tr id="height">
          <th>{{mb_label object=$compte_rendu field=height}}</th>
          <td>
          {{if $droit}}
            {{mb_field object=$compte_rendu field=height}}
          {{else}}
            {{mb_field object=$compte_rendu field=height readonly="readonly"}}
          {{/if}}
          </td>
        </tr>
          
        {{if $footers|@count}}
        <tr id="footers">
          <th>{{mb_label object=$compte_rendu field=footer_id}}</th>
          <td>
            <select name="footer_id" class="{{$compte_rendu->_props.footer_id}}" {{if !$droit}}disabled="disabled"{{/if}}>
              <option value="">&mdash; Choisir un pied-de-page</option>
              {{foreach from=$footers item=footersByOwner key=owner}}
              <optgroup label="{{tr}}CCompteRendu._owner.{{$owner}}{{/tr}}">
                {{foreach from=$footersByOwner item=_footer}}
                <option value="{{$_footer->_id}}" {{if $compte_rendu->footer_id == $_footer->_id}}selected="selected"{{/if}}>{{$_footer->nom}}</option>
                {{foreachelse}}
                <option value="" disabled="disabled">{{tr}}None{{/tr}}</option>
                {{/foreach}}
              </optgroup>
              {{/foreach}}
            </select>
          </td>
        </tr>
        {{/if}}

        {{if $headers|@count}}
        <tr id="headers">
          <th>{{mb_label object=$compte_rendu field=header_id}}</th>
          <td>
            <select name="header_id" class="{{$compte_rendu->_props.header_id}}" {{if !$droit}}disabled="disabled"{{/if}}>
              <option value="">&mdash; Choisir une en-tête</option>
              {{foreach from=$headers item=headersByOwner key=owner}}
              <optgroup label="{{tr}}CCompteRendu._owner.{{$owner}}{{/tr}}">
                {{foreach from=$headersByOwner item=_header}}
                <option value="{{$_header->_id}}" {{if $compte_rendu->header_id == $_header->_id}}selected="selected"{{/if}}>{{$_header->nom}}</option>
                {{foreachelse}}
                <option value="" disabled="disabled">{{tr}}None{{/tr}}</option>
                {{/foreach}}
              </optgroup>
              {{/foreach}}
            </select>
          </td>
        </tr>
        {{/if}}

          
        <tr>
          <th>{{mb_label object=$compte_rendu field="object_class"}}</th>
            <td>
              <select name="object_class" class="{{$compte_rendu->_props.object_class}}" onchange="loadCategory()">
                <option value="">&mdash; Choisir un objet</option>
              </select>
            </td>
          </tr>

          <tr>
            <th>{{mb_label object=$compte_rendu field="file_category_id"}}</th>
            <td>
              <select name="file_category_id" class="{{$compte_rendu->_props.file_category_id}}">
                <option value="">&mdash; Aucune Catégorie</option>
              </select>
            </td>
          </tr>
          
          <tr>
            {{if $droit}}
              <td class="button" colspan="2">
              {{if $compte_rendu->_id}}
              <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le modèle',objName:'{{$compte_rendu->nom|smarty:nodefaults|JSAttribute}}'})">
              {{tr}}Delete{{/tr}}
              </button>
              {{else}}
              <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
              {{/if}}
              </td>
            {{/if}}
          </tr>
          
          <tr>
            <th class="category" colspan="2">Autres actions</th>
          </tr>
          
          <tr>
            <td class="button" colspan="2">
               <button type="button" class="add" onclick="copie(this.form)">{{tr}}Duplicate{{/tr}}</button>
            </td>
          </tr>
          
        </table>
      </td>
      
      <td class="greedyPane" style="height: 500px">
       {{if $compte_rendu->_id}}
         {{if !$droit}}
         <div class="big-info">
           Le présent modèle est en lecture seule. 
           <br/>Il comporte en l'état {{$compte_rendu->source|count_words}} mots.
           <br/>Vous pouvez le copier pour votre propre usage en cliquant sur <strong>Dupliquer</strong>. 
         </div>
         <hr/>
         {{/if}}

         {{mb_field object=$compte_rendu field="source" id="htmlarea"}}
       {{/if}}
      </td>
  </tr>
</table>    

</form>     
