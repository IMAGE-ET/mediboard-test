{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

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
    var oForm = getForm("editPlageTiming");
    var options = {
      exactMinutes: false, 
      minInterval : {{"CPlageOp"|static:minutes_interval}},
      minHours    : {{"CPlageOp"|static:hours_start}},
      maxHours    : {{"CPlageOp"|static:hours_stop}}
    };
    Calendar.regField(oForm.debut, null, options);
    Calendar.regField(oForm.fin  , null, options);
    options = {
      exactMinutes: false
    };
    Calendar.regField(oForm.temps_inter_op, null, options);
    reloadAllLists();
    reloadPersonnelPrevu();
  });

</script>
<table class="main">
  <tr>
    <th colspan="2">
      {{mb_include module=system template=inc_object_notes object=$plage}}
      <form name="editPlageTiming" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)" class="{{$plage->_spec}}">
        <input type="hidden" name="m" value="dPbloc" />
        <input type="hidden" name="dosql" value="do_plagesop_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="plageop_id" value="{{$plage->_id}}" />
        <input type="hidden" name="_repeat" value="1" />
        <input type="hidden" name="_type_repeat" value="simple" />
        <span style="float: right;">
          {{mb_field object=$plage field="temps_inter_op" hidden=true onchange="this.form.submit();"}}
          <br />
          entre chaque intervention
        </span>
        {{if $plage->chir_id}}
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$plage->_ref_chir}}
        {{else}}
        {{$plage->_ref_spec}}
        {{/if}}
        - {{$plage->date|date_format:$conf.longdate}}
        <br />
        {{$plage->_ref_salle->nom}}
        de {{mb_field object=$plage field="debut" hidden=true onchange="this.form.submit();"}}
        à  {{mb_field object=$plage field="fin"   hidden=true onchange="this.form.submit();"}}
      </form>
    </th>
  </tr>
  
  <tr>
    <th class="title">Ajout de personnes</th>
    <th class="title">Personnes en salle</th>
  </tr>
  <tr>
    <td>
      <table class="form">
        <tr>
          <td>
            <div class="big-info">Pour plus de simplicité, l'ajout de personnel se fait maintenant directement dans la case de droite.</div>
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