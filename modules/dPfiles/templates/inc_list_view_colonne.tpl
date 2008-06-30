<script type="text/javascript">
Main.add(function () {
  {{if $accordDossier}}
  var tabs{{$selClass}}{{$selKey}} = Control.Tabs.create('tab-{{$selClass}}{{$selKey}}', false);
  {{else}}
  var tabs = Control.Tabs.create('tab-consult', true);
  {{/if}}
});
</script>

<ul id="tab-{{if $accordDossier}}{{$selClass}}{{$selKey}}{{else}}consult{{/if}}" class="control_tabs">
{{foreach from=$affichageFile item=curr_listCat key=keyCat}}
  <li><a href="#{{$keyCat}}">{{$curr_listCat.name}} ({{$curr_listCat.DocsAndFiles|@count}})</a></li>
{{/foreach}}
</ul>
<hr class="control_tabs" />

{{foreach from=$affichageFile item=curr_listCat key=keyCat}}
<table class="tbl" id="{{$keyCat}}" style="display: none;">
  {{if $canFile->edit && !$accordDossier}}
  <tr>
    <td colspan="6" class="text">
     <form name="newDocumentFrm" action="?m={{$m}}" method="post">
     <table class="form">
       <tr>
         <td>
           <div style="float: right">
           <select name="_choix_modele" onchange="Document.create(this.value, {{$selKey}})">           
             <option value="">&mdash; Choisir un modèle</option>
             {{if $listModelePrat|@count}}
             <optgroup label="Modèles du praticien">
             {{foreach from=$listModelePrat item=curr_modele}}
             <option value="{{$curr_modele->compte_rendu_id}}">{{$curr_modele->nom}}</option>
             {{/foreach}}
             </optgroup>
             {{/if}}
             {{if $listModeleFunc|@count}}
             <optgroup label="Modèles du cabinet">
             {{foreach from=$listModeleFunc item=curr_modele}}
             <option value="{{$curr_modele->compte_rendu_id}}">{{$curr_modele->nom}}</option>
             {{/foreach}}
             </optgroup>
             {{/if}}
           </select>
           </div>
           <button class="new" type="button" onclick="uploadFile('{{$selClass}}', '{{$selKey}}', '{{$keyCat}}')">
             Ajouter un fichier
           </button>
         </td>
       </tr>
     </table>
     </form>
   </td>
 </tr>
  {{/if}}
  {{counter start=0 skip=1 assign=curr_data}}
  {{foreach from=$curr_listCat.DocsAndFiles item=curr_file}}
  {{if $curr_data is div by 3 || $curr_data==0}}
  <tr>
  {{/if}}
    <td class="{{cycle name=cellicon values="dark, light"}}">
      {{if $curr_file->_class_name=="CCompteRendu"}}
        {{assign var="elementId" value=$curr_file->_id}}
        {{assign var="srcImg" value="images/pictures/medifile.png"}}
      {{else}}
        {{assign var="elementId" value=$curr_file->_id}}
        {{assign var="srcImg" value="?m=dPfiles&a=fileviewer&suppressHeaders=1&file_id=$elementId&phpThumb=1&wl=64&hp=64"}}
      {{/if}}

      <a href="#" onclick="popFile('{{$selClass}}', '{{$selKey}}', '{{$curr_file->_class_name}}', '{{$elementId}}', '0');">
        <img src="{{$srcImg}}" alt="Petit aperçu" title="Afficher le grand aperçu" />
      </a>
    </td>
    <td class="{{cycle name=celltxt values="dark, light"}} text" style="vertical-align: middle;">
      <span class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { params: { object_class: '{{$curr_file->_class_name}}', object_id: {{$curr_file->_id}} } });">
        {{$curr_file->_view}}
      </span>
      <span class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { mode: 'objectViewHistory', params: { object_class: '{{$curr_file->_class_name}}', object_id: {{$curr_file->_id}} } });">
        <em>(historique)</em>
      </span>
      <hr />

      {{if $curr_file->_class_name=="CCompteRendu" && $canFile->edit && !$accordDossier}}
        <form name="editDoc{{$curr_file->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="m" value="dPcompteRendu" />
        <input type="hidden" name="dosql" value="do_modele_aed" />
        <input type="hidden" name="_id" value="{{$curr_file->_id}}" />
        <input type="hidden" name="del" value="0" />
        {{assign var="confirmDeleteType" value="le document"}}
        {{assign var="confirmDeleteName" value=$curr_file->nom}}
        
      {{elseif $curr_file->_class_name=="CFile" && $canFile->edit && !$accordDossier}}
        <form name="editFile{{$curr_file->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
        <input type="hidden" name="m" value="dPfiles" />
        <input type="hidden" name="dosql" value="do_file_aed" />
        <input type="hidden" name="_id" value="{{$curr_file->_id}}" />
        <input type="hidden" name="del" value="0" />
        {{assign var="confirmDeleteType" value="le fichier"}}
        {{assign var="confirmDeleteName" value=$curr_file->file_name}}
      {{/if}}

      {{if $canFile->edit && !$accordDossier}}
        <button class="edit" type="button" onclick="Document.edit({{$elementId}})">
          {{tr}}Edit{{/tr}}
        </button>
        <button type="button" class="trash" onclick="file_deleted={{$elementId}};confirmDeletion(
          this.form, {
            typeName:'{{$confirmDeleteType}}',
            objName:'{{$confirmDeleteName|smarty:nodefaults|JSAttribute}}',
            ajax:1,
            target:'systemMsg'
          },{
            onComplete:reloadAfterDeleteFile
          } );">
          Supprimer
        </button>
        
        <select name="file_category_id" onchange="submitFileChangt(this.form)">
          <option value="" {{if !$curr_file->file_category_id}}selected="selected"{{/if}}>&mdash; Aucune catégorie</option>
          {{foreach from=$listCategory item=curr_cat}}
          <option value="{{$curr_cat->file_category_id}}" {{if $curr_cat->file_category_id == $curr_file->file_category_id}}selected="selected"{{/if}} >
            {{$curr_cat->nom}}
          </option>
          {{/foreach}}
        </select>
      </form>
      {{/if}}

    </td>
  {{if ($curr_data+1) is div by 3}}
  </tr>
  {{/if}}
  {{counter}}
{{foreachelse}}
<tr>
  <td colspan="9" class="button">
    Pas de documents            
  </td>
</tr>
{{/foreach}}
</table>
{{/foreach}}