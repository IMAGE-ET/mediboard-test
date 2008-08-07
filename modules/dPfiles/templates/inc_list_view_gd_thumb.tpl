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
  <li><a href="#Category-{{$keyCat}}">{{$curr_listCat.name}} ({{$curr_listCat.DocsAndFiles|@count}})</a></li>
{{/foreach}}
</ul>
<hr class="control_tabs" />

{{foreach from=$affichageFile item=curr_listCat key=keyCat}}
<table class="tbl" id="Category-{{$keyCat}}" style="display: none;">
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
    {{assign var="elementId" value=$curr_file->_id}}

    {{if $curr_file->_class_name=="CCompteRendu"}}
      {{assign var="srcImg" value="images/pictures/medifile.png"}}
      {{assign var="includeInfosFile" value="$curr_file->source"}}
    {{/if}}
    
    {{if $curr_file->_class_name=="CFile"}}
      {{assign var="srcImg" value="?m=dPfiles&a=fileviewer&suppressHeaders=1&file_id=$elementId&phpThumb=1&hp=450&wl=450"}}        
    {{/if}}
    
      <div id="viewFile-{{$curr_file->_id}}">
        {{include file="../../dPfiles/templates/inc_preview_file.tpl" 
        		fileSel=$curr_file
        		objectClass=$selClass 
        		objectId=$selKey
        		elementClass=$curr_file->_class_name
        		elementId=$curr_file->_id
        		sfn=0
        }}
      </div>
      <script type="text/javascript">
         File.view("{{$selClass}}", "{{$selKey}}", "{{$curr_file->_class_name}}", "{{$curr_file->_id}}","0"); 
      </script>
   
      <br />
      {{$curr_file->_view}}
      {{if $curr_file->_class_name == "CFile"}}
        <br />
        {{mb_label object=$curr_file field=file_date}} :
        {{mb_value object=$curr_file field=file_date}}
      {{/if}}
    <hr />  
    {{include file=inc_file_toolbar.tpl}}
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
{{/foreach}}