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
  afterSuccessB2G = function() {
    Url.queueRequests = false;
    var pendings_requests = $H(Url.pendingRequests).values();
    Url.pendingRequests = {}; // empty the list
    if (pendings_requests.length) {
      pendings_requests.each(function(_url) {
        _url.url.requestUpdate(_url.ioTarget, _url.oOptions);
      });
    }
    else {
      window.location.reload();
    }
  };

  initModal = function(sejour_id) {
    var url = new Url("admin", "ajax_need_bris_de_glace");
    url.addParam("sejour_id", "{{$sejour->_id}}");
    url.requestModal(null, null, {
      onClose : function() {
        Url.queueRequests = false;
      },
      dontQueue : true
    });
  };

  if (!Url.queueRequests) {
    Main.add(function () {
      {{if $modale}}
        initModal();
      {{/if}}
    });
  }

  // we request the save of nexts ajax
  Url.queueRequests = true;

</script>

{{if $modale}}
  <div class="small-info">
      Bris de glace requis pour cette vue
  </div>
{{else}}
  {{mb_include module=admin template=inc_vw_form_bris_de_glace sejour_id=$sejour->_id}}
{{/if}}