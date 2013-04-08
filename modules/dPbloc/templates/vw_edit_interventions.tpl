{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=bloc script=edit_planning}}

<script>
  printFicheAnesth = function(dossier_anesth_id, operation_id) {
    var url = new Url("cabinet", "print_fiche"); 
    url.addParam("dossier_anesth_id", dossier_anesth_id);
    url.addParam("operation_id", operation_id);
    url.popup(700, 500, "printFicheAnesth");
  }

  chooseAnesthCallback = function() {
    location.reload(); 
  }
  
  var reloadAllLists = function() {
    reloadLeftList();
    reloadRightList();
  }
  
  reloadLeftList = function() {
    var url = new Url("bloc", "ajax_list_intervs");
    url.addParam("plageop_id", {{$plage->_id}});
    url.addParam("list_type" , "left");
    url.requestUpdate("left_list");
  }
  
  reloadRightList = function() {
    var url = new Url("bloc", "ajax_list_intervs");
    url.addParam("plageop_id", {{$plage->_id}});
    url.addParam("list_type" , "right");
    url.requestUpdate("right_list");
  }
  
  submitOrder = function(oForm, side) {
    var callback = reloadAllLists;
    
    if(side == "left") {
      callback = reloadLeftList;
    }
    if(side == "right") {
      callback = reloadRightList;
    }
    
    return onSubmitFormAjax(oForm, callback);
  }

  extraInterv = function(op_id) {
    var url = new Url("bloc", "ajax_edit_extra_interv");
    url.addParam("op_id", op_id);
    url.requestModal(700, 400);
    url.modalObject.observe("afterClose", reloadAllLists);
  }
  
  reloadModifPlage = function() {
    var url = new Url("bloc", "ajax_modif_plage");
    url.addParam("plageop_id", {{$plage->_id}});
    url.requestUpdate("modif_plage", reloadAllLists);
  }
  
  reloadPersonnelPrevu = function() {
    var url = new Url("bloc", "ajax_view_personnel_plage");
    url.addParam("plageop_id", {{$plage->_id}});
    url.requestUpdate("personnel_en_salle");
  }

  reloadPersonnel = function(operation_id){
    var url = new Url("salleOp", "httpreq_vw_personnel");
    url.addParam("operation_id", operation_id);
    url.addParam("in_salle", 0);
    url.requestUpdate("listPersonnel");
  }

  Main.add(function(){
    reloadModifPlage();
    reloadPersonnelPrevu();
  });

</script>
<table class="main">
  <tr>
    <th class="title" colspan="2">
      {{mb_include module=system template=inc_object_notes object=$plage}}
      {{mb_include module=system template=inc_object_idsante400 object=$plage}}
      {{mb_include module=system template=inc_object_history object=$plage}}
      {{if $plage->chir_id}}
        {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$plage->_ref_chir}}
      {{else}}
        {{$plage->_ref_spec}}
      {{/if}}
      - {{$plage->date|date_format:$conf.longdate}}
      - {{$plage->_ref_salle->nom}}
    </th>
  </tr>
  <tr>
    <td>
      <table class="form">
        <tr>
          <td>
            <div id="modif_plage">
            </div>
            <div class="small-info">Pour plus de simplicité, l'ajout de personnel se fait maintenant directement dans la case de droite.</div>
          </td>
        </tr>
      </table>   
    </td>
    <td id="personnel_en_salle">
    </td> 
  </tr>
  <tr>
    <td class="halfPane" id="left_list">
    </td>
    <td class="halfPane" id="right_list">
    </td>
  </tr>
</table>