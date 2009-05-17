{{mb_include_script module="dPcompteRendu" script="modele_selector"}}

<script type="text/javascript">
Main.add(function () {
  {{if $accordDossier}}
  var tabs{{$selClass}}{{$selKey}} = Control.Tabs.create('tab-{{$selClass}}{{$selKey}}', false);
  {{else}}
  var tabs = Control.Tabs.create('tab-consult', true);
  {{/if}}
});

var File = {
  add: function() {
    
  }
};
</script>

<ul id="tab-{{if $accordDossier}}{{$selClass}}{{$selKey}}{{else}}consult{{/if}}" class="control_tabs">
{{foreach from=$affichageFile item=_cat key=_cat_id}}
  <li>
  	<a href="#Category-{{$_cat_id}}" {{if !$_cat.DocsAndFiles|@count}}class="empty"{{/if}}>
  		{{$_cat.name}} 
  		<small>({{$_cat.DocsAndFiles|@count}})</small>
  	</a>
  </li>
{{/foreach}}
</ul>

<hr class="control_tabs" />

{{include file=inc_files_add_toolbar.tpl}}

{{foreach from=$affichageFile item=_cat key=_cat_id}}
<div id="Category-{{$_cat_id}}" style="display: none; clear: both;">  
  {{foreach from=$_cat.DocsAndFiles item=curr_file}}
  <div style="float: left; width: 280px;">
  	 <table class="tbl">
  	   <tbody class="hoverable">
	  	   <tr>
	  	     <td rowspan="2" style="width: 80px; height: 70px; text-align: center">
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
			    
					<!-- Tooltip -->
					<td class="text" style="height: 40px; overflow: auto">
			      <span class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$curr_file->_guid}}');">
			        {{$curr_file->_view|truncate:60}}
			      </span>
					</td>
	
					<!-- Historique & identifiants externes-->
			    <td class="text" style="vertical-align: middle; width: 1%">
			      {{mb_include module=system template=vw_object_idsante400 object=$curr_file}}
			      <div class="idsante400" id="{{$curr_file->_guid}}"></div>
			      <span class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$curr_file->_guid}}', 'objectViewHistory');">
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
{{/foreach}}
<hr style="clear: both;" />