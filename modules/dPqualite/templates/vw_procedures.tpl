<script language="Javascript" type="text/javascript">

function popFile(file_id, sfn){
  var url = new Url;
  url.addParam("nonavig", 1);
  url.ViewFilePopup(file_id, sfn);
}

function ZoomFileAjax(file_id, sfn){
  file_preview = file_id;
  var url = new Url;
  url.setModuleAction("dPfiles", "preview_files");
  url.addParam("file_id", file_id);
  if(sfn!=0){
    url.addParam("sfn", sfn);
  }
  url.requestUpdate('bigView', { waitingText : "Chargement du miniature" });
}

</script>
<table class="main">
  <tr>
    <td class="halfPane">
      
      <form name="FrmThemeVue" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <label for="selTheme" title="Veuillez sélectionner un thème">Thème</label>
      <select name="selTheme" onchange="this.form.submit();">
        <option value="0">&mdash; Tous les thèmes &mdash;</option>
        {{foreach from=$listThemes item=currTheme}}
        <option value="{{$currTheme->doc_theme_id}}" {{if $selTheme==$currTheme->doc_theme_id}}selected="selected"{{/if}}>{{$currTheme->nom}}</option>
        {{/foreach}}
      </select>
      </form><br />
      
      <table class="tbl">
        <tr>
          <th>Référence</th>
          <th>Titre</th>
          <th>Date</th>
        </tr>
        {{foreach from=$procedures item=currProc}}
        <tr>
          <td>
            {{if $canEdit}}
            <a class="buttonedit notext" style="float: left;" href="index.php?m={{$m}}&amp;tab=vw_procencours&amp;doc_ged_id={{$currProc->doc_ged_id}}"></a>
            {{/if}}
            <a href="javascript:ZoomFileAjax({{$currProc->_lastactif->file_id}}, 0);">
              {{$currProc->_reference_doc}} (version : {{$currProc->version}})
            </a>
          </td>
          <td class="text">
            <a href="javascript:ZoomFileAjax({{$currProc->_lastactif->file_id}}, 0);">
              {{$currProc->titre}}
            </a>
          </td>
          <td>
            {{$currProc->_lastactif->date|date_format:"%d %b %Y à %Hh%M"}}
          </td>
        </tr>
        {{foreachelse}}
        <tr>
          <td colspan="3">
            Aucune procédure disponible
          </td>
        </tr>
        {{/foreach}}
      </table>    
    </td>
    <td class="halfPane" id="bigView" style="text-align: center;">
      {{include file="../../dPfiles/templates/inc_preview_file.tpl"}}
    </td>
  </tr>
</table>