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
    var url = new Url(this.module, "ajax_read_hl7v2_files");
    url.requestUpdate("read_hl7_file");
  },
  
  create: function () {
    var url = new Url(this.module, "ajax_write_hl7v2");
    url.requestUpdate("read_hl7_file");
  },
  
  triggerPatient: function (action) {
    var url = new Url();
    url.addParam("m", this.module);
    url.addParam("dosql", "do_trigger_patient_modification");
    url.addParam("action", action);
    url.requestUpdate("read_hl7_file", {method: "post"});
  },

  triggerSejour: function (action) {
    var url = new Url();
    url.addParam("m", this.module);
    url.addParam("dosql", "do_trigger_sejour_modification");
    url.addParam("action", action);
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

<button type="button" class="new" onclick="Action.triggerPatient('create')">
  Patient create
</button>

<button type="button" class="new" onclick="Action.triggerPatient('modify')">
  Patient modify
</button>

<button type="button" class="new" onclick="Action.triggerSejour('create')">
  Séjour create
</button>

<button type="button" class="new" onclick="Action.triggerSejour('modify')">
  Séjour modify
</button>

<div id="read_hl7_file"></div>