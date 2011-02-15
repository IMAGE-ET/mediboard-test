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

<script type="text/javascript">
function editSource(id, class_name) {
  var action = "ajax_edit_source_";
  if (!class_name) {
    alert("{{tr}}CSourceLPR.choose_type{{/tr}}");
    return;
  }
  switch (class_name) {
    case "CSourceLPR":
      action += "lpr";
      break;
    case "CSourceSMB":
      action += "smb";
  }
  var url = new Url("printing", action);
  url.addParam("source_id", id);
  url.addParam("class_name", class_name);
  url.requestUpdate("edit_source");
}

function refreshList() {
  var url = new Url("printing", "ajax_list_sources");
  url.requestUpdate("list_sources");
}

testPrint = function(class_name, id) {
  var url = new Url("printing", "ajax_test_print");
  url.addParam("id", id);
  url.addParam("class_name", class_name);
  url.requestUpdate("result_print");
}
</script>

{{main}}
  refreshList();
  editSource('{{$source_id}}', '{{$class_name}}');
{{/main}}

<table class="main">
  <tr>
    <td id="list_sources" style="width: 45%;">
    </td>
    <!-- Création / Modification de la source -->
    <td id="edit_source">
    </td>
  </tr>
</table>