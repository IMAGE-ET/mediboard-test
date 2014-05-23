{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage ameli
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision$
 *}}

<script type="text/javascript">
  importAATI = function() {
    new Url("ameli", "ajax_do_add_aati").requestUpdate("import_aati");
  };
</script>

{{mb_include module=system template=configure_dsn dsn=ameli}}

<table class="tbl">
  <tr>
    <th>{{tr}}Action{{/tr}}</th>
    <th>{{tr}}Status{{/tr}}</th>
  </tr>
  <tr>
    <td><button class="tick" onclick="importAATI();">Importer les référentiels AATI</button></td>
    <td id="import_aati"></td>
  </tr>
</table>
