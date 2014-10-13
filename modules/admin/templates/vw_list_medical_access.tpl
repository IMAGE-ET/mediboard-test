{{*
 * $Id$
 *  
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<script>
  refreshListMA = function(page) {
    var url = new Url("admin", "ajax_list_medical_access");
    url.addParam("guid", "{{$guid}}");
    url.addParam("page", page);
    url.requestUpdate('result_log_access_{{$guid}}');
  };

  Main.add(function() {
    refreshListMA({{$page}});
  });
</script>


<div id="result_log_access_{{$guid}}">

</div>
