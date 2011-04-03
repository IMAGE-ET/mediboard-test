{{mb_script module="dPcompteRendu" script="modele_selector"}}

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
{{foreach from=$affichageFile item=_cat key=_cat_id}}
  <li>
    {{assign var=docCount value=$_cat.items|@count}}
    {{if $docCount || $conf.dPfiles.CFilesCategory.show_empty}}
  	<a href="#Category-{{$_cat_id}}" {{if !$docCount}}class="empty"{{/if}}>
  		{{$_cat.name}}
  		<small>({{$docCount}})</small>
  	</a>
		{{/if}}
  </li>
{{/foreach}}
</ul>

<hr class="control_tabs" />

{{include file=inc_files_add_toolbar.tpl}}

{{foreach from=$affichageFile item=_cat key=_cat_id}}
{{assign var=docCount value=$_cat.items|@count}}
{{if $docCount || $conf.dPfiles.CFilesCategory.show_empty}}
<div id="Category-{{$_cat_id}}" style="display: none; clear: both;">  
  {{foreach from=$_cat.items item=_doc_item}}
  <div style="float: left; width: 220px;">
    <table class="tbl">
      <tbody class="hoverable">
        <tr>
          <td rowspan="2" style="width: 70px; height: 112px; text-align: center">
					  <div></div>
            {{assign var="elementId" value=$_doc_item->_id}}
            {{if $_doc_item->_class_name=="CCompteRendu"}}
              {{if $conf.dPcompteRendu.CCompteRendu.pdf_thumbnails && $app->user_prefs.pdf_and_thumbs}}
                {{assign var="nomdoc" value=$_doc_item->nom}}
                {{assign var="file_owner" value=$_doc_item->_ref_chir->_id}}
                {{assign var="file_id" value=$_doc_item->_ref_file->_id}}
                {{assign var="srcImg" value="?m=dPfiles&a=fileviewer&suppressHeaders=1&file_id=$file_id&phpThumb=1&w=64&h=92"}}
              {{else}}
			          {{assign var="srcImg" value="images/pictures/medifile.png"}}
              {{/if}}
            {{else}}
              {{assign var="srcImg" value="?m=dPfiles&a=fileviewer&suppressHeaders=1&file_id=$elementId&phpThumb=1&w=64&h=92"}}
            {{/if}}

            <a href="#" onclick="popFile('{{$selClass}}', '{{$selKey}}', '{{$_doc_item->_class_name}}', '{{$elementId}}', '0');">
              <img class="thumbnail" src="{{$srcImg}}" />
            </a>
          </td>
			    
          <!-- Tooltip -->
          <td class="text" style="height: 35px; overflow: auto">
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_doc_item->_guid}}');">
              {{$_doc_item->_view|truncate:60}}
              {{if $_doc_item->private}}
                &mdash; <em>{{tr}}CCompteRendu-private{{/tr}}</em>
              {{/if}}
            </span>
          </td>

        </tr>
        <tr>

          <!-- Toolbar -->
          <td class="button" style="height: 1px;">
            {{include file=inc_file_toolbar.tpl notext=notext}}
          </td>

        </tr>
      </tbody>
    </table>
  </div>
  {{foreachelse}}
    <div class="empty">Aucun document</div>
  {{/foreach}}
</div>
{{/if}}
{{/foreach}}
<hr style="clear: both;" />