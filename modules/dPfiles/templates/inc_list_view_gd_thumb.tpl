<script type="text/javascript">

File = { 
  view: function (objectClass, objectId, elementClass, elementId, sfn) {
    file_preview = elementId;
    var url = new Url;
    url.setModuleAction("dPfiles", "preview_files");
    url.addParam("objectClass", objectClass);
    url.addParam("objectId", objectId);
    url.addParam("elementClass", elementClass);
    url.addParam("elementId", elementId);
    if(sfn && sfn!=0){
      url.addParam("sfn", sfn);
    }
    url.requestUpdate('viewFile-'+elementId, { waitingText : "Chargement de la miniature" });
  }
}

</script>


{{if $accordDossier}}
<div class="accordionMain" id="accordion{{$selClass}}{{$selKey}}">
{{else}}
<div class="accordionMain" id="accordionConsult">
{{/if}}
{{foreach from=$affichageFile item=curr_listCat key=keyCat}}
  <div id="Acc{{$keyCat}}">
    <div id="Acc{{$keyCat}}Header" class="accordionTabTitleBar">
      {{$curr_listCat.name}} ({{$curr_listCat.DocsAndFiles|@count}})
    </div>
    <div id="Acc{{$keyCat}}Content" class="accordionTabContentBox">
      <table class="tbl">
        {{if $canFile->edit && !$accordDossier}}
        <tr>
          <td colspan="9">
            <button class="new" onclick="uploadFile('{{$selClass}}', '{{$selKey}}', '{{$keyCat}}')">
              Ajouter un fichier
            </button>
          </td>
        </tr>
        {{/if}}
        {{counter start=0 skip=1 assign=curr_data}}
        {{foreach from=$curr_listCat.DocsAndFiles item=curr_file}}
        {{if $curr_data is div by 2 || $curr_data==0}}
        <tr>
        {{/if}}
        <td class="halfPane button {{cycle name=cellicon values="dark, light"}}">
          {{assign var="includeInfosFile" value="0"}}
          
          {{if $curr_file->_class_name=="CCompteRendu"}}
            <form name="editDoc{{$curr_file->compte_rendu_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
            <input type="hidden" name="m" value="dPcompteRendu" />
            <input type="hidden" name="dosql" value="do_modele_aed" />
            <input type="hidden" name="compte_rendu_id" value="{{$curr_file->compte_rendu_id}}" />
            <input type="hidden" name="del" value="0" />
            {{assign var="confirmDeleteType" value="le document"}}
            {{assign var="confirmDeleteName" value=$curr_file->nom}}
            {{assign var="elementId" value=$curr_file->compte_rendu_id}}
            {{assign var="srcImg" value="images/pictures/medifile.png"}}
            {{assign var="includeInfosFile" value="$curr_file->source"}}
          
          {{elseif $curr_file->_class_name=="CFile"}}
            <form name="editFile{{$curr_file->_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
            <input type="hidden" name="m" value="dPfiles" />
            <input type="hidden" name="dosql" value="do_file_aed" />
            <input type="hidden" name="file_id" value="{{$curr_file->file_id}}" />
            <input type="hidden" name="del" value="0" />
            {{assign var="confirmDeleteType" value="le fichier"}}
            {{assign var="confirmDeleteName" value=$curr_file->file_name}}
            {{assign var="elementId" value=$curr_file->file_id}}
            {{assign var="srcImg" value="?m=dPfiles&a=fileviewer&suppressHeaders=1&file_id=$elementId&phpThumb=1&hp=450&wl=450"}}        
          {{/if}}
            <div id="viewFile-{{$curr_file->_id}}">
              {{assign var="fileSel" value=$curr_file}}
              {{include file="../../dPfiles/templates/inc_preview_file.tpl"}}  
            </div>
            <script type="text/javascript">
               File.view("{{$selClass}}", "{{$selKey}}", "{{$curr_file->_class_name}}", "{{$curr_file->_id}}","0"); 
            </script>
         
            <br />
            {{$curr_file->_view}}
            {{if $curr_file->_class_name=="CFile"}}
              <br />Date : {{$curr_file->file_date|date_format:"%d/%m/%Y ï¿½ %Hh%M"}}
            {{/if}}
          </a>
          <br />  
            
          {{if $canFile->edit && !$accordDossier}}
            <button class="edit" type="button" onclick="editDocument({{$elementId}})">
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
          {{/if}}
          </form>
        </td>
        {{if ($curr_data+1) is div by 2}}
        </tr>
        {{/if}}
        {{counter}}
        {{foreachelse}}
        <tr>
          <td colspan="2" class="button">
            Pas de documents            
          </td>
        </tr>
        {{/foreach}}
      </table>
    </div>
  </div>
{{/foreach}}      
</div>
<script language="Javascript" type="text/javascript">
{{if $accordDossier}}
oAccord{{$selClass}}{{$selKey}} = new Rico.Accordion( $('accordion{{$selClass}}{{$selKey}}'), {
  panelHeight: ViewPort.SetAccordHeight('accordion{{$selClass}}{{$selKey}}'),
  showDelay:50,
  showSteps:3
});
{{else}}
oAccord = new Rico.Accordion( $('accordionConsult'), {
  panelHeight: ViewPort.SetAccordHeight('accordionConsult',{ iBottomMargin : 12 } ),
  onShowTab: storeKeyCat,
  showDelay:50,
  showSteps:3,
  onLoadShowTab: showTabAcc
});
{{/if}}
</script>