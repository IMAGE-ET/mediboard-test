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
  <li><a href="#Category-{{$keyCat}}">{{$curr_listCat.name}} ({{$curr_listCat.DocsAndFiles|@count}})</a></li>
{{/foreach}}
</ul>
<hr class="control_tabs" />

{{foreach from=$affichageFile item=curr_listCat key=keyCat}}
<table class="tbl" id="Category-{{$keyCat}}" style="display: none;">
  {{if $canFile->edit && !$accordDossier}}
  <tr>
    <td colspan="6" class="text">
     <form name="FileNew-Category-{{$keyCat}}" action="?m={{$m}}" method="post">
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
  {{if $curr_data is div by 2 || $curr_data==0}}
  <tr>
  {{/if}}
    <td class="{{cycle name=cellicon values="dark, light"}}">
      {{assign var="elementId" value=$curr_file->_id}}
      {{if $curr_file->_class_name=="CCompteRendu"}}
        {{assign var="srcImg" value="images/pictures/medifile.png"}}
      {{else}}
        {{assign var="srcImg" value="?m=dPfiles&a=fileviewer&suppressHeaders=1&file_id=$elementId&phpThumb=1&wl=64&hp=64"}}
      {{/if}}

      <a href="#" onclick="popFile('{{$selClass}}', '{{$selKey}}', '{{$curr_file->_class_name}}', '{{$elementId}}', '0');">
        <img src="{{$srcImg}}" alt="Petit aperçu" title="Afficher le grand aperçu" />
      </a>
    </td>
    <td class="{{cycle name=celltxt values="dark, light"}} text" style="vertical-align: middle;">

			<!-- Historique -->
      <span class="tooltip-trigger" style="float: right" onmouseover="ObjectTooltip.create(this, { mode: 'objectViewHistory', params: { object_class: '{{$curr_file->_class_name}}', object_id: {{$curr_file->_id}} } });">
				<img src="images/icons/history.gif" alt="historique" title="Voir l'historique" />
      </span>

      <span class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { params: { object_class: '{{$curr_file->_class_name}}', object_id: {{$curr_file->_id}} } });">
        {{$curr_file->_view}}
      </span>
      <hr />
      {{include file=inc_file_toolbar.tpl}}
    </td>
  {{if ($curr_data+1) is div by 2}}
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