<script language="JavaScript" type="text/javascript">
function flipChambre(chambre_id) {
  flipElementClass("chambre" + chambre_id, "chambrecollapse", "chambreexpand", "chambres");
}

function flipSejour(sejour_id) {
  flipElementClass("sejour" + sejour_id, "sejourcollapse", "sejourexpand");
}

var selected_hospitalisation = null;

function selectHospitalisation(sejour_id) {
  var element = document.getElementById("hospitalisation" + selected_hospitalisation);
  if (element) {
    element.checked = false;
  }

  selected_hospitalisation = sejour_id;
 
  submitAffectation();
}

var selected_lit = null;

function selectLit(lit_id) {
  var element = document.getElementById("lit" + selected_lit);
  if (element) {
    element.checked = false;
  }

  selected_lit = lit_id;
  
  submitAffectation();
}

function submitAffectation() {
  if (selected_lit && selected_hospitalisation) {
    var form = eval("document.addAffectation" + selected_hospitalisation);
    form.lit_id.value = selected_lit;
    form.submit();
  }
}

function submitAffectationSplit(form) {
  form._new_lit_id.value = selected_lit;
  if (!selected_lit) {
    alert("Veuillez sélectionner un nouveau lit et revalider la date");
    return;
  }
  
  if (form._date_split.value <= form.entree.value || 
      form._date_split.value >= form.sortie.value) {
    var msg = "La date de déplacement (" + form._date_split.value + ") doit être comprise entre";
    msg += "\n- la date d'entrée: " + form.entree.value; 
    msg += "\n- la date de sortie: " + form.sortie.value;
    alert(msg);
    return;
  }
  
  form.submit();
}

function setupCalendar(affectation_id) {
  var form = null;
  
  if (form = eval("document.entreeAffectation" + affectation_id)) {
    Calendar.setup( {
        inputField  : form.name + "_entree",
        ifFormat    : "%Y-%m-%d %H:%M",
        button      : form.name + "__trigger_entree",
        showsTime   : true,
        onUpdate    : function() { 
          if (calendar.dateClicked) {
            form = eval("document.entreeAffectation" + affectation_id);
            form.submit();
          }
        }
      }
    );
  }

  
  if (form = eval("document.sortieAffectation" + affectation_id)) {
    Calendar.setup( {
        inputField  : form.name + "_sortie",
        ifFormat    : "%Y-%m-%d %H:%M",
        button      : form.name + "__trigger_sortie",
        showsTime   : true,
        onUpdate    : function() { 
          if (calendar.dateClicked) {
            form = eval("document.sortieAffectation" + affectation_id);
            form.submit();
          }
        }
      }
    );
  }
  
  if (form = eval("document.splitAffectation" + affectation_id)) {
    Calendar.setup( {
        inputField  : form.name + "__date_split",
        ifFormat    : "%Y-%m-%d %H:%M",
        button      : form.name + "__trigger_split",
        showsTime   : true,
        onUpdate    : function() { 
          if (calendar.dateClicked) {
            form = eval("document.splitAffectation" + affectation_id);
            submitAffectationSplit(form);
          }
        }
      }
    );
  }

}

function pageMain() {
  {{if $dialog != 1}}
  regRedirectFlatCal("{{$date}}", "index.php?m={{$m}}&tab={{$tab}}&date=");
  {{/if}}
}

function popPlanning() {
  url = new Url;
  url.setModuleAction("dPhospi", "vw_affectations");
  url.popup(700, 550, "Planning");
}

function showLegend() {
  url = new Url;
  url.setModuleAction("dPhospi", "legende");
  url.popup(500, 500, "Legend");
}

</script>

<table class="main">

<tr>
  <td>
    <a href="javascript:showLegend()" class="buttonsearch">Légende</a>
  </td>
  <th>
    <a href="javascript:{{if $dialog}}window.print(){{else}}popPlanning(){{/if}}">
      Planning du {{$date|date_format:"%A %d %B %Y"}} : {{$totalLits}} place(s) de libre
    </a>
  </th>
  {{if $dialog != 1}}
  <td>
    <form name="chgMode" action="?m={{$m}}" method="get">
    <input type="hidden" name="m" value="{{$m}}" />
    <label for="mode" title="Veuillez choisir un type de vue">Type de vue</label>
    <select name="mode" onchange="submit()">
      <option value="0" {{if $mode == 0}}selected="selected"{{/if}}>Vue instantanée</option>
      <option value="1" {{if $mode == 1}}selected="selected"{{/if}}>Vue de la journée</option>
    </select>
    </form>
  </td>
  {{/if}}
