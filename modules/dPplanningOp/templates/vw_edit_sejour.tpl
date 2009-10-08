{{mb_include_script module="dPplanningOp" script="protocole_selector"}}

<script type="text/javascript">
ProtocoleSelector.init = function(){
  this.sForSejour     = true;
  this.sForm          = "editSejour";
  this.sChir_id       = "praticien_id";
  this.sServiceId     = "service_id";
  this.sDepassement   = "depassement";
  
  this.sType          = "type";
  this.sDuree_prevu   = "_duree_prevue";
  this.sConvalescence = "convalescence";
  this.sDP            = "DP";
  this.sRques_sej     = "rques";

  this.sProtoPrescAnesth = "_protocole_prescription_anesth_id";
  this.sProtoPrescChir   = "_protocole_prescription_chir_id";
  
  this.pop();
}

function toggleMode() {
  var trigger = $("modeExpert-trigger"),
      hiddenElement = $("modeExpert"),
      expert = !hiddenElement.visible();
  
  trigger.update(expert ? '{{tr}}button-COperation-modeExpert{{/tr}}' : '{{tr}}button-COperation-modeEasy{{/tr}}');
  hiddenElement.setVisible(expert);
}

{{if $app->user_prefs.mode_dhe == 0}}
  Main.add(toggleMode);
{{/if}}
</script> 

<table class="main">

  {{if $sejour->_id}}
  <tr>
    <td>
      <a class="button new" href="?m={{$m}}&amp;tab={{$tab}}&amp;sejour_id=0">
        {{tr}}CSejour.create{{/tr}}
      </a>
    </td>
    <td>
      <a class="button new" href="?m={{$m}}&amp;tab=vw_edit_planning&amp;operation_id=0&amp;sejour_id={{$sejour->_id}}">
        Programmer une nouvelle intervention dans ce séjour
      </a>
    </td>
  </tr>
  {{/if}}

  <tr>
    {{if $sejour->_id}}
    <th colspan="2" class="title modify">
      {{mb_include module=system template=inc_object_idsante400 object=$sejour}}
      {{mb_include module=system template=inc_object_history object=$sejour}}

      <div style="float:left;" class="noteDiv {{$sejour->_guid}}">
        <img alt="Ecrire une note" src="images/icons/note_grey.png" />
      </div>
      
      <button type="button" class="search" style="float: left;" onclick="ProtocoleSelector.init()">
        {{tr}}button-COperation-choixProtocole{{/tr}}
      </button>
      
      <button type="button" class="hslip" style="float: right;" onclick="toggleMode(this)" id="modeExpert-trigger">
        {{tr}}button-COperation-modeExpert{{/tr}}
      </button>
      
      Modification du séjour {{$sejour->_view}} {{if $sejour->_num_dossier}}[{{$sejour->_num_dossier}}]{{/if}}
    </th>
    {{else}}
    <th colspan="2" class="title">
      <button type="button" class="search" style="float: left;" onclick="ProtocoleSelector.init()">
        {{tr}}button-COperation-choixProtocole{{/tr}}
      </button>
      
      <button type="button" class="hslip" style="float: right;" onclick="toggleMode(this)" id="modeExpert-trigger">
        {{tr}}button-COperation-modeExpert{{/tr}}
      </button>
      Création d'un nouveau séjour
    </th>
    {{/if}}
  </tr>
  
  <tr>
    <td>
      {{include file="js_form_sejour.tpl"}}
      {{include file="inc_form_sejour.tpl" mode_operation=false}}
    </td>
    <td>
      {{include file="inc_infos_operation.tpl"}}
      {{include file="inc_infos_hospitalisation.tpl"}}
    </td>
  </tr>

</table>