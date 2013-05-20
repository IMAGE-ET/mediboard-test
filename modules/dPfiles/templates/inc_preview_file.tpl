{{*
  * File preview
  *
  * @category dPfiles
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  * @version  SVN: $Id:$
  * @link     http://www.mediboard.org
*}}

{{if $fileSel && $fileSel->_id}}
  <h4>{{$fileSel->_view}}</h4>
  
  {{if $fileSel->_class=="CFile"}}
    {{$fileSel->file_date|date_format:$conf.datetime}}<br />
  {{/if}}

  {{if $fileSel->_class == "CFile" && $fileSel->_nb_pages && !$acces_denied}}
  <!-- Déplacement dans les pages -->
    
    <button type="button" {{if $page_prev === null}}disabled="disabled"{{/if}} title="Page précédente" onclick="ZoomAjax('{{$objectClass}}', '{{$objectId}}', '{{$elementClass}}', '{{$elementId}}', '{{$page_prev}}');">
    	<img src="images/icons/prev.png" />
    </button>
    
    {{if $fileSel->_nb_pages && $fileSel->_nb_pages>=2}}
    <select name="_num_page" onchange="ZoomAjax('{{$objectClass}}', '{{$objectId}}', '{{$elementClass}}', '{{$elementId}}', this.value);">
      {{foreach from=$arrNumPages item=currPage}}
        <option value="{{$currPage-1}}" {{if $currPage-1==$sfn}}selected="selected" {{/if}}>
          {{$currPage}} / {{$fileSel->_nb_pages}}
        </option>
      {{/foreach}}
    </select>
    {{elseif $fileSel->_nb_pages}}
      Page {{$sfn+1}} / {{$fileSel->_nb_pages}}
    {{/if}}
    
    <button type="button" {{if !$page_next}}disabled="disabled"{{/if}} title="Page suivante" onclick="ZoomAjax('{{$objectClass}}', '{{$objectId}}', '{{$elementClass}}', '{{$elementId}}', '{{$page_next}}');">
    	<img src="images/icons/next.png" />
    </button>
  {{/if}}
  
  <hr />

  {{if $display_as_is}}
    <a class="button lookup" href="#popFile"
      onclick="popFile('{{$objectClass}}', '{{$objectId}}', '{{$elementClass}}', '{{$elementId}}',{{if $sfn}}{{$sfn}}{{else}}0{{/if}})">
    Visualiser
    </a>
    {{if $includeInfosFile}}
      {{mb_include module=files template=inc_preview_contenu_file}}
    {{else}}
      <img class="preview" src="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$fileSel->_id}}&amp;phpThumb=1&amp;hp=450&amp;wl=450{{if $sfn}}&amp;sfn={{$sfn}}{{/if}}" title="Afficher le grand aperçu" border="0" />
    {{/if}}
  {{else}}
    <a href="#popFile" onclick="popFile('{{$objectClass}}', '{{$objectId}}', '{{$elementClass}}', '{{$elementId}}',{{if $sfn}}{{$sfn}}{{else}}0{{/if}})">
      {{if $includeInfosFile}}
        {{mb_include module=files template=inc_preview_contenu_file}}
      {{else}}
        <img class="preview" src="?m=dPfiles&amp;a=fileviewer&amp;suppressHeaders=1&amp;file_id={{$fileSel->_id}}&amp;phpThumb=1&amp;hp=450&amp;wl=450{{if $sfn}}&amp;sfn={{$sfn}}{{/if}}" title="Afficher le grand aperçu" border="0" />
      {{/if}}
    </a>
  {{/if}}
{{else}}
  <div class="small-info">
  Sélectionnez un document pour en avoir un aperçu.
  </div>
{{/if}}