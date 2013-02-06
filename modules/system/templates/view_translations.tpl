{{*
  * AddEdit overwrite translations
  *  
  * @category System
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

<script type="text/javascript">
  editTrad = function(id) {
    var url = new Url("system","ajax_edit_translation");
    url.addParam("trad_id", id);
    url.requestModal(500,400);
  }
</script>

<button onclick="editTrad(0)" class="new">{{tr}}CTranslationOverwrite.new{{/tr}}</button>
{{if $cache}}
  <div class="warning">{{tr}}CTranslationOverwrite-info-reload-cache{{/tr}}</div>
{{/if}}
<table class="main">
  <tr>
    <td id="listTrad">
      <table class="tbl">
        <tr>
          <th>{{tr}}CTranslationOverwrite-source{{/tr}}</th>
          <th>{{tr}}CTranslationOverwrite-_old_translation{{/tr}}</th>
          <th>{{tr}}CTranslationOverwrite-translation{{/tr}}</th>
          <th>{{tr}}CTranslationOverwrite-language{{/tr}}</th>
        </tr>
        {{foreach from=$translations_bdd item=_trad}}
          <tr onclick="editTrad({{$_trad->_id}})">
            <td>{{mb_value object=$_trad field=source}}</td>
            <td>{{$_trad->_old_translation}}</td>
            <td>{{mb_value object=$_trad field=translation}} {{if $_trad->_old_translation != $_trad->translation}}<div class="info" style="display: inline;">{{tr}}CTranslationOverwrite-info-notCachedTrad{{/tr}}</div>{{/if}}</td>
            <td>{{tr}}CTranslationOverwrite.language.{{$_trad->language}}{{/tr}}</td>
          </tr>
        {{foreachelse}}
          <tr><td colspan="3" class="empty">{{tr}}CTranslationOverwrite.none{{/tr}}</td></tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>