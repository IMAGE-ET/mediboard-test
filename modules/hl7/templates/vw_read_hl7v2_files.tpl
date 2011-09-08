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
  
  create: function () {
    var url = new Url(this.module, "ajax_write_hl7v2");
    url.requestUpdate("read_hl7_file");
  },
  
  triggerPatientModif: function () {
    var url = new Url();
    url.addParam("m", this.module);
    url.addParam("dosql", "do_trigger_object_modification");
    url.requestUpdate("read_hl7_file", {method: "post"});
  }
}

</script>

<button type="button" class="new" onclick="Action.read()">
  {{tr}}Read{{/tr}}
</button>

<button type="button" class="new" onclick="Action.create()">
  {{tr}}Creation{{/tr}}
</button>

<button type="button" class="new" onclick="Action.triggerPatientModif()">
  Evt Patient
</button>

<div id="read_hl7_file"></div>