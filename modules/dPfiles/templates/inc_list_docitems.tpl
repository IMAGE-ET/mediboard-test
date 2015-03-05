{{*
  * List docitems by category
  *  
  * @category dPfiles
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

{{assign var=pdf_thumbnails value=$conf.dPcompteRendu.CCompteRendu.pdf_thumbnails}}
{{assign var=pdf_and_thumbs value=$app->user_prefs.pdf_and_thumbs}}

{{foreach from=$list item=docitems_by_cat key=cat}}
  <div class="compact">
    {{if $cat != ""}}
      {{$cat}}
    {{else}}
      {{tr}}CFilesCategory.none{{/tr}}
    {{/if}}
  </div>
  <ul>
    {{foreach from=$docitems_by_cat item=_docitem}}
      <li>
        {{if $_docitem instanceof CCompteRendu}}
        <button type="button" class="print notext"
                onclick="{{if $pdf_thumbnails && $pdf_and_thumbs}}
                Document.printPDF({{$_docitem->_id}});
                {{else}}
                Document.print({{$_docitem->_id}});
                {{/if}}">{{tr}}Print{{/tr}}</button>
          <a href="#document-{{$_docitem->_id}}" style="display: inline;"
             onclick="Document.edit('{{$_docitem->_id}}')">
              {{$_docitem->nom}}
          </a>
        {{else}}
          <a href="#document-{{$_docitem->_id}}" style="display: inline;"
               onclick="return popFile('{{$_docitem->object_class}}','{{$_docitem->object_id}}','{{$_docitem->_class}}','{{$_docitem->_id}}')">
            {{$_docitem->file_name}}
          </a>
        {{/if}}
      </li>
    {{/foreach}}
  </ul>
{{foreachelse}}
  <div class="empty">
    {{tr}}None{{/tr}}
  </div>
{{/foreach}}