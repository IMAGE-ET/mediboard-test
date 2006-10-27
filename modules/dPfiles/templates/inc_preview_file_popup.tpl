{{assign var="href" value="?m=dPfiles&a=preview_files&popup=1&dialog=1&objectClass=$objectClass&objectId=$objectId"}}
<script language="Javascript" type="text/javascript">

function goToPage(numpage){
  window.location.href = "?m=dPfiles&a=preview_files&popup=1&objectClass={{$objectClass}}&objectId={{$objectId}}&elementClass={{$elementClass}}&elementId={{$elementId}}&dialog=1&nonavig={{$nonavig}}&sfn=" + numpage;
}

function printDocument(doc_id) {
  var url = new Url;
  url.setModuleAction("dPcompteRendu", "print_cr");
  url.addParam("compte_rendu_id", doc_id);
  url.popup(700, 600, 'CompteRendu');
}

window.onbeforeunload= function () {
}
</script>
<table class="form">
  <tr>
    <td style="width: 20%">
    {{if $filePrev && !$nonavig}}
    <a class="button" href="{{$href}}&amp;elementClass={{$filePrev.elementClass}}&amp;elementId={{$filePrev.elementId}}">
      <img align="top" src="modules/{{$m}}/images/prev.png" alt="Fichier précédent" />
      Document précédent
    </a>
    {{/if}}
    </td>
    <td class="button" style="width:60%">
      {{if $fileSel}}
      <strong>{{$fileSel->_view}}</strong>
      {{if $fileSel->_class_name=="CCompteRendu"}}
      <button class="print notext" onclick="printDocument({{$fileSel->_id}})">
      </button>
      {{/if}}
      <br />Catégorie : {{if $catFileSel->nom}}
      {{$catFileSel->nom}} {{else}}Aucune Catégorie{{/if}}
      {{if $fileSel->_class_name=="CFile"}}
        &mdash; Date : {{$fileSel->file_date|date_format:"%d/%m/%Y à %Hh%M"}}
      {{/if}}
     {{/if}}
    </td>
    <th style="width:20%">
    {{if $fileNext && !$nonavig}}
    <a class="button" href="{{$href}}&amp;elementClass={{$fileNext.elementClass}}&amp;elementId={{$fileNext.elementId}}">
      Document suivant <img align="top" src="modules/{{$m}}/images/next.png" alt="Fichier suivant" />
    </a>
    {{/if}}
    </th>
  </tr>
  <tr>
    <td class="button" colspan="3">      
      {{if $fileSel}}
        {{if $fileSel->_class_name=="CFile" && $fileSel->_nb_pages && !$acces_denied}}
            {{if $page_prev !== null}}
            <a class="button" href="{{$href}}&amp;objectClass={{$objectClass}}&amp;objectId={{$objectId}}&amp;elementClass={{$elementClass}}&amp;elementId={{$elementId}}&amp;nonavig={{$nonavig}}&amp;sfn={{$page_prev}}">
              <img align="top" src="modules/{{$m}}/images/prev.png" alt="Page précédente" />
              Page précédente
            </a>
            {{/if}}
            {{if $fileSel->_nb_pages && $fileSel->_nb_pages>=2}}
              <select name="_num_page" onchange="javascript:goToPage(this.value);">
              {{foreach from=$arrNumPages|smarty:nodefaults item=currPage}}
              <option value="{{$currPage-1}}" {{if $currPage-1==$sfn}}selected="selected" {{/if}}>
                Page {{$currPage}} / {{$fileSel->_nb_pages}}
              </option>
              {{/foreach}}
              </select>
            {{elseif $fileSel->_nb_pages}}
            Page {{$sfn+1}} / {{$fileSel->_nb_pages}}
            {{/if}}

            {{if $page_next}}
            <a class="button" href="{{$href}}&amp;objectClass={{$objectClass}}&amp;objectId={{$objectId}}&amp;elementClass={{$elementClass}}&amp;elementId={{$elementId}}&amp;nonavig={{$nonavig}}&amp;sfn={{$page_next}}">
              Page suivante 
              <img align="top" src="modules/{{$m}}/images/next.png" alt="Page suivante" />
            </a>
            {{/if}}
        {{/if}}<br />
          {{if $includeInfosFile}}
          {{assign var="stylecontenu" value=null}}
          {{include file="inc_preview_contenu_file.tpl"}}
          {{else}}
          <a href="index.php?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$elementId}}" title="Télécharger le fichier">
            <img src="index.php?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$elementId}}&amp;phpThumb=1&amp;w=700{{if $sfn}}&amp;sfn={{$sfn}}{{/if}}" alt="Grand aperçu" />
          </a> 
          {{/if}}
      {{/if}}
    </td>
  </tr>
</table>