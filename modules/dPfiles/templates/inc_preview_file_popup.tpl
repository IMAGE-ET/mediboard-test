{{assign var="href" value="?m=dPfiles&a=preview_files&popup=1&dialog=1&objectClass=$objectClass&objectId=$objectId"}}
<script type="text/javascript">

function goToPage(numpage){
  window.location.href = "?m=dPfiles&a=preview_files&popup=1&objectClass={{$objectClass}}&objectId={{$objectId}}&elementClass={{$elementClass}}&elementId={{$elementId}}&dialog=1&nonavig={{$nonavig}}&sfn=" + numpage;
}

window.onbeforeunload= function () {
}

function openWindowMail() {
  var url = new Url("dPcompteRendu", "ajax_view_mail");
  url.addParam("object_guid", "{{$fileSel->_guid}}");
  url.requestModal(700, 320);
}

window.destinataires = {{"utf8_encode"|array_map_recursive:$destinataires|@json|smarty:nodefaults}};

  modifTitle = function(elt) {
    $("fileName").hide();
    $("modifTitlefrm").show();
  }

  updatetitle = function() {
    var form = getForm('frm-file-name');
    $("fileName").update($V(form.file_name));
    $("fileName").show();
    $("modifTitlefrm").hide();
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
    <strong onclick="modifTitle(this)">
      <span id="fileName">{{$fileSel->_view}}</span>
      {{if $fileSel->private}}
        &mdash; <em>{{tr}}CCompteRendu-private{{/tr}}</em>
      {{/if}}
    </strong>

    <div id="modifTitlefrm" style="display: none">
      {{mb_form name="frm-file-name" m="files" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: updatetitle});"}}
      {{mb_class object=$fileSel}}
      {{mb_key object=$fileSel}}
      {{mb_field object=$fileSel field=file_name}}
        <button type="submit" class="save notext">{{tr}}Save{{/tr}}</button>
      {{/mb_form}}
    </div>

  	<!-- Category -->
    {{if $catFileSel->nom}}
      <br />
      {{mb_label object=$fileSel field=file_category_id}} :
      {{$catFileSel->nom}}
  	{{/if}}
  
  	<!-- Date -->
    {{if $fileSel->_class=="CFile"}}
      <br />
      {{mb_label object=$fileSel field=file_date}} :
      {{mb_value object=$fileSel field=file_date}}
    {{/if}}
    
    <br />
    <a class="button notext thumbnails" href="?m=dPfiles&a=ajax_files_gallery&dialog=1&object_class={{$objectClass}}&object_id={{$objectId}}"></a>
    {{if $exchange_source->_id}}
      <button type="button" class="mail" onclick="openWindowMail();">{{tr}}CCompteRendu.send_mail{{/tr}}</button>
    {{/if}}
  {{/if}}
</div>
</td></tr></table>

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
    {{if $fileSel->_class == "CFile" && !$includeInfosFile}}
      {{mb_field object=$fileSel field=_rotate hidden=1}}
      <button type="button" style="float: left;" class="rotate_left notext singleclick" onclick="onSubmitRotate(this, 'left')" title="{{tr}}CFile._rotate.left{{/tr}}"></button>
      <button type="button" style="float: right;" class="rotate_right notext singleclick" onclick="onSubmitRotate(this, 'right')" title="{{tr}}CFile._rotate.right{{/tr}}"></button>
    {{/if}}   
    </form>
    {{if $fileSel}}

    {{if $fileSel->_class=="CFile" && $fileSel->_nb_pages > 1 && !$acces_denied}}
        
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
      <a target="_blank" href="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$fileSel->_id}}" title="{{tr}}CFile.download{{/tr}}">
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