{{*
 * View Printing Sources
 *  
 * @category PRINTING
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
*}}

<script type="text/css">
  updateSelected = function(id) {
    removeSelected();
    var source = $("source-" + id);
    source.addClassName("selected");
  }

  removeSelected = function() {
    var source = $$(".osource.selected")[0];
    if (source) {
      source.removeClassName("selected");
    }
  }
</script>

<select id="type_source">
  <option value="CSourceLPR">
    {{tr}}CSourceLPR{{/tr}}
  </option>
  <option value="CSourceSMB">
    {{tr}}CSourceSMB{{/tr}}
  </option>
</select>

<button type="button" onclick="removeSelected(); editSource(0, $('type_source').value);" class="new">
  {{tr}}Create{{/tr}}
</button>

<table class="tbl">
  <tr>
    <th class="title" colspan="2">
    {{tr}}CSourceLPR.list{{/tr}}
    </th>
  </tr>
  <tr>
    <th class="category">
      {{tr}}CSourceLPR-name{{/tr}}
    </th>
    <th class="category">
      {{tr}}CSourceLPR.type{{/tr}}
    </th>
  </tr>
  
  {{foreach from=$sources item=_source}}
    <tr id='source-{{$_source->_id}}' class="osource {{if $_source->_id == $source_id}}selected{{/if}}">
      <td>
        <a href="#1" onclick="editSource('{{$_source->_id}}', '{{$_source->_class_name}}'); updateSelected('{{$_source->_id}}')">
         {{$_source->name}} 
        </a>
      </td>
      <td>
        {{tr}}{{$_source->_class_name}}{{/tr}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="2">
        {{tr}}CSourceLPR.no_sources{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>