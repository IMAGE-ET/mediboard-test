<!-- $Id: form_print_planning.tpl 23 2006-05-04 15:05:35Z MyttO $ -->

{literal}
<script type="text/javascript">

function printFiche() {
  var iFiche_id = document.forms.editFrm.fiche_paie_id.value;
  var url = new Url();
  url.setModuleAction("dPgestionCab", "print_fiche");
  url.addParam("fiche_paie_id", iFiche_id);
  url.popup(700, 550, "Fiche");
}

function pageMain() {
  regFieldCalendar("editFrm", "debut");
  regFieldCalendar("editFrm", "fin");
}

</script>
{/literal}

<table class="main">
  <tr>
    <td colspan="2">
      <form name="userSelector" action="?m={$m}" method="get">
      <select name="user_id" onchange="this.form.submit()">
      {foreach from=$listUsers item=curr_user}
        <option value="{$curr_user->user_id}" {if $curr_user->user_id == $user->user_id}selected="selected"{/if}>
          {$curr_user->_view}
        </option>
      {/foreach}
      </select>
      </form>
      {if $fichePaie->fiche_paie_id}
      <br />
      <a class="button" href="index.php?m={$m}&amp;tab=edit_paie&amp;fiche_paie_id=0" title="Créer une nouvelle fiche de paie">
        Créer une nouvelle fiche de paie
      </a>
      {/if}
    </td>
  </tr>
  <tr>
    <td class="halfPane">
      <form name="editFrm" action="./index.php?m={$m}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_fichePaie_aed" />
      <input type="hidden" name="m" value="dPgestionCab" />
      <input type="hidden" name="fiche_paie_id" value="{$fichePaie->fiche_paie_id}" />
      <input type="hidden" name="params_paie_id" value="{$paramsPaie->params_paie_id}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        {if $fichePaie->fiche_paie_id}
        <tr>
          <th class="title" colspan="2">Modifier la {$fichePaie->_view}</th>
        </tr>
        {else}
        <tr>
          <th class="title" colspan="2">Créer une fiche de paie</th>
        </tr>
        {/if}
        <tr>
          <th>
            <label for="debut" title="Debut de la période de la fiche de paie">Début de la période :</label>
          </th>
          <td class="date">
            <div id="editFrm_debut_da">{$fichePaie->debut|date_format:"%d/%m/%Y"}</div>
            <input type="hidden" name="debut" value="{$fichePaie->debut}" />
            <img id="editFrm_debut_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de début"/>
          </td>
        </tr>
        <tr>
          <th>
            <label for="fin" title="Fin de la période de la fiche de paie">Fin de la période :</label>
          </th>
          <td class="date">
            <div id="editFrm_fin_da">{$fichePaie->fin|date_format:"%d/%m/%Y"}</div>
            <input type="hidden" name="fin" value="{$fichePaie->fin}" />
            <img id="editFrm_fin_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de début"/>
          </td>
        </tr>
        <tr>
          <th>
            <label for="salaire" title="Salaire horaireen euros">Salaire horaire :</label>
          </th>
          <td>
            <input type="text" name="salaire" size="5" title="{$fichePaie->_props.salaire}" value="{$fichePaie->salaire}" />
            €
          </td>
        </tr>
        <tr>
          <th>
            <label for="heures" title="Nombre d'heures travaillées dans la période">Nombre d'heures travaillées :</label>
          </th>
          <td>
            <input type="text" size="4" name="heures" title="{$fichePaie->_props.heures}" value="{$fichePaie->heures}" />
            h
          </td>
        </tr>
        <tr>
          <th>
            <label for="heures_sup" title="Nombre d'heures suplémentaires travaillées dans la période">Nombre d'heures suplémentaires :</label>
          </th>
          <td>
            <input type="text" size="4" name="heures_sup" title="{$fichePaie->_props.heures_sup}" value="{$fichePaie->heures_sup}" />
            h
          </td>
        </tr>
        <tr>
          <th>
            <label for="mutuelle" title="Valeur de la cotisation pour la mutuelle">Mutuelle :</label>
          </th>
          <td>
            <input type="text" name="mutuelle" size="4" title="{$fichePaie->_props.mutuelle}" value="{$fichePaie->mutuelle}" />
            €
          </td>
        </tr>
        <tr>
          <th>
            <label for="precarite" title="Prime de précarité exprimée en pourcentage">Prime de précarité :</label>
          </th>
          <td>
            <input type="text" size="4" name="precarite" title="{$fichePaie->_props.precarite}" value="{$fichePaie->precarite}" />
            %
          </td>
        </tr>
        <tr>
          <th>
            <label for="anciennete" title="Prime d'ancienneté exprimée en pourcentage">Prime d'ancienneté :</label>
          </th>
          <td>
            <input type="text" size="4" name="anciennete" title="{$fichePaie->_props.anciennete}" value="{$fichePaie->anciennete}" />
            %
          </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button type="submit">Sauver</button>
            {if $fichePaie->fiche_paie_id}
            <button type="button" onclick="confirmDeletion(this.form,{ldelim}typeName:'la ',objName:'{$fichePaie->_view|escape:javascript}'{rdelim})">
              Supprimer
            </button>
            <button type="button" onclick="printFiche()">
              Imprimer
            </button>
            {/if}
          </td>
        </tr>
      </table>
      </form>
    </td>
    <td class="halfPane">
      <table class="form">
        <tr>
          <th class="title">Anciennes Fiches de paie</th>
        </tr>
        {foreach from=$listFiches item=curr_fiche}
        <tr>
          <td>
            <form name="editFrm{$curr_fiche->fiche_paie_id}" action="./index.php?m={$m}" method="post" onsubmit="return checkForm(this)">
            <input type="hidden" name="dosql" value="do_fichePaie_aed" />
            <input type="hidden" name="m" value="dPgestionCab" />
            <input type="hidden" name="fiche_paie_id" value="{$curr_fiche->fiche_paie_id}" />
            <input type="hidden" name="del" value="0" />
            <button type="button" style="float:right;" onclick="confirmDeletion(this.form,{ldelim}typeName:'la ',objName:'{$curr_fiche->_view|escape:javascript}'{rdelim})">
              <img src="modules/dPgestionCab/images/cross.png" alt="supprimer" />
            </button>
            </form>
            <a href="index.php?m=dPgestionCab&amp;tab=edit_paie&amp;fiche_paie_id={$curr_fiche->fiche_paie_id}" title="Editer cette fiche" >
              {$curr_fiche->_view}
            </a>
          </td>
        </tr>
        {foreachelse}
        <tr>
          <td>
            Liste vide
          </td>
        </tr>
        {/foreach}
      </table>
    </td>
  </tr>
</table>