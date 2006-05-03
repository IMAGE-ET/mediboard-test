<script language="JavaScript" type="text/javascript">
{literal}
function flipChambre(chambre_id) {
  flipElementClass("chambre" + chambre_id, "chambrecollapse", "chambreexpand", "chambres");
}

function flipOperation(operation_id) {
  flipElementClass("operation" + operation_id, "operationcollapse", "operationexpand");
}

var selected_hospitalisation = null;

function selectHospitalisation(operation_id) {
  var element = document.getElementById("hospitalisation" + selected_hospitalisation);
  if (element) {
    element.checked = false;
  }

  selected_hospitalisation = operation_id;
 
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
            submitAffectationSplit(form)
          }
        }
      }
    );
  }

}

function pageMain() {
  {/literal}
  {if $dialog != 1}
  regRedirectFlatCal("{$date}", "index.php?m={$m}&tab={$tab}&date=");
  {/if}
  {literal}
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

{/literal} 
</script>

<table class="main">

<tr>
  <td>
    <a href="javascript:showLegend()">Légende</a>
  </td>
  <th>
    <a href="javascript:{if $dialog}window.print(){else}popPlanning(){/if}">
      Planning du {$date|date_format:"%A %d %B %Y"} : {$totalLits} place(s) de libre
    </a>
  </th>
  {if $dialog != 1}
  <td>
    <form name="chgMode" action="?m={$m}" method="get">
    <input type="hidden" name="m" value="{$m}" />
    <select name="mode" onchange="submit()">
      <option value="0" {if $mode == 0}selected="selected"{/if}>Vue instantanée</option>
      <option value="1" {if $mode == 1}selected="selected"{/if}>Vue de la journée</option>
    </select>
    </form>
  </td>
  {/if}
</tr>

<tr>
  <td class="greedyPane" colspan="2">

    <table class="tbl">

    <tr>
    {foreach from=$services item=curr_service}
      <th><a href="index.php?m={$m}&amp;tab={$tab}&amp;service_id={$curr_service->service_id}">{$curr_service->nom} / {$curr_service->_nb_lits_dispo} lit(s) dispo</a></td>
    {/foreach}
    </tr>

    <tr>
    {foreach from=$services item=curr_service}
      <td>
      {foreach from=$curr_service->_ref_chambres item=curr_chambre}
        <table class="chambrecollapse" id="chambre{$curr_chambre->chambre_id}">
          <tr>
            <th class="chambre" colspan="2" onclick="javascript:flipChambre({$curr_chambre->chambre_id});
                {foreach from=$curr_chambre->_ref_lits item=curr_lit}
                {foreach from=$curr_lit->_ref_affectations item=curr_aff}
                setupCalendar({$curr_aff->affectation_id});
                {/foreach}
                {/foreach}">
              {if $curr_chambre->_overbooking}
              <img src="modules/{$m}/images/surb.png" alt="warning" title="Over-booking: {$curr_chambre->_overbooking} collisions" />
              {/if}

              {if $curr_chambre->_ecart_age > 15}
              <img src="modules/{$m}/images/age.png" alt="warning" title="Ecart d'âge important: {$curr_chambre->_ecart_age} ans" />
              {/if}

              {if $curr_chambre->_genres_melanges}
              <img src="modules/{$m}/images/sexe.png" alt="warning" title="Sexes opposés" />
              {/if}

              {if $curr_chambre->_chambre_seule}
              <img src="modules/{$m}/images/seul.png" alt="warning" title="Chambre seule obligatoire" />
              {/if}
              
              {if $curr_chambre->_chambre_double}
              <img src="modules/{$m}/images/double.png" alt="warning" title="Chambre double possible" />
              {/if}

              {if $curr_chambre->_conflits_chirurgiens}
              <img src="modules/{$m}/images/prat.png" alt="warning" title="{$curr_chambre->_conflits_chirurgiens} Conflit(s) de praticiens" />
              {/if}

              {if $curr_chambre->_conflits_pathologies}
              <img src="modules/{$m}/images/path.png" alt="warning" title="{$curr_chambre->_conflits_pathologies} Conflit(s) de pathologies" />
              {/if}

              <strong><a name="chambre{$curr_chambre->chambre_id}">{$curr_chambre->nom}</a></strong>
            </th>
          </tr>
          {foreach from=$curr_chambre->_ref_lits item=curr_lit}
          <tr class="lit" >
            <td colspan="1">
              {if $curr_lit->_overbooking}
              <img src="modules/{$m}/images/warning.png" alt="warning" title="Over-booking: {$curr_lit->_overbooking} collisions" />
              {/if}
              {$curr_lit->nom}
            </td>
            <td class="action">
              <input type="radio" id="lit{$curr_lit->lit_id}" onclick="selectLit({$curr_lit->lit_id})" />
            </td>
          </tr>
          {foreach from=$curr_lit->_ref_affectations item=curr_affectation}
          <tr class="patient">
            {if $curr_affectation->confirme}
            <td class="text" style="background-image:url(modules/{$m}/images/ray.gif); background-repeat:repeat;">
            {else}
            <td class="text">
            {/if}
              {if ($curr_affectation->_ref_operation->admis == "n") || ($curr_affectation->_ref_prev->affectation_id && $curr_affectation->_ref_prev->effectue == 0)}
                <font style="color:#a33">
              {else}
                {if $curr_affectation->_ref_operation->septique == 1}
                <font style="color:#3a3">
                {else}
                <font>
                {/if}
              {/if}
              
              {if $curr_affectation->_ref_operation->type_adm == "ambu"}
              <img src="modules/{$m}/images/X.png" alt="X" title="Sortant ce soir" />
              {elseif $curr_affectation->sortie|date_format:"%Y-%m-%d" == $demain}
                {if $curr_affectation->_ref_next->affectation_id}
                <img src="modules/{$m}/images/OC.png" alt="OC" title="Sortant demain" />
                {else}
                <img src="modules/{$m}/images/O.png" alt="O" title="Sortant demain" />
                {/if}
              {elseif $curr_affectation->sortie|date_format:"%Y-%m-%d" == $date}
                {if $curr_affectation->_ref_next->affectation_id}
                <img src="modules/{$m}/images/OoC.png" alt="OoC" title="Sortant aujourd'hui" />
                {else}
                <img src="modules/{$m}/images/Oo.png" alt="Oo" title="Sortant aujourd'hui" />
                {/if}
              {/if}
              {if $curr_affectation->_ref_operation->type_adm == "ambu"}
              <em>{$curr_affectation->_ref_operation->_ref_pat->_view}</em>
              {else}
              <strong>{$curr_affectation->_ref_operation->_ref_pat->_view}</strong>
              {/if}
              {if ($curr_affectation->_ref_operation->admis == "n") || ($curr_affectation->_ref_prev->affectation_id && $curr_affectation->_ref_prev->effectue == 0)}
              {$curr_affectation->entree|date_format:"%d/%m %Hh%M"}
              {/if}
            </font>
            </td>
            <td class="action" style="background:#{$curr_affectation->_ref_operation->_ref_chir->_ref_function->color}">
              {$curr_affectation->_ref_operation->_ref_chir->_shortview}
            </td>
          </tr>
          <tr class="dates">
            {if $curr_affectation->_ref_prev->affectation_id}
            <td class="text">
              <em>Déplacé</em> (chambre: {$curr_affectation->_ref_prev->_ref_lit->_ref_chambre->nom}):
              {$curr_affectation->entree|date_format:"%A %d %B %H:%M"}
              ({$curr_affectation->_entree_relative} jours)
            <td class="action">
              {eval var=$curr_affectation->_ref_operation->_ref_pat->_view assign="pat_view"}

              <form name="rmvAffectation{$curr_affectation->affectation_id}" action="?m={$m}" method="post">

              <input type="hidden" name="dosql" value="do_affectation_aed" />
              <input type="hidden" name="del" value="1" />
              <input type="hidden" name="affectation_id" value="{$curr_affectation->affectation_id}" />

              </form>
              
              <a style="float: right;" href="javascript:confirmDeletion(document.rmvAffectation{$curr_affectation->affectation_id},{ldelim}typeName:'l\'affectation',objName:'{$pat_view|addslashes}'{rdelim})">
                <img src="modules/{$m}/images/trash.png" alt="trash" title="Supprimer l'affectation" />
              </a>
            {else}
            <td class="text">
              {eval var=$curr_affectation->_ref_operation->_ref_pat->_view assign="pat_view"}

              <form name="rmvAffectation{$curr_affectation->affectation_id}" action="?m={$m}" method="post">

              <input type="hidden" name="dosql" value="do_affectation_aed" />
              <input type="hidden" name="del" value="1" />
              <input type="hidden" name="affectation_id" value="{$curr_affectation->affectation_id}" />

              </form>
              
              <a style="float: right;" href="javascript:confirmDeletion(document.rmvAffectation{$curr_affectation->affectation_id},{ldelim}typeName:'l\'affectation',objName:'{$pat_view|addslashes}'{rdelim})">
                <img src="modules/{$m}/images/trash.png" alt="trash" title="Supprimer l'affectation" />
              </a>
              <em>Entrée</em>:
              {$curr_affectation->entree|date_format:"%A %d %B %H:%M"}
              ({$curr_affectation->_entree_relative} jours)
            </td>
            <td class="action">
              {eval var=$curr_affectation->_ref_operation->_ref_pat->_view assign="pat_view"}

              <form name="entreeAffectation{$curr_affectation->affectation_id}" action="?m={$m}" method="post">

              <input type="hidden" name="dosql" value="do_affectation_aed" />
              <input type="hidden" name="affectation_id" value="{$curr_affectation->affectation_id}" />
              <input type="hidden" name="entree" value="{$curr_affectation->entree}" />

              </form>
              
              <a>
                <img id="entreeAffectation{$curr_affectation->affectation_id}__trigger_entree" src="modules/{$m}/images/planning.png" alt="Planning" title="Modifier la date d'entrée" />
              </a>
            {/if}
            </td>
          </tr>
          <tr class="dates">
            {if $curr_affectation->_ref_next->affectation_id}
            <td class="text" colspan="2">
              <em>Déplacé</em> (chambre: {$curr_affectation->_ref_next->_ref_lit->_ref_chambre->nom}):
              {$curr_affectation->sortie|date_format:"%A %d %B %H:%M"}
              ({$curr_affectation->_sortie_relative} jours)
            {else}
            <td class="text">
              <form name="splitAffectation{$curr_affectation->affectation_id}" action="?m={$m}" method="post">

              <input type="hidden" name="dosql" value="do_affectation_split" />
              <input type="hidden" name="affectation_id" value="{$curr_affectation->affectation_id}" />
              <input type="hidden" name="operation_id" value="{$curr_affectation->operation_id}" />
              <input type="hidden" name="entree" value="{$curr_affectation->entree}" />
              <input type="hidden" name="sortie" value="{$curr_affectation->sortie}" />
              <input type="hidden" name="_new_lit_id" value="" />
              <input type="hidden" name="_date_split" value="{$curr_affectation->sortie}" />

              </form>
              
              <a style="float: right;">
                <img id="splitAffectation{$curr_affectation->affectation_id}__trigger_split" src="modules/{$m}/images/move.gif" alt="Move" title="Déplacer un patient" />
              </a>

              <em>Sortie</em>:
              {$curr_affectation->sortie|date_format:"%A %d %B %H:%M"}
              ({$curr_affectation->_sortie_relative} jours)
            </td>
            <td class="action">
              {eval var=$curr_affectation->_ref_operation->_ref_pat->_view assign="pat_view"}

              <form name="sortieAffectation{$curr_affectation->affectation_id}" action="?m={$m}" method="post">

              <input type="hidden" name="dosql" value="do_affectation_aed" />
              <input type="hidden" name="affectation_id" value="{$curr_affectation->affectation_id}" />
              <input type="hidden" name="sortie" value="{$curr_affectation->sortie}" />

              </form>
              
              <a>
                <img id="sortieAffectation{$curr_affectation->affectation_id}__trigger_sortie" src="modules/{$m}/images/planning.png" alt="Planning" title="Modifier la date de sortie" />
              </a>
            {/if}
            </td>
          </tr>
          <tr class="dates">
            <td colspan="2"><em>Age</em>: {$curr_affectation->_ref_operation->_ref_pat->_age} ans</td>
          </tr>
          <tr class="dates">
            <td class="text" colspan="2"><em>Dr. {$curr_affectation->_ref_operation->_ref_chir->_view}</em></td>
          </tr>
          <tr class="dates">
            <td class="text" colspan="2">
              {foreach from=$curr_affectation->_ref_operation->_ext_codes_ccam item=curr_code}
              <em>{$curr_code->code}</em> : {$curr_code->libelleLong}<br />
              {/foreach}
            </td>
          </tr>
          <tr class="dates">
            <td class="text" colspan="2">
              <form name="SeptieOperation{$curr_affectation->_ref_operation->operation_id}" action="?m=dPplanningOp" method="post">

              <input type="hidden" name="m" value="dPplanningOp" />
              <input type="hidden" name="otherm" value="dPhospi" />
              <input type="hidden" name="dosql" value="do_planning_aed" />
              <input type="hidden" name="operation_id" value="{$curr_affectation->_ref_operation->operation_id}" />
        
              <em>Pathologie</em>:
              {$curr_affectation->_ref_operation->pathologie}
              <input type="radio" name="septique" value="0" {if $curr_affectation->_ref_operation->septique == 0} checked="checked" {/if} onclick="this.form.submit()" />
              <label for="septique_0" title="Opération propre">Propre</label>
              <input type="radio" name="septique" value="1" {if $curr_affectation->_ref_operation->septique == 1} checked="checked" {/if} onclick="this.form.submit()" />
              <label for="septique_1" title="Opération septique">Septique</label>
      
              </form>
                            
            </td>
          </tr>
          {if $curr_affectation->_ref_operation->rques != ""}
          <tr class="dates">
            <td class="text" colspan="2" style="background-color: #ff5">
              <em>Intervention</em>: {$curr_affectation->_ref_operation->rques|nl2br}
            </td>
          </tr>
          {/if}
          {if $curr_affectation->_ref_operation->_ref_pat->rques != ""}
          <tr class="dates">
            <td class="text" colspan="2" style="background-color: #ff5">
              <em>Patient</em>: {$curr_affectation->_ref_operation->_ref_pat->rques|nl2br}
            </td>
          </tr>
          {/if}
          <tr class="dates">
            <td class="text" colspan="2">
              <form name="editChFrm{$curr_affectation->_ref_operation->operation_id}" action="index.php" method="post">
              <input type="hidden" name="m" value="{$m}" />
              <input type="hidden" name="dosql" value="do_edit_chambre" />
              <input type="hidden" name="id" value="{$curr_affectation->_ref_operation->operation_id}" />
              {if $curr_affectation->_ref_operation->chambre == 'o'}
              <input type="hidden" name="value" value="n" />
              <button type="submit" style="background-color: #f55;">
                <img src="modules/{$m}/images/refresh.png" alt="changer" /> chambre simple
              </button>
              {else}
              <input type="hidden" name="value" value="o" />
              <button type="submit">
                <img src="modules/{$m}/images/refresh.png" alt="changer" /> chambre double
              </button>
              {/if}
              </form>
            </td>
          </tr>
          {foreachelse}
          <tr class="litdispo"><td colspan="2">Lit disponible</td></tr>
          <tr class="litdispo">
            <td class="text" colspan="2">
            depuis:
            {if $curr_lit->_ref_last_dispo && $curr_lit->_ref_last_dispo->affectation_id}
            {$curr_lit->_ref_last_dispo->sortie|date_format:"%A %d %B %H:%M"} 
            ({$curr_lit->_ref_last_dispo->_sortie_relative} jours)
            {else}
            Toujours
            {/if}
            </td>
          </tr>
          <tr class="litdispo">
            <td class="text" colspan="2">
            jusque: 
            {if $curr_lit->_ref_next_dispo && $curr_lit->_ref_next_dispo->affectation_id}
            {$curr_lit->_ref_next_dispo->entree|date_format:"%A %d %B %H:%M"}
            ({$curr_lit->_ref_next_dispo->_entree_relative} jours)
            {else}
            Toujours
            {/if}
            </td>
          </tr>
          {/foreach}
          {/foreach}
        </table>
      {/foreach}
      </td>
    {/foreach}
    </tr>
    
    </table>
    
  </td>
  {if $dialog != 1}
  <td class="pane">
    
    <div id="calendar-container"></div>
  
    {foreach from=$groupOpNonAffectees key=group_name item=opNonAffectees}

    <table class="tbl">
      <tr>
        <th class="title">
          Admissions 
          {if $group_name == "veille" }de la veille{/if}
          {if $group_name == "matin" }du matin{/if}
          {if $group_name == "soir" }du soir{/if}
          {if $group_name == "avant"}antérieures{/if}
        </th>
      </tr>
    </table>

    {foreach from=$opNonAffectees item=curr_operation}
    <form name="addAffectation{$curr_operation->operation_id}" action="?m={$m}" method="post">

    <input type="hidden" name="dosql" value="do_affectation_aed" />
    <input type="hidden" name="lit_id" value="" />
    <input type="hidden" name="operation_id" value="{$curr_operation->operation_id}" />
    <input type="hidden" name="entree" value="{$curr_operation->_entree_adm}" />
    <input type="hidden" name="sortie" value="{$curr_operation->_sortie_adm}" />

    </form>

    <table class="operationcollapse" id="operation{$curr_operation->operation_id}">
      <tr>
        <td class="selectoperation" style="background:#{$curr_operation->_ref_chir->_ref_function->color}">
          {if $curr_operation->pathologie != ""}  
          <input type="radio" id="hospitalisation{$curr_operation->operation_id}" onclick="selectHospitalisation({$curr_operation->operation_id})" />
          {/if}
        </td>
        <td class="patient" onclick="flipOperation({$curr_operation->operation_id})">
          <strong><a name="operation{$curr_operation->operation_id}">{$curr_operation->_ref_pat->_view}</a></strong>
          {if $curr_operation->type_adm == "comp"}
          ({$curr_operation->duree_hospi}j)
          {else}
          ({$curr_operation->type_adm|truncate:1:""|capitalize})
          {/if}
        </td>
      </tr>
      <tr>
        <td class="date" colspan="2"><em>Entrée</em>: {$curr_operation->_entree_adm|date_format:"%A %d %B %H:%M"}</td>
      </tr>
      <tr>
        <td class="date" colspan="2"><em>Sortie</em>: {$curr_operation->_sortie_adm|date_format:"%A %d %B"}</td>
      </tr>
      <tr>
        <td class="date" colspan="2"><em>Age:</em>: {$curr_operation->_ref_pat->_age} ans
      </tr>
      <tr>
        <td class="date" colspan="2"><em>Dr. {$curr_operation->_ref_chir->_view}</em></td>
      </tr>
      <tr>
        <td class="date" colspan="2">
          {foreach from=$curr_operation->_ext_codes_ccam item=curr_code}
          <em>{$curr_code->code}</em> : {$curr_code->libelleLong}<br />
          {/foreach}
        </td>
      </tr>
      <tr>
        <td class="date" colspan="2">
        
        <form name="EditOperation{$curr_operation->operation_id}" action="?m=dPplanningOp" method="post">

        <input type="hidden" name="m" value="dPplanningOp" />
        <input type="hidden" name="otherm" value="dPhospi" />
        <input type="hidden" name="dosql" value="do_planning_aed" />
        <input type="hidden" name="operation_id" value="{$curr_operation->operation_id}" />
        
        <em>Pathologie:</em>
        <select name="pathologie">
          <option value="">&mdash; Choisir &mdash;</option>
          {foreach from=$pathos->dispo item=curr_patho}
          <option {if $curr_patho == $curr_operation->pathologie}selected="selected"{/if}>
          {$curr_patho}
          </option>
          {/foreach}
        </select>
        <br />
        <input type="radio" name="septique" value="0" {if $curr_operation->septique == 0} checked="checked" {/if} />
        <label for="septique_0" title="Opération propre">Propre</label>
        <input type="radio" name="septique" value="1" {if $curr_operation->septique == 1} checked="checked" {/if} />
        <label for="septique_1" title="Opération septique">Septique</label>

        <input type="submit" value="valider" />
        
        </form>
        
        </td>
      </tr>
      {if $curr_operation->rques != ""}
      <tr>
        <td class="date" colspan="2" style="background-color: #ff5">
          <em>Intervention</em>: {$curr_operation->rques|escape:nl2br}
        </td>
      </tr>
      {/if}
      {if $curr_operation->_ref_pat->rques != ""}
      <tr>
        <td class="date" colspan="2" style="background-color: #ff5">
          <em>Patient</em>: {$curr_operation->_ref_pat->rques|escape:nl2br}
        </td>
      </tr>
      {/if}
      {if $curr_operation->chambre == "o"}
      <tr>
        <td class="date" style="background-color: #f55;" colspan="2">
          <strong>Chambre seule</strong>
        </td>
      </tr>
      {else}
      <tr>
        <td class="date" colspan="2">
          <strong>Chambre double</strong>
        </td>
      </tr>
      {/if}
    </table>
    
    {/foreach}
    {/foreach}

  </td>
  {/if}
</tr>

</table>