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

<script>
  updateSelected = function(guid) {
    removeSelected();
    $("type_source").selectedIndex = 0;
    var source = $("source-" + guid);
    source.addClassName("selected");
  };

  removeSelected = function() {
    var source = $$(".osource.selected")[0];
    if (source) {
      source.removeClassName("selected");
    }
  };
</script>

<select id="type_source" onchange="removeSelected(); editSource(0, this.value);">
  <option value="">
    &mdash; {{tr}}CSourceLPR.choose_type{{/tr}}
  </option>
  <option value="CSourceLPR">
    {{tr}}CSourceLPR{{/tr}}
  </option>
  <option value="CSourceSMB">
    {{tr}}CSourceSMB{{/tr}}
  </option>
</select>

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
    <tr id='source-{{$_source->_guid}}' class="osource {{if $_source->_id == $source_id && $_source->_class == $class}}selected{{/if}}">
      <td>
        <a href="#1" onclick="editSource('{{$_source->_id}}', '{{$_source->_class}}'); updateSelected('{{$_source->_guid}}')">
         {{$_source->name}} 
        </a>
      </td>
      <td>
        {{tr}}{{$_source->_class}}{{/tr}}
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