</tr>

<tr>
  <td class="greedyPane" colspan="2">

    <table class="tbl">

    <tr>
    {{foreach from=$services item=curr_service}}
      <th>
        <a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;service_id={{$curr_service->service_id}}">
        {{$curr_service->nom}} / {{$curr_service->_nb_lits_dispo}} lit(s) dispo</a>
      </th>
    {{/foreach}}
    </tr>

    <tr>
    {{foreach from=$services item=curr_service}}
      <td>
      {{foreach from=$curr_service->_ref_chambres item=curr_chambre}}
        <table class="chambrecollapse" id="chambre{{$curr_chambre->chambre_id}}">
          <tr>
            <th class="chambre" colspan="2" onclick="javascript:flipChambre({{$curr_chambre->chambre_id}});
                {{foreach from=$curr_chambre->_ref_lits item=curr_lit}}
                {{foreach from=$curr_lit->_ref_affectations item=curr_aff}}
                setupCalendar({{$curr_aff->affectation_id}});
                {{/foreach}}
                {{/foreach}}">
              {{if $curr_chambre->_overbooking}}
              <img src="modules/{{$m}}/images/surb.png" alt="warning" title="Over-booking: {{$curr_chambre->_overbooking}} collisions" />
              {{/if}}

              {{if $curr_chambre->_ecart_age > 15}}
              <img src="modules/{{$m}}/images/age.png" alt="warning" title="Ecart d'âge important: {{$curr_chambre->_ecart_age}} ans" />
              {{/if}}

              {{if $curr_chambre->_genres_melanges}}
              <img src="modules/{{$m}}/images/sexe.png" alt="warning" title="Sexes opposés" />
              {{/if}}

              {{if $curr_chambre->_chambre_seule}}
              <img src="modules/{{$m}}/images/seul.png" alt="warning" title="Chambre seule obligatoire" />
              {{/if}}
              
              {{if $curr_chambre->_chambre_double}}
              <img src="modules/{{$m}}/images/double.png" alt="warning" title="Chambre double possible" />
              {{/if}}

              {{if $curr_chambre->_conflits_chirurgiens}}
              <img src="modules/{{$m}}/images/prat.png" alt="warning" title="{{$curr_chambre->_conflits_chirurgiens}} Conflit(s) de praticiens" />
              {{/if}}

              {{if $curr_chambre->_conflits_pathologies}}
              <img src="modules/{{$m}}/images/path.png" alt="warning" title="{{$curr_chambre->_conflits_pathologies}} Conflit(s) de pathologies" />
              {{/if}}

              <strong><a name="chambre{{$curr_chambre->chambre_id}}">{{$curr_chambre->nom}}</a></strong>
            </th>
          </tr>
          {{foreach from=$curr_chambre->_ref_lits item=curr_lit}}
          <tr class="lit" >
            <td colspan="1">
              {{if $curr_lit->_overbooking}}
              <img src="modules/{{$m}}/images/warning.png" alt="warning" title="Over-booking: {{$curr_lit->_overbooking}} collisions" />
              {{/if}}
              {{$curr_lit->nom}}
            </td>
            <td class="action">
              <input type="radio" id="lit{{$curr_lit->lit_id}}" onclick="selectLit({{$curr_lit->lit_id}})" />
            </td>
          </tr>
          {{foreach from=$curr_lit->_ref_affectations item=curr_affectation}}
          {{eval var=$curr_affectation->_ref_sejour->_ref_patient->_view assign="patient_view"}}
          <tr class="patient">
            {{if $curr_affectation->confirme}}
            <td class="text" style="background-image:url(modules/{{$m}}/images/ray.gif); background-repeat:repeat;">
            {{else}}
            <td class="text">
            {{/if}}
              {{if !$curr_affectation->_ref_sejour->entree_reelle && !($curr_affectation->_ref_prev->affectation_id && $curr_affectation->_ref_prev->effectue == 0)}}
                <font style="color:#a33">
              {{else}}
                {{if $curr_affectation->_ref_sejour->septique == 1}}
                <font style="color:#3a3">
                {{else}}
                <font>
                {{/if}}
              {{/if}}
              
              {{if $curr_affectation->_ref_sejour->type == "ambu"}}
              <img src="modules/{{$m}}/images/X.png" alt="X" title="Sortant ce soir" />
              {{elseif $curr_affectation->sortie|date_format:"%Y-%m-%d" == $demain}}
                {{if $curr_affectation->_ref_next->affectation_id}}
                <img src="modules/{{$m}}/images/OC.png" alt="OC" title="Sortant demain" />
                {{else}}
                <img src="modules/{{$m}}/images/O.png" alt="O" title="Sortant demain" />
                {{/if}}
              {{elseif $curr_affectation->sortie|date_format:"%Y-%m-%d" == $date}}
                {{if $curr_affectation->_ref_next->affectation_id}}
                <img src="modules/{{$m}}/images/OoC.png" alt="OoC" title="Sortant aujourd'hui" />
                {{else}}
                <img src="modules/{{$m}}/images/Oo.png" alt="Oo" title="Sortant aujourd'hui" />
                {{/if}}
              {{/if}}
              {{if $curr_affectation->_ref_sejour->type == "ambu"}}
              <em>{{$patient_view}}</em>
              {{else}}
              <strong>{{$patient_view}}</strong>
              {{/if}}
              {{if (!$curr_affectation->_ref_sejour->entree_reelle) || ($curr_affectation->_ref_prev->affectation_id && $curr_affectation->_ref_prev->effectue == 0)}}
              {{$curr_affectation->entree|date_format:"%d/%m %Hh%M"}}
              {{/if}}
            </font>
            </td>
            <td class="action" style="background:#{{$curr_affectation->_ref_sejour->_ref_praticien->_ref_function->color}}">
              {{$curr_affectation->_ref_sejour->_ref_praticien->_shortview}}
            </td>
          </tr>
          <tr class="dates">
            {{if $curr_affectation->_ref_prev->affectation_id}}
            <td class="text">
              <em>Déplacé</em> (chambre: {{$curr_affectation->_ref_prev->_ref_lit->_ref_chambre->nom}}):
              {{$curr_affectation->entree|date_format:"%A %d %B %H:%M"}}
              ({{$curr_affectation->_entree_relative}} jours)
            <td class="action">

              <form name="rmvAffectation{{$curr_affectation->affectation_id}}" action="?m={{$m}}" method="post">

              <input type="hidden" name="dosql" value="do_affectation_aed" />
              <input type="hidden" name="del" value="1" />
              <input type="hidden" name="affectation_id" value="{{$curr_affectation->affectation_id}}" />

              </form>
              
              <a style="float: right;" href="javascript:confirmDeletion(document.rmvAffectation{{$curr_affectation->affectation_id}},{typeName:'l\'affectation',objName:'{{$patient_view|addslashes}}'})">
                <img src="modules/{{$m}}/images/trash.png" alt="trash" title="Supprimer l'affectation" />
              </a>
            {{else}}
            <td class="text">

              <form name="rmvAffectation{{$curr_affectation->affectation_id}}" action="?m={{$m}}" method="post">

              <input type="hidden" name="dosql" value="do_affectation_aed" />
              <input type="hidden" name="del" value="1" />
              <input type="hidden" name="affectation_id" value="{{$curr_affectation->affectation_id}}" />

              </form>
              
              <a style="float: right;" href="javascript:confirmDeletion(document.rmvAffectation{{$curr_affectation->affectation_id}},{typeName:'l\'affectation',objName:'{{$patient_view|addslashes}}'})">
                <img src="modules/{{$m}}/images/trash.png" alt="trash" title="Supprimer l'affectation" />
              </a>
              <em>Entrée</em>:
              {{$curr_affectation->entree|date_format:"%A %d %B %H:%M"}}
              ({{$curr_affectation->_entree_relative}} jours)
            </td>
            <td class="action">

              <form name="entreeAffectation{{$curr_affectation->affectation_id}}" action="?m={{$m}}" method="post">

              <input type="hidden" name="dosql" value="do_affectation_aed" />
              <input type="hidden" name="affectation_id" value="{{$curr_affectation->affectation_id}}" />
              <input type="hidden" name="entree" value="{{$curr_affectation->entree}}" />

              </form>
              
              <a>
                <img id="entreeAffectation{{$curr_affectation->affectation_id}}__trigger_entree" src="modules/{{$m}}/images/planning.png" alt="Planning" title="Modifier la date d'entrée" />
              </a>
            {{/if}}
            </td>
          </tr>
          <tr class="dates">
            {{if $curr_affectation->_ref_next->affectation_id}}
            <td class="text" colspan="2">
              <em>Déplacé</em> (chambre: {{$curr_affectation->_ref_next->_ref_lit->_ref_chambre->nom}}):
              {{$curr_affectation->sortie|date_format:"%A %d %B %H:%M"}}
              ({{$curr_affectation->_sortie_relative}} jours)
            {{else}}
            <td class="text">
              <form name="splitAffectation{{$curr_affectation->affectation_id}}" action="?m={{$m}}" method="post">

              <input type="hidden" name="dosql" value="do_affectation_split" />
              <input type="hidden" name="affectation_id" value="{{$curr_affectation->affectation_id}}" />
              <input type="hidden" name="sejour_id" value="{{$curr_affectation->sejour_id}}" />
              <input type="hidden" name="entree" value="{{$curr_affectation->entree}}" />
              <input type="hidden" name="sortie" value="{{$curr_affectation->sortie}}" />
              <input type="hidden" name="_new_lit_id" value="" />
              <input type="hidden" name="_date_split" value="{{$curr_affectation->sortie}}" />

              </form>
              
              <a style="float: right;">
                <img id="splitAffectation{{$curr_affectation->affectation_id}}__trigger_split" src="modules/{{$m}}/images/move.gif" alt="Move" title="Déplacer un patient" />
              </a>

              <em>Sortie</em>:
              {{$curr_affectation->sortie|date_format:"%A %d %B %H:%M"}}
              ({{$curr_affectation->_sortie_relative}} jours)
            </td>
            <td class="action">

              <form name="sortieAffectation{{$curr_affectation->affectation_id}}" action="?m={{$m}}" method="post">

              <input type="hidden" name="dosql" value="do_affectation_aed" />
              <input type="hidden" name="affectation_id" value="{{$curr_affectation->affectation_id}}" />
              <input type="hidden" name="sortie" value="{{$curr_affectation->sortie}}" />

              </form>
              
              <a>
                <img id="sortieAffectation{{$curr_affectation->affectation_id}}__trigger_sortie" src="modules/{{$m}}/images/planning.png" alt="Planning" title="Modifier la date de sortie" />
              </a>
            {{/if}}
            </td>
          </tr>
          <tr class="dates">
            <td colspan="2"><em>Age</em>: {{$curr_affectation->_ref_sejour->_ref_patient->_age}} ans</td>
          </tr>
          <tr class="dates">
            <td class="text" colspan="2"><em>Dr. {{$curr_affectation->_ref_sejour->_ref_praticien->_view}}</em></td>
          </tr>
          <tr class="dates">
            <td class="text" colspan="2">
              {{foreach from=$curr_affectation->_ref_sejour->_ref_operations item=curr_operation}}
                {{foreach from=$curr_operation->_ext_codes_ccam item=curr_code}}
                <em>{{$curr_code->code}}</em> : {{$curr_code->libelleLong}}<br />
                {{/foreach}}
              {{/foreach}}
            </td>
          </tr>
          <tr class="dates">
            <td class="text" colspan="2">
              <form name="SeptieSejour{{$curr_affectation->_ref_sejour->sejour_id}}" action="?m=dPhospi" method="post">

              <input type="hidden" name="m" value="dPplanningOp" />
              <input type="hidden" name="otherm" value="dPhospi" />
              <input type="hidden" name="dosql" value="do_sejour_aed" />
              <input type="hidden" name="sejour_id" value="{{$curr_affectation->_ref_sejour->sejour_id}}" />
        
              <em>Pathologie</em>:
              {{$curr_affectation->_ref_sejour->pathologie}}
              <input type="radio" name="septique" value="0" {{if $curr_affectation->_ref_sejour->septique == 0}} checked="checked" {{/if}} onclick="this.form.submit()" />
              <label for="septique_0" title="Séjour propre">Propre</label>
              <input type="radio" name="septique" value="1" {{if $curr_affectation->_ref_sejour->septique == 1}} checked="checked" {{/if}} onclick="this.form.submit()" />
              <label for="septique_1" title="Séjour septique">Septique</label>
      
              </form>
                            
            </td>
          </tr>
          {{if $curr_affectation->_ref_sejour->rques != ""}}
          <tr class="dates">
            <td class="text" colspan="2" style="background-color: #ff5">
              <em>Séjour</em>: {{$curr_affectation->_ref_sejour->rques|nl2br}}
            </td>
          </tr>
          {{/if}}
          {{foreach from=$curr_affectation->_ref_sejour->_ref_operations item=curr_operation}}
          {{if $curr_operation->rques != ""}}
          <tr class="dates">
            <td class="text" colspan="2" style="background-color: #ff5">
              <em>Intervention</em>: {{$curr_operation->rques|nl2br}}
            </td>
          </tr>
          {{/if}}
          {{/foreach}}
          {{if $curr_affectation->_ref_sejour->_ref_patient->rques != ""}}
          <tr class="dates">
            <td class="text" colspan="2" style="background-color: #ff5">
              <em>Patient</em>: {{$curr_affectation->_ref_sejour->_ref_patient->rques|nl2br}}
            </td>
          </tr>
          {{/if}}
          <tr class="dates">
            <td class="text" colspan="2">
              <form name="editChFrm{{$curr_affectation->_ref_sejour->sejour_id}}" action="index.php" method="post">
              <input type="hidden" name="m" value="{{$m}}" />
              <input type="hidden" name="dosql" value="do_edit_chambre" />
              <input type="hidden" name="id" value="{{$curr_affectation->_ref_sejour->sejour_id}}" />
              {{if $curr_affectation->_ref_sejour->chambre_seule == 'o'}}
              <input type="hidden" name="value" value="n" />
              <button clas="change" type="submit" style="background-color: #f55;">
                chambre simple
              </button>
              {{else}}
              <input type="hidden" name="value" value="o" />
              <button class="change" type="submit">
                chambre double
              </button>
              {{/if}}
              </form>
            </td>
          </tr>
          {{foreachelse}}
          <tr class="litdispo"><td colspan="2">Lit disponible</td></tr>
          <tr class="litdispo">
            <td class="text" colspan="2">
            depuis:
            {{if $curr_lit->_ref_last_dispo && $curr_lit->_ref_last_dispo->affectation_id}}
            {{$curr_lit->_ref_last_dispo->sortie|date_format:"%A %d %B %H:%M"}} 
            ({{$curr_lit->_ref_last_dispo->_sortie_relative}} jours)
            {{else}}
            Toujours
            {{/if}}
            </td>
          </tr>
          <tr class="litdispo">
            <td class="text" colspan="2">
            jusque: 
            {{if $curr_lit->_ref_next_dispo && $curr_lit->_ref_next_dispo->affectation_id}}
            {{$curr_lit->_ref_next_dispo->entree|date_format:"%A %d %B %H:%M"}}
            ({{$curr_lit->_ref_next_dispo->_entree_relative}} jours)
            {{else}}
            Toujours
            {{/if}}
            </td>
          </tr>
          {{/foreach}}
          {{/foreach}}
        </table>
      {{/foreach}}
      </td>
    {{/foreach}}
    </tr>
    
    </table>
    
  </td>
  {{if $dialog != 1}}
  <td class="pane">
    
    <div id="calendar-container"></div>
  
    {{foreach from=$groupSejourNonAffectes key=group_name item=sejourNonAffectes}}

    <table class="tbl">
      <tr>
        <th class="title">
          Admissions 
          {{if $group_name == "veille" }}de la veille{{/if}}
          {{if $group_name == "matin" }}du matin{{/if}}
          {{if $group_name == "soir" }}du soir{{/if}}
          {{if $group_name == "avant"}}antérieures{{/if}}
        </th>
      </tr>
    </table>

    {{foreach from=$sejourNonAffectes item=curr_sejour}}
    <form name="addAffectation{{$curr_sejour->sejour_id}}" action="?m={{$m}}" method="post">

    <input type="hidden" name="dosql" value="do_affectation_aed" />
    <input type="hidden" name="lit_id" value="" />
    <input type="hidden" name="sejour_id" value="{{$curr_sejour->sejour_id}}" />
    <input type="hidden" name="entree" value="{{$curr_sejour->entree_prevue}}" />
    <input type="hidden" name="sortie" value="{{$curr_sejour->sortie_prevue}}" />

    </form>

    <table class="sejourcollapse" id="sejour{{$curr_sejour->sejour_id}}">
      <tr>
        <td class="selectsejour" style="background:#{{$curr_sejour->_ref_praticien->_ref_function->color}}">
          {{if $curr_sejour->pathologie != ""}}  
          <input type="radio" id="hospitalisation{{$curr_sejour->sejour_id}}" onclick="selectHospitalisation({{$curr_sejour->sejour_id}})" />
          {{/if}}
        </td>
        <td class="patient" onclick="flipSejour({{$curr_sejour->sejour_id}})">
          <strong><a name="sejour{{$curr_sejour->sejour_id}}">{{$curr_sejour->_ref_patient->_view}}</a></strong>
          {{if $curr_sejour->type == "comp"}}
          ({{$curr_sejour->_duree_prevue}}j)
          {{else}}
          ({{$curr_sejour->type|truncate:1:""|capitalize}})
          {{/if}}
        </td>
      </tr>
      <tr>
        <td class="date" colspan="2"><em>Entrée</em> : {{$curr_sejour->entree_prevue|date_format:"%A %d %B %H:%M"}}</td>
      </tr>
      <tr>
        <td class="date" colspan="2"><em>Sortie</em> : {{$curr_sejour->sortie_prevue|date_format:"%A %d %B"}}</td>
      </tr>
      <tr>
        <td class="date" colspan="2"><em>Age</em> : {{$curr_sejour->_ref_patient->_age}} ans
      </tr>
      <tr>
        <td class="date" colspan="2"><em>Dr. {{$curr_sejour->_ref_praticien->_view}}</em></td>
      </tr>
      <tr>
        <td class="date" colspan="2">
          {{foreach from=$curr_sejour->_ref_operations item=curr_operation}}
          {{foreach from=$curr_operation->_ext_codes_ccam item=curr_code}}
          <em>{{$curr_code->code}}</em> : {{$curr_code->libelleLong}}<br />
          {{/foreach}}
          {{/foreach}}
        </td>
      </tr>
      <tr>
        <td class="date" colspan="2">
        
        <form name="EditSejour{{$curr_sejour->sejour_id}}" action="?m=dPhospi" method="post">

        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="otherm" value="dPhospi" />
        <input type="hidden" name="dosql" value="do_sejour_aed" />
        <input type="hidden" name="sejour_id" value="{{$curr_sejour->sejour_id}}" />
        
        <em>Pathologie:</em>
        <select name="pathologie">
          <option value="">&mdash; Choisir &mdash;</option>
          {{foreach from=$pathos->dispo item=curr_patho}}
          <option {{if $curr_patho == $curr_sejour->pathologie}}selected="selected"{{/if}}>
          {{$curr_patho}}
          </option>
          {{/foreach}}
        </select>
        <br />
        <input type="radio" name="septique" value="0" {{if $curr_sejour->septique == 0}} checked="checked" {{/if}} />
        <label for="septique_0" title="Opération propre">Propre</label>
        <input type="radio" name="septique" value="1" {{if $curr_sejour->septique == 1}} checked="checked" {{/if}} />
        <label for="septique_1" title="Séjour septique">Septique</label>

        <input type="submit" value="valider" />
        
        </form>
        
        </td>
      </tr>
      {{if $curr_sejour->rques != ""}}
      <tr>
        <td class="date" colspan="2" style="background-color: #ff5">
          <em>Séjour</em>: {{$curr_sejour->rques|escape:nl2br}}
        </td>
      </tr>
      {{/if}}
      {{foreach from=$curr_sejour->_ref_operations item=curr_operation}}
      {{if $curr_operation->rques != ""}}
      <tr>
        <td class="date" colspan="2" style="background-color: #ff5">
          <em>Intervention</em>: {{$curr_operation->rques|escape:nl2br}}
        </td>
      </tr>
      {{/if}}
      {{/foreach}}
      {{if $curr_sejour->_ref_patient->rques != ""}}
      <tr>
        <td class="date" colspan="2" style="background-color: #ff5">
          <em>Patient</em>: {{$curr_sejour->_ref_patient->rques|escape:nl2br}}
        </td>
      </tr>
      {{/if}}
      {{if $curr_sejour->chambre_seule == "o"}}
      <tr>
        <td class="date" style="background-color: #f55;" colspan="2">
          <strong>Chambre seule</strong>
        </td>
      </tr>
      {{else}}
      <tr>
        <td class="date" colspan="2">
          <strong>Chambre double</strong>
        </td>
      </tr>
      {{/if}}
    </table>
    
    {{/foreach}}
    {{/foreach}}

  </td>
  {{/if}}
</tr>

</table>