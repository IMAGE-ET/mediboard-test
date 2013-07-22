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
<table class="main">
  <tr>
    <td id="listTrad">
      <table class="tbl">
        <tr>
          <th class="narrow"></th>
          <th>{{tr}}CTranslationOverwrite-source{{/tr}}</th>
          <th>{{tr}}CTranslationOverwrite-_old_translation{{/tr}}</th>
          <th>{{tr}}CTranslationOverwrite-translation{{/tr}}</th>
          <th>{{tr}}CTranslationOverwrite-language{{/tr}}</th>
        </tr>
        {{foreach from=$translations_bdd item=_trad}}
          <tr>
            <td><button class="button edit notext"  onclick="editTrad({{$_trad->_id}})">{{tr}}Edit{{/tr}}</button></td>
            <td>{{mb_value object=$_trad field=source}}</td>
            <td>{{$_trad->_old_translation}}</td>
            <td {{if !$_trad->_in_cache}}class="warning"{{/if}}><strong>{{mb_value object=$_trad field=translation}}</strong></td>
            <td><img src="images/icons/flag-{{$_trad->language}}.png" alt=""/> {{tr}}CTranslationOverwrite.language.{{$_trad->language}}{{/tr}}</td>
          </tr>
        {{foreachelse}}
          <tr><td colspan="4" class="empty">{{tr}}CTranslationOverwrite.none{{/tr}}</td></tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>