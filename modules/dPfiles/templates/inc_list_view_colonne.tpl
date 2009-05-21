{{mb_include_script module="dPcompteRendu" script="modele_selector"}}

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
    {{assign var=docCount value=$_cat.DocsAndFiles|@count}}
    {{if $docCount || $dPconfig.dPfiles.CFilesCategory.show_empty}}
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
{{assign var=docCount value=$_cat.DocsAndFiles|@count}}
{{if $docCount || $dPconfig.dPfiles.CFilesCategory.show_empty}}
<div id="Category-{{$_cat_id}}" style="display: none; clear: both;">  
  {{foreach from=$_cat.DocsAndFiles item=_doc_item}}
  <div style="float: left; width: 280px;">
  	 <table class="tbl">
  	   <tbody class="hoverable">
	  	   <tr>
	  	     <td rowspan="2" style="width: 70px; height: 70px; text-align: center">
			      {{assign var="elementId" value=$_doc_item->_id}}
			      {{if $_doc_item->_class_name=="CCompteRendu"}}
			        {{assign var="srcImg" value="images/pictures/medifile.png"}}
			      {{else}}
			        {{assign var="srcImg" value="?m=dPfiles&a=fileviewer&suppressHeaders=1&file_id=$elementId&phpThumb=1&wl=64&hp=64"}}
			      {{/if}}
	
			      <a href="#" onclick="popFile('{{$selClass}}', '{{$selKey}}', '{{$_doc_item->_class_name}}', '{{$elementId}}', '0');">
			        <img src="{{$srcImg}}" alt="Petit aperçu" title="Afficher le grand aperçu" />
			      </a>
			    </td>
			    
					<!-- Tooltip -->
					<td class="text" style="height: 35px; overflow: auto">
			      <span class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$_doc_item->_guid}}');">
			        {{$_doc_item->_view|truncate:60}}
			      </span>
					</td>
	
					<!-- Historique & identifiants externes-->
			    <td class="text" style="vertical-align: middle; width: 1%">
			      {{mb_include module=system template=vw_object_idsante400 object=$_doc_item}}
			      <span class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$_doc_item->_guid}}', 'objectViewHistory');">
							<img src="images/icons/history.gif" alt="historique" title="Voir l'historique" />
			      </span>
					</td>

				</tr>
				<tr>

  				<!-- Toolbar -->
				  <td colspan="2" class="button">
			      {{include file=inc_file_toolbar.tpl notext=notext}}
			  	</td>

			 	</tr>
			</tbody>
		</table>
	</div>
	{{foreachelse}}
	<em>Pas de documents</em>
	{{/foreach}}
</div>
{{/if}}
{{/foreach}}
<hr style="clear: both;" />