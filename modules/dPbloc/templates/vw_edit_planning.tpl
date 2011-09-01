{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

{{if $can->edit}}

function showAlerte() {
  var url = new Url("dPbloc", "vw_alertes");
  url.addParam("date", "{{$date}}");
  url.addParam("type", "jour");
  url.addParam("bloc_id", "{{$bloc->_id}}");
  url.popup(600, 500, "Alerte");
}

function checkPlage() {
  var form = getForm('editFrm');
  
  if (!checkForm(form)) {
    return false;
  }
    
  if (form.chir_id.value == "" && form.spec_id.value == "") {
    alert("Merci de choisir un chirurgien ou une spécialité");
    form.chir_id.focus();
    return false;
  }
  
  return true;
}

function popPlanning(debut) {
  var url = new Url("dPbloc", "view_planning");
  url.addParam("_date_min", debut);
  url.addParam("_date_max", debut);
  url.addParam("salle"    , 0);
  url.popup(900, 550, "Planning");
}

Main.add(function(){
  var oForm = getForm('editFrm');
  Calendar.regField(oForm.date);
  Calendar.regField(oForm.temps_inter_op);
  var options = {
    exactMinutes: false, 
    minInterval: {{"CPlageOp"|static:minutes_interval}},
    minHours: {{"CPlageOp"|static:hours_start}},
    maxHours: {{"CPlageOp"|static:hours_stop}}
  };
  Calendar.regField(oForm.debut, null, options);
  Calendar.regField(oForm.fin, null, options);
});
{{/if}}
</script>
<table class="main">
  <tr>
    <td class="greedyPane" style="text-align:center;">
      {{if $can->edit}}
      {{if $nbIntervNonPlacees || $nbIntervHorsPlage || $nbAlertesInterv}}
        <div class="warning" style="float: right; text-align:left;">
          <a href="#nothing" onclick="showAlerte()">
          {{if $nbAlertesInterv}}
            {{$nbAlertesInterv}} alerte(s) sur des interventions
            <br />
          {{/if}}
          {{if $nbIntervNonPlacees}}
            {{$nbIntervNonPlacees}} intervention(s) non validée(s)
            <br />
          {{/if}}
          {{if $nbIntervHorsPlage}}
            {{$nbIntervHorsPlage}} intervention(s) hors plage
            <br />
          {{/if}}
          </a>
        </div>
      {{/if}}
      <button class="print" onclick="popPlanning('{{$date}}');" style="font-weight: bold;">
        {{$date|date_format:"%A %d %B"}}
      </button>
      {{/if}}
      
      <form action="?" name="selection" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="vw_edit_planning" />
        <select name="bloc_id" onchange="this.form.submit();">
          {{foreach from=$listBlocs item=curr_bloc}}
            <option value="{{$curr_bloc->_id}}" {{if $curr_bloc->_id == $bloc->_id}}selected="selected"{{/if}}>
              {{$curr_bloc->nom}}
            </option>
          {{foreachelse}}
            <option value="" disabled="disabled">{{tr}}CBlocOperatoire.none{{/tr}}</option>
          {{/foreach}}
        </select>
      </form>
      
      <table id="planningBloc">
      {{assign var=typeVuePlanning value="day"}}
      {{assign var=curr_day value=$date}}
      {{include file="inc_planning_day.tpl"}}
      </table>
      {{if $can->edit}}
      {{if $plagesel->plageop_id}}
      <a class="button new" href="?m=dPbloc&amp;tab=vw_edit_planning&amp;plageop_id=0">
        {{tr}}CPlageOp-title-create{{/tr}}
      </a>
      {{/if}}
      {{if $can->edit}}
      <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkPlage()" class="{{$plagesel->_spec}}">
      <input type="hidden" name="dosql" value="do_plagesop_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="plageop_id" value="{{$plagesel->plageop_id}}" />

      <table class="form">
        <tr>
          {{if $plagesel->plageop_id}}
          <th class="title modify" colspan="6">
				    {{mb_include module=system template=inc_object_idsante400 object=$plagesel}}
				    {{mb_include module=system template=inc_object_history object=$plagesel}}
            {{tr}}CPlageOp-title-modify{{/tr}}
          {{else}}
          <th class="title" colspan="6">
            {{tr}}CPlageOp-title-create{{/tr}}
          {{/if}}
          </th>
        </tr>
        <tr>
         <th>{{mb_label object=$plagesel field="chir_id"}}</th>
         <td>
          <select name="chir_id" class="{{$plagesel->_props.chir_id}}" style="width: 15em;">
            <option value="">&mdash; Choisir un chirurgien</option>
            {{foreach from=$chirs item=_chir}}
              <option class="mediuser" style="border-color: #{{$_chir->_ref_function->color}};" value="{{$_chir->user_id}}" 
              {{if $plagesel->chir_id == $_chir->user_id}}selected="selected"{{/if}}>
                {{$_chir->_view}}
              </option>
            {{/foreach}}
          </select>
        </td>
        <th>{{mb_label object=$plagesel field="salle_id"}}</th>
        <td>
          <select name="salle_id" class="{{$plagesel->_props.salle_id}}" style="width: 15em;">
            <option value="">&mdash; {{tr}}CSalle.select{{/tr}}</option>
            {{if $plagesel->_id}}
              {{foreach from=$listBlocs item=curr_bloc}}
                <optgroup label="{{$curr_bloc->_view}}">
                {{foreach from=$curr_bloc->_ref_salles item=curr_salle}}
                  <option value="{{$curr_salle->_id}}" {{if $curr_salle->_id == $plagesel->salle_id}}selected="selected"{{/if}}>
                    {{$curr_salle}}
                  </option>
                {{foreachelse}}
                  <option value="" disabled="disabled">{{tr}}CSalle.none{{/tr}}</option>
                {{/foreach}}
                </optgroup>
              {{/foreach}}
            {{else}}
              {{foreach from=$bloc->_ref_salles item=curr_salle}}
                <option value="{{$curr_salle->_id}}" {{if $curr_salle->_id == $plagesel->salle_id}}selected="selected"{{/if}}>
                  {{$curr_salle}}
                </option>
              {{foreachelse}}
                <option value="" disabled="disabled">{{tr}}CSalle.none{{/tr}}</option>
              {{/foreach}}
            {{/if}}
          </select>
        </td>
      </tr>
      <tr>
        <th>{{mb_label object=$plagesel field="spec_id"}}</th>
        <td>
          <select name="spec_id" class="{{$plagesel->_props.spec_id}}" style="width: 15em;">
            <option value="">&mdash; Choisir une spécialité</option>
            {{foreach from=$specs item=spec}}
              <option value="{{$spec->function_id}}" class="mediuser" style="border-color: #{{$spec->color}};"
              {{if $spec->function_id == $plagesel->spec_id}}selected="selected"{{/if}}>
                {{$spec->text}}
              </option>
            {{/foreach}}
          </select>
        </td>
        <th>{{mb_label object=$plagesel field="date"}}</th>
        <td>
          {{if $plagesel->plageop_id}}
          <input type="hidden" name="date" value="{{$plagesel->date}}" />
          {{else}}
          <input type="hidden" name="date" value="{{$date}}" />
          {{/if}}
        </td>
      </tr>
      
      <tr>
        <th>{{mb_label object=$plagesel field="anesth_id"}}</th>
        <td>
          <select name="anesth_id" style="width: 15em;">
            <option value="">&mdash; Choisir un anesthésiste</option>
            {{foreach from=$anesths item=anesth}}
            <option value="{{$anesth->user_id}}" {{if $plagesel->anesth_id == $anesth->user_id}} selected="selected" {{/if}} >
              {{$anesth->_view}}
            </option>
            {{/foreach}}
    	  </select>
        </td>
        <th>{{mb_label object=$plagesel field="debut"}}</th>
        <td>{{mb_field object=$plagesel field="debut" hidden=true}}</td>
      </tr>
      
      <tr>
        <th>{{mb_label object=$plagesel field="unique_chir"}}</th>
        <td>{{mb_field object=$plagesel field="unique_chir"}}</td>
    	  <th>{{mb_label object=$plagesel field="fin"}}</th>
        <td>{{mb_field object=$plagesel field="fin" hidden=true}}</td>
      </tr>
      <tr>
        <th>
          <label for="_repeat" title="Nombre de plages à créer">Nombre de plages</label>
        </th>
        <td>
          <input type="text" class="notNull num min|1" name="_repeat" size="1" value="1" />
        </td>
        <th>{{mb_label object=$plagesel field="temps_inter_op"}}</th>
        <td>{{mb_field object=$plagesel field="temps_inter_op"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$plagesel field="_type_repeat"}}</th>
        <td>{{mb_field object=$plagesel field="_type_repeat" style="width: 15em;" typeEnum="select"}}</td>
        <th>{{mb_label object=$plagesel field="delay_repl"}}</th>
        <td>{{mb_field object=$plagesel field="delay_repl" size=1 increment=true form="editFrm" min=0}} jours</td>
      </tr>
      <tr>
        <th>{{mb_label object=$plagesel field="max_intervention"}}</th>
        <td>{{mb_field object=$plagesel field="max_intervention" size=1 increment=true form="editFrm" min=0}}</td>
        <th>{{mb_label object=$plagesel field="spec_repl_id"}}</th>
        <td>
          <select name="spec_repl_id" class="{{$plagesel->_props.spec_repl_id}}" style="width: 15em;">
            <option value="">&mdash; Spécialité de remplacement</option>
            {{foreach from=$specs item=spec}}
              <option value="{{$spec->function_id}}" class="mediuser" style="border-color: #{{$spec->color}};"
              {{if $spec->function_id == $plagesel->spec_repl_id}}selected="selected"{{/if}}>
                {{$spec->text}}
              </option>
            {{/foreach}}
          </select>
        </td>
      </tr>
      <tr>
        <td colspan="4" class="text">
          <div class="small-info">
            Pour modifier plusieurs plages (nombre de plages > 1),
            veuillez ne pas changer les champs début et fin en même temps
          </div>
        </td>
      </tr>
      <tr>
        <td class="button" colspan="4">
        {{if $plagesel->plageop_id}}
          <button type="submit" class="modify">Modifier</button>
        {{else}}
          <button type="submit" class="new">Ajouter</button>
        {{/if}}
        </td>
      </tr>
    </table>
    </form>

    {{if $plagesel->plageop_id}}
      <form name="removeFrm" action="?m={{$m}}" method="post" class="{{$plagesel->_spec}}">
      <input type="hidden" name="dosql" value="do_plagesop_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="plageop_id" value="{{$plagesel->plageop_id}}" /> 
      <table class="form">
        <tr>
          <th class="title" colspan="2">Supprimer la plage opératoire</th>
        </tr>  
        <tr>
          <th>Supprimer cette plage pendant</th> 
          <td><input type="text" name="_repeat" size="1" value="1" /> semaine(s)</td>
        </tr>   
        <tr>
          <td class="button" colspan="2">
            <button class="trash" type="button" onclick="confirmDeletion(this.form, {typeName:'la plage opératoire',objName:'{{$plagesel->_view|smarty:nodefaults|JSAttribute}}'})">
              Supprimer
            </button>
          </td>
        </tr>
      </table>
      </form>
    {{/if}}
    {{/if}}
    {{/if}}
   </td>
   <td>
     {{include file="inc_legende_planning.tpl" listSpec=$specs}}
   </td>
  </tr>
</table>