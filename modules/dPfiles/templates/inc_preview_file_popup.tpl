{{assign var="href" value="?m=dPfiles&amp;a=preview_files&amp;popup=1&amp;dialog=1"}}
<table class="form">
  <tr>
    <td style="width: 20%">
    {{if $filePrev}}
    <a class="button" href="{{$href}}&amp;file_id={{$filePrev}}&amp;navig=1">
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
    {{if $fileNext}}
    <a class="button" href="{{$href}}&amp;file_id={{$fileNext}}&amp;navig=1">
      Fichier suivant <img align="top" src="modules/{{$m}}/images/next.png" alt="Fichier suivant" />
    </a>
    {{/if}}
    </th>
  </tr>
  <tr>
    <td class="button" colspan="3">      
      {{if $file->file_id}}
        {{if $file->_nb_pages}}
            {{if $page_prev !== null}}
            <a class="button" href="{{$href}}&amp;file_id={{$file->file_id}}&amp;navig=1&amp;sfn={{$page_prev}}">
              <img align="top" src="modules/{{$m}}/images/prev.png" alt="Page précédente" />
              Page précédente
            </a>
            {{/if}}
            {{$pageEnCours}}
            {{if $page_next}}
            <a class="button" href="{{$href}}&amp;file_id={{$file->file_id}}&amp;navig=1&amp;sfn={{$page_next}}">
              Page suivante 
              <img align="top" src="modules/{{$m}}/images/next.png" alt="Page suivante" />
            </a>
            {{/if}}
        {{/if}}<br />
        <a href="mbfileviewer.php?file_id={{$file->file_id}}" title="Télécharger le fichier">
          <img src="mbfileviewer.php?file_id={{$file->file_id}}&amp;phpThumb=1&amp;w=700{{if $sfn}}&amp;sfn={{$sfn}}{{/if}}" alt="Grand aperçu" />
        </a>
        {{include file="inc_preview_contenu_file.tpl"}}
      {{/if}}
    </td>
  </tr>
</table>