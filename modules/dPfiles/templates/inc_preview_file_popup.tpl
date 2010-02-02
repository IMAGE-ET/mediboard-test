{{assign var="href" value="?m=dPfiles&a=preview_files&popup=1&dialog=1&objectClass=$objectClass&objectId=$objectId"}}
<script type="text/javascript">

function goToPage(numpage){
  window.location.href = "?m=dPfiles&a=preview_files&popup=1&objectClass={{$objectClass}}&objectId={{$objectId}}&elementClass={{$elementClass}}&elementId={{$elementId}}&dialog=1&nonavig={{$nonavig}}&sfn=" + numpage;
}

window.onbeforeunload= function () {
}

</script>
<table class="form">
  <tr>
    <td style="width: 20%">
    {{if $filePrev && !$nonavig}}
    <a class="button left" href="{{$href}}&amp;elementClass={{$filePrev.elementClass}}&amp;elementId={{$filePrev.elementId}}">
      {{tr}}CDocumentItem-next{{/tr}}
    </a>
    {{/if}}
    </td>
    <td style="width:60%; text-align: center; white-space: normal;">
			<!-- Nom du fichier -->
      {{if $fileSel}}
      <strong>{{$fileSel->_view}}</strong>
      
			<!-- Category -->
      {{if $catFileSel->nom}}
      <br />
      {{mb_label object=$fileSel field=file_category_id}} :
      {{$catFileSel->nom}}
			{{/if}}

			<!-- Date -->
      {{if $fileSel->_class_name=="CFile"}}
	      <br />
	      {{mb_label object=$fileSel field=file_date}} :
	      {{mb_value object=$fileSel field=file_date}}
      {{/if}}
     {{/if}}
    </td>
    
    <th style="width:20%">
    {{if $fileNext && !$nonavig}}
    <a class="button rtl right" href="{{$href}}&amp;elementClass={{$fileNext.elementClass}}&amp;elementId={{$fileNext.elementId}}">
      {{tr}}CDocumentItem-next{{/tr}}
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
              <img align="top" src="images/icons/prev.png" alt="Page précédente" />
              Page précédente
            </a>
            {{/if}}
            {{if $fileSel->_nb_pages && $fileSel->_nb_pages>=2}}
              <select name="_num_page" onchange="goToPage(this.value);">
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
              <img align="top" src="images/icons/next.png" alt="Page suivante" />
            </a>
            {{/if}}
        {{/if}}
        <br />
          {{if $includeInfosFile}}
          {{include file="inc_preview_contenu_file.tpl"}}
          {{else}}
          <a href="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$elementId}}" title="Télécharger le fichier">
            <img src="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$elementId}}&amp;phpThumb=1&amp;w=700{{if $sfn}}&amp;sfn={{$sfn}}{{/if}}" alt="Grand aperçu" />
          </a> 
          {{/if}}
      {{/if}}
    </td>
  </tr>
</table>