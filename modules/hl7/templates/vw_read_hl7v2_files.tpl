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

<button type="button" class="new" onclick="Action.read()">
  {{tr}}Read{{/tr}}
</button>

<div id="read_hl7_file"></div>