{{assign var="href" value="?m=dPfiles&a=preview_files&popup=1&dialog=1"}}
<script language="Javascript" type="text/javascript">

function goToPage(numpage){
  window.location.href = "?m=dPfiles&a=preview_files&popup=1&dialog=1&file_id={{$file->file_id}}&nonavig={{$nonavig}}&sfn=" + numpage;
}

</script>
<table class="form">
  <tr>
    <td style="width: 20%">
    {{if $filePrev && !$nonavig}}
    <a class="button" href="{{$href}}&amp;file_id={{$filePrev}}">
      <img align="top" src="modules/{{$m}}/images/prev.png" alt="Fichier précédent" />
      Fichier précédent
    </a>
    {{/if}}
    </td>
    <td class="button" style="width:60%">
      {{if $file->file_id}}
      <strong>{{$file->_view}}</strong><br />
      Catégorie : {{if $listCat->nom}}
      {{$listCat->nom}} {{else}}Aucune Catégorie{{/if}} 
      &mdash; Date : {{$file->file_date|date_format:"%d/%m/%Y à %Hh%M"}}
     {{/if}}
    </td>
    <th style="width:20%">
    {{if $fileNext && !$nonavig}}
    <a class="button" href="{{$href}}&amp;file_id={{$fileNext}}">
      Fichier suivant <img align="top" src="modules/{{$m}}/images/next.png" alt="Fichier suivant" />
    </a>
    {{/if}}
    </th>
  </tr>
  <tr>
    <td class="button" colspan="3">      
      {{if $file->file_id}}
        {{if $file->_nb_pages && !$acces_denied}}
            {{if $page_prev !== null}}
            <a class="button" href="{{$href}}&amp;file_id={{$file->file_id}}&amp;nonavig={{$nonavig}}&amp;sfn={{$page_prev}}">
              <img align="top" src="modules/{{$m}}/images/prev.png" alt="Page précédente" />
              Page précédente
            </a>
            {{/if}}
            {{if $file->_nb_pages && $file->_nb_pages>=2}}
              <select name="_num_page" onchange="javascript:goToPage(this.value);">
              {{foreach from=$arrNumPages|smarty:nodefaults item=currPage}}
              <option value="{{$currPage-1}}" {{if $currPage-1==$sfn}}selected="selected" {{/if}}>
                Page {{$currPage}} / {{$file->_nb_pages}}
              </option>
              {{/foreach}}
              </select>
            {{elseif $file->_nb_pages}}
            Page {{$sfn+1}} / {{$file->_nb_pages}}
            {{/if}}

            {{if $page_next}}
            <a class="button" href="{{$href}}&amp;file_id={{$file->file_id}}&amp;nonavig={{$nonavig}}&amp;sfn={{$page_next}}">
              Page suivante 
              <img align="top" src="modules/{{$m}}/images/next.png" alt="Page suivante" />
            </a>
            {{/if}}
        {{/if}}<br />
        <a href="index.php?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$file->file_id}}" title="Télécharger le fichier">
          {{if $includeInfosFile}}
          {{include file="inc_preview_contenu_file.tpl"}}
          {{else}}
          <img src="index.php?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$file->file_id}}&amp;phpThumb=1&amp;w=700{{if $sfn}}&amp;sfn={{$sfn}}{{/if}}" alt="Grand aperçu" />
          {{/if}}
        </a>        
      {{/if}}
    </td>
  </tr>
</table>