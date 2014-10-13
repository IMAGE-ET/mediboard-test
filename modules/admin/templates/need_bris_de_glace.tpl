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
  // @TODO vérifier si il existe une demande de bris de glace en cour

  Main.add(function() {
    var url = new Url("admin", "ajax_need_bris_de_glace");
    url.addParam("sejour_id", "{{$sejour->_id}}");
    url.requestModal();
    url.modalObject.observe("afterClose", function() {
      {{if $callback}}
        {{$callback}}();
      {{else}}
        window.location.reload();
      {{/if}}
    });
    //url.registerCalls();
  });

</script>


<div class="small-info">Bris de glace requis pour cette vue</div>