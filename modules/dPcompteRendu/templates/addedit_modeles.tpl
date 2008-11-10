<script type="text/javascript">

function popFile(objectClass, objectId, elementClass, elementId, sfn){
  var url = new Url;
  url.ViewFilePopup(objectClass, objectId, elementClass, elementId, sfn);
}

function copie() {

  {{if $droit}}
    if(confirm('Vous avez deja accès à ce modèle, souhaitez-vous confirmer la copie de ce modèle ?')){
      var oForm = document.editFrm;
      oForm.compte_rendu_id.value = "";
      
      {{if $isPraticien}}
      oForm.chir_id.value = "{{$user_id}}";
      oForm.function_id.value = "";
      {{/if}}
      
      oForm.nom.value = "Copie de "+oForm.nom.value;
      oForm.submit(); 
    }  
  {{/if}}
  
  {{if !$droit}}
    var oForm = document.editFrm;
    oForm.compte_rendu_id.value = '';
    oForm.chir_id.value = "{{$user_id}}";
    oForm.nom.value = "Copie de "+oForm.nom.value;
    oForm.submit();
  {{/if}}
} 

function nouveau() {
  var url = new Url;
  url.setModuleTab("dPcompteRendu", "addedit_modeles");
  url.addParam("compte_rendu_id", "0");
  url.redirect();
}

function supprimer() {
  var form = document.editFrm;
  form.del.value = 1;
  form.submit();
}

// Taleau des categories en fonction de la classe du compte rendu
var listObjectClass = {{$listObjectClass|@json}};

// Creation du tableau de traduction
var aTraducClass = new Array();
{{foreach from=$listObjectAffichage key=key item=currClass}}
aTraducClass["{{$key}}"] = "{{$currClass}}";
{{/foreach}}

function loadObjectClass(value) {
  var form = document.editFrm;
  var select = form.elements['object_class'];
  var options = listObjectClass;

  // Delete all former options except first
  while (select.length > 1) {
    select.options[1] = null;
  }
  
  // Insert new ones
  for (var elm in options) {
    var option = elm;
    if (typeof(options[option]) != "function") { // to filter prototype functions
      select.options[select.length] = new Option(aTraducClass[option], option, option == value);
    }
  }
  
  // Check null position
  select.fire("ui:change");
 
  loadCategory();
}

function loadCategory(value){
  var form = document.editFrm;
  var select = form.elements['file_category_id'];
  var className  = form.elements['object_class'].value;
  var options = listObjectClass[className];
  // delete all former options except first
  while (select.length > 1) {
    select.options[1] = null;
  }
  // insert new ones
  for (var elm in options) {
    var option = options[elm];
    if (typeof(option) != "function") { // to filter prototype functions
      select.options[select.length] = new Option(option, elm, elm == value);
    }
  }
}

Main.add(function () {
  loadObjectClass('{{$compte_rendu->object_class}}');
  loadCategory('{{$compte_rendu->file_category_id}}');
});

</script>

<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<table class="main">
  <tr>
    <td>
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_modele_aed" />
      {{mb_field object=$compte_rendu field="compte_rendu_id" hidden=1 prop=""}}
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
            <a style="float:right;" href="#" onclick="view_log('CCompteRendu',{{$compte_rendu->_id}})">
            <img src="images/icons/history.gif" alt="historique" />
            </a>
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
            <select {{if !$droit}}disabled='disabled'{{/if}} name="group_id" class="{{$compte_rendu->_props.group_id}}" onchange="this.form.chir_id.value = this.form.function_id.value = ''">
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
            <select {{if !$droit}}disabled='disabled'{{/if}} name="function_id" class="{{$compte_rendu->_props.function_id}}" onchange="this.form.chir_id.value = this.form.group_id.value = ''">
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
            <select {{if !$droit}}disabled='disabled'{{/if}} name="chir_id" class="{{$compte_rendu->_props.chir_id}}" onchange="this.form.function_id.value = this.form.group_id.value =''; ">
              <option value="">&mdash; Associer à un praticien</option>
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
	            {{mb_field object=$compte_rendu field=type disabled="disable"}}
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
          
        {{if is_array($footers)}}
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
					      <option value="">{{tr}}None{{/tr}}</option>
					      {{/foreach}}
					    </optgroup>
					    {{/foreach}}
					  </select>
          </td>
        </tr>
        {{/if}}

        {{if is_array($headers)}}
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
					      <option value="">{{tr}}None{{/tr}}</option>
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
               <button class="add" onclick="copie(this.form)">{{tr}}Duplicate{{/tr}}</button>
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
