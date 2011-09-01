{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<!-- Import des tables -->
<script type="text/javascript">

var Action = {
  module: "hl7",
  
  read: function () {
    var url = new Url(this.module, "ajax_read_hl7v2_file");
    url.requestUpdate("read_hl7_file");
  },
}

</script>

<table class="tbl">
  <tr>
    <th class="category" style="width:15%">{{tr}}Action{{/tr}}</th>
    <th class="category">{{tr}}Status{{/tr}}</th>
  </tr>
  <tr>
    <td style="vertical-align: top;">
      <button type="button" class="new" onclick="Action.read()">
        {{tr}}Read{{/tr}}
      </button>
    </td>
    <td id="read_hl7_file"></td>
  </tr>
</table>