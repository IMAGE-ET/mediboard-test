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
    <td colspan="2" class="text">
      <button class="new" onclick="uploadFile('{{$selClass}}', '{{$selKey}}', '{{$keyCat}}')">
        Ajouter un fichier
      </button>
    </td>
  </tr>
  {{/if}}
  {{foreach from=$curr_listCat.DocsAndFiles item=curr_file}}
  <tr>
    <td class="{{cycle name=cellicon values="dark, light"}}">
      {{assign var="elementId" value=$curr_file->_id}}
      {{if $curr_file->_class_name=="CCompteRendu"}}
        {{assign var="srcImg" value="images/pictures/medifile.png"}}
      {{else}}
        {{assign var="srcImg" value="?m=dPfiles&a=fileviewer&suppressHeaders=1&file_id=$elementId&phpThumb=1&wl=64&hp=64"}}
      {{/if}}
      
      <a href="#" onclick="ZoomAjax('{{$selClass}}', '{{$selKey}}', '{{$curr_file->_class_name}}', '{{$elementId}}', '0');" title="Afficher l'aperçu">
        <img src="{{$srcImg}}" alt="-" />
      </a>

    </td>
    <td class="text {{cycle name=celltxt values="dark, light"}}" style="vertical-align: middle;">
      <strong>{{$curr_file->_view}}</strong>
      <hr />
      {{include file=inc_file_toolbar.tpl}}
    </td>
  </tr>
{{foreachelse}}
<tr>
  <td colspan="2" class="button">
    {{tr}}CDocument-none{{/tr}}            
  </td>
</tr>
{{/foreach}}
</table>
{{/foreach}}