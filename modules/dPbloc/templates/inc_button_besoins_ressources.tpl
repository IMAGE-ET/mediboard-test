{{*
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPbloc
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

{{mb_default var=type value=operation_id}}
{{mb_default var=usage value=0}}
{{unique_id var=uniq_id}}

<script type="text/javascript">
  // Dans le cas d'une intervention, il faut vérifier
  // que pour chaque type de ressource exprimé par un besoin, une ressource
  // au moment de l'intervention est disponible.
  {{if $type == "operation_id"}}
    Main.add(function() {
      checkRessources("{{$object_id}}");
    });
  {{/if}}
  
  editBesoins = function (object_id) {
    var url = new Url("dPbloc", "ajax_edit_besoins");
    url.addParam("type", "{{$type}}");
    url.addParam("object_id", object_id);
    url.addParam("usage", "{{$usage}}");
    url.requestModal(500, 380);
    
    {{if $type == "operation_id"}}
    // Arprès fermeture de la modale, on réactualise
    // la couleur de la bordure des boutons
    
      url.modalObject.observe("afterClose", function() {
        checkRessources(object_id);
      });
    {{/if}}
  }
  
  checkRessources = function(object_id) {
    var url = new Url("dPbloc", "ajax_check_ressources");
    url.addParam("type", "{{$type}}");
    url.addParam("object_id", object_id);
    url.requestJSON(function(color) {
      // Ajout d'une bordure sur le bouton suivant l'état des besoins
      $$(".ressource_bouton_"+object_id).each(function(button) {
        button.setStyle({border: "2px solid #"+color});
      });
    });
  }
</script>

<button type="button" class="search ressource_bouton_{{$object_id}}" onclick="editBesoins('{{$object_id}}');">Matériel</button>
