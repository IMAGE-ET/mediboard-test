{{assign var="href" value="?m=dPfiles&a=preview_files&popup=1&dialog=1&objectClass=$objectClass&objectId=$objectId"}}
<script type="text/javascript">

function goToPage(numpage){
  window.location.href = "?m=dPfiles&a=preview_files&popup=1&objectClass={{$objectClass}}&objectId={{$objectId}}&elementClass={{$elementClass}}&elementId={{$elementId}}&dialog=1&nonavig={{$nonavig}}&sfn=" + numpage;
}

window.onbeforeunload= function () {
}

</script>

<table class="main form">
  <tr><td>
  
<div style="text-align: center;">
  {{if !$nonavig && ($filePrev || $fileNext)}}
    <button type="button"style="float: left;" {{if !$filePrev}}disabled="disabled"{{/if}} class="left" onclick="location.href='{{$href}}&amp;elementClass={{$filePrev.elementClass}}&amp;elementId={{$filePrev.elementId}}'">
      {{tr}}CDocumentItem-prev{{/tr}}
    </button>
    <button style="float: right;" class="button rtl right" {{if !$fileNext}}disabled="disabled"{{/if}} onclick="location.href='{{$href}}&amp;elementClass={{$fileNext.elementClass}}&amp;elementId={{$fileNext.elementId}}'">
      {{tr}}CDocumentItem-next{{/tr}}
    </button>
  {{/if}}
  
	<!-- Nom du fichier -->
  {{if $fileSel}}
    <strong>
      {{$fileSel->_view}}
      {{if $fileSel->private}}
        &mdash; <em>{{tr}}CCompteRendu-private{{/tr}}</em>
      {{/if}}
    </strong>
    
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
</div>

<div style="text-align: center;">
  <script type="text/javascript">
  function onSubmitRotate(element, rotate) {
    var form = element.form;
    $V(form._rotate, rotate);
    return onSubmitFormAjax(form, {onComplete: function() { location.reload(); }});
  }
  </script>
  <form name="FileRotate" action="?dialog=1" method="post">
    <input type="hidden" name="m" value="dPfiles"/>
    <input type="hidden" name="a" value="preview_files"/>
    <input type="hidden" name="dosql" value="do_file_aed"/>
    {{mb_key object=$fileSel hidden=1}}
    {{mb_field object=$fileSel field=_rotate hidden=1}}
      
    <button type="button" style="float: left;" class="rotate_left notext singleclick" onclick="onSubmitRotate(this, 'left')" title="{{tr}}CFile._rotate.left{{/tr}}"></button>
    <button type="button" style="float: right;" class="rotate_right notext singleclick" onclick="onSubmitRotate(this, 'right')" title="{{tr}}CFile._rotate.right{{/tr}}"></button>
       
    </form>
    {{if $fileSel}}

    {{if $fileSel->_class_name=="CFile" && $fileSel->_nb_pages > 1 && !$acces_denied}}
        
      <button class="left" {{if $page_prev === null}}disabled="disabled"{{/if}}
         onclick="location.href='{{$href}}&amp;objectClass={{$objectClass}}&amp;objectId={{$objectId}}&amp;elementClass={{$elementClass}}&amp;elementId={{$elementId}}&amp;nonavig={{$nonavig}}&amp;sfn={{$page_prev}}'">
        Page précédente
      </button>
      
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

      <button class="right rtl" {{if $page_next === null}}disabled="disabled"{{/if}}
         onclick="location.href='{{$href}}&amp;objectClass={{$objectClass}}&amp;objectId={{$objectId}}&amp;elementClass={{$elementClass}}&amp;elementId={{$elementId}}&amp;nonavig={{$nonavig}}&amp;sfn={{$page_next}}'">
        Page suivante 
      </button>
    {{/if}}
      
    <br />
    {{if $includeInfosFile}}
      {{include file="inc_preview_contenu_file.tpl"}}
    {{else}}
      <a target="_blank" href="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$fileSel->_id}}" title="Télécharger le fichier">
        <img id="thumb" style="border: 1px solid #000;" src="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$fileSel->_id}}&amp;phpThumb=1&amp;w=700{{if $sfn}}&amp;sfn={{$sfn}}{{/if}}" />
      </a>
    {{/if}}
  {{/if}}
</div>

{{*
{{if $isConverted == 1}}
  <a class="button save" target="_blank" href="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$file_id_original}}">{{tr}}CFile.save_original{{/tr}}</a>
{{/if}}
*}}
</td></tr></table>