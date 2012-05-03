{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

  var printFicheAnesth = function(consultation_id, operation_id) {
    var url = new Url("dPcabinet", "print_fiche"); 
    url.addParam("consultation_id", consultation_id);
    url.addParam("operation_id", operation_id);
    url.popup(700, 500, "printFicheAnesth");
  }

  var chooseAnesthCallback = function() {
    location.reload(); 
  }
  
  var reloadAllLists = function() {
    reloadLeftList();
    reloadRightList();
  }
  
  var reloadLeftList = function() {
    var url = new Url("dPbloc", "ajax_list_intervs");
    url.addParam("plageop_id", {{$plage->_id}});
    url.addParam("list_type" , "left");
    url.requestUpdate("left_list");
  }
  
  var reloadRightList = function() {
    var url = new Url("dPbloc", "ajax_list_intervs");
    url.addParam("plageop_id", {{$plage->_id}});
    url.addParam("list_type" , "right");
    url.requestUpdate("right_list");
  }
  
  var submitOrder = function(oForm, side) {
    var callback = reloadAllLists;
    
    if(side == "left") {
      callback = reloadLeftList;
    }
    if(side == "right") {
      callback = reloadRightList;
    }
    
    return onSubmitFormAjax(oForm, callback);
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
            <!-- liste déroulante de choix de l'anesthesiste  et du personnel de bloc -->
            <form name="editPlage" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)" class="{{$plage->_spec}}">
            <input type="hidden" name="m" value="dPbloc" />
            <input type="hidden" name="dosql" value="do_plagesop_aed" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="plageop_id" value="{{$plage->_id}}" />
            <input type="hidden" name="_repeat" value="1" />
            <input type="hidden" name="_type_repeat" value="simple" />
          
            <select name="anesth_id" style="width: 10em;">
            <option value="">&mdash; Anesthésiste</option>
            {{foreach from=$listAnesth item=_anesth}}
            <option value="{{$_anesth->_id}}" {{if $plage->anesth_id == $_anesth->_id}} selected="selected" {{/if}}>{{$_anesth->_view}}</option>
            {{/foreach}}
            </select>
            <button class="tick" type="submit">Modifier</button>
            </form>
          </td>
        </tr>
        {{if $listPersIADE}}
        <tr>
          <td>
            <form name="editAffectationIADE" action="?m={{$m}}" method="post">
            <input type="hidden" name="m" value="dPpersonnel" />
            <input type="hidden" name="dosql" value="do_affectation_aed" />
            
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="object_id" value="{{$plage->_id}}" />
            <input type="hidden" name="object_class" value="{{$plage->_class}}" />
            <input type="hidden" name="realise" value="0" />
            <select name="personnel_id" style="width: 10em;">
              <option value="">&mdash; {{tr}}CPersonnel.emplacement.iade{{/tr}}</option>
              {{foreach from=$listPersIADE item=_personnelBloc}}
              <option value="{{$_personnelBloc->_id}}">{{$_personnelBloc->_ref_user->_view}}</option>
              {{/foreach}}
            </select>
            <button class="submit" type="submit">Ajouter personnel en salle</button>
            </form>
          </td>
        </tr>
        {{/if}}
        {{if $listPersAideOp}}
        <tr>
          <td>
            <form name="editAffectationAideOp" action="?m={{$m}}" method="post">
            <input type="hidden" name="m" value="dPpersonnel" />
            <input type="hidden" name="dosql" value="do_affectation_aed" />
            
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="object_id" value="{{$plage->_id}}" />
            <input type="hidden" name="object_class" value="{{$plage->_class}}" />
            <input type="hidden" name="realise" value="0" />
            <select name="personnel_id" style="width: 10em;">
              <option value="">&mdash; {{tr}}CPersonnel.emplacement.op{{/tr}}</option>
              {{foreach from=$listPersAideOp item=_personnelBloc}}
              <option value="{{$_personnelBloc->_id}}">{{$_personnelBloc->_ref_user->_view}}</option>
              {{/foreach}}
            </select>
            <button class="submit" type="submit">Ajouter personnel en salle</button>
            </form>
          </td>
        </tr>
        {{/if}}
        {{if $listPersPanseuse}}
        <tr>
          <td>
            <form name="editAffectationPanseuse" action="?m={{$m}}" method="post">
            <input type="hidden" name="m" value="dPpersonnel" />
            <input type="hidden" name="dosql" value="do_affectation_aed" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="object_id" value="{{$plage->_id}}" />
            <input type="hidden" name="object_class" value="{{$plage->_class}}" />
            <input type="hidden" name="realise" value="0" />
            <select name="personnel_id" style="width: 10em;">
              <option value="">&mdash; {{tr}}CPersonnel.emplacement.op_panseuse{{/tr}}</option>
              {{foreach from=$listPersPanseuse item=_personnelBloc}}
              <option value="{{$_personnelBloc->_id}}">{{$_personnelBloc->_ref_user->_view}}</option>
              {{/foreach}}
            </select>
            <button class="submit" type="submit">Ajouter personnel en salle</button>
            </form>
          </td>
        </tr>
        {{/if}}
      </table>   
    </td>
    
    <td>
      <table class="tbl">
        <tr>
          <th>Anesthésiste</th>
          {{if $plage->anesth_id}}
            <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$plage->_ref_anesth}}</td>
          {{else}}
            <td class="empty">Aucun anesthésiste</td>
          {{/if}}
        </tr>
        {{if $affectations_plage.iade}}
        <tr>
          <th>{{tr}}CPersonnel.emplacement.iade{{/tr}}</th>
          <td class="text">
            <!-- div qui affiche le personnel de bloc -->
            {{foreach from=$affectations_plage.iade item=_affectation}}
              <form name="supAffectation-{{$_affectation->_id}}" action="?m={{$m}}" method="post">
                <input type="hidden" name="m" value="dPpersonnel" />
                <input type="hidden" name="dosql" value="do_affectation_aed" />
                <input type="hidden" name="affect_id" value="{{$_affectation->_id}}" />
                <input type="hidden" name="del" value="1" />
                <button class="cancel" type="submit">{{$_affectation->_ref_personnel->_ref_user->_view}}</button>
              </form>
            {{/foreach}}
          </td>
        </tr>
        {{/if}}
        {{if $affectations_plage.op}}
        <tr>
          <th>{{tr}}CPersonnel.emplacement.op{{/tr}}</th>
          <td class="text">
            <!-- div qui affiche le personnel de bloc -->
            {{foreach from=$affectations_plage.op item=_affectation}}
              <form name="supAffectation-{{$_affectation->_id}}" action="?m={{$m}}" method="post">
                <input type="hidden" name="m" value="dPpersonnel" />
                <input type="hidden" name="dosql" value="do_affectation_aed" />
                <input type="hidden" name="affect_id" value="{{$_affectation->_id}}" />
                <input type="hidden" name="del" value="1" />
                <button class="cancel" type="submit">{{$_affectation->_ref_personnel->_ref_user->_view}}</button>
              </form>
            {{/foreach}}
          </td>
        </tr>
        {{/if}}
        {{if $affectations_plage.op_panseuse}}
        <tr>
          <th>{{tr}}CPersonnel.emplacement.op_panseuse{{/tr}}</th>
          <td class="text">
            <!-- div qui affiche le personnel de bloc -->
            {{foreach from=$affectations_plage.op_panseuse item=_affectation}}
              <form name="supAffectation-{{$_affectation->_id}}" action="?m={{$m}}" method="post">
                <input type="hidden" name="m" value="dPpersonnel" />
                <input type="hidden" name="dosql" value="do_affectation_aed" />
                <input type="hidden" name="affect_id" value="{{$_affectation->_id}}" />
                <input type="hidden" name="del" value="1" />
                <button class="cancel" type="submit">{{$_affectation->_ref_personnel->_ref_user->_view}}</button>
              </form>
            {{/foreach}}
          </td>
        </tr>
        {{/if}}
      </table>
    </td> 
  </tr>
  <tr>
    <td class="halfPane" id="left_list">
    </td>
    <td class="halfPane" id="right_list">
    </td>
  </tr>
</table>