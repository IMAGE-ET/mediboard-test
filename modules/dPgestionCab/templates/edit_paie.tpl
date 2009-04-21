<!-- $Id$ -->

<script type="text/javascript">
function printFiche(iFiche_id) {
  var url = new Url();
  url.setModuleAction("dPgestionCab", "print_fiche");
  url.addParam("fiche_paie_id", iFiche_id);
  url.popup(700, 550, "Fiche");
}

function saveFiche() {
  var oForm = document.forms.editFrm;
  oForm.dosql.value = "do_fichePaie_save";
  oForm.submit();
}
</script>

<table class="main">
  <tr>
    <td colspan="2">
      <form name="userSelector" action="?" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <label for="employecab_id" title="Veuillez sélectionner l'employé concerné">Employé Concerné</label>
      <select name="employecab_id" onchange="this.form.submit()">
      {{foreach from=$listEmployes item=curr_emp}}
        <option value="{{$curr_emp->employecab_id}}" {{if $curr_emp->employecab_id == $employe->employecab_id}}selected="selected"{{/if}}>
          {{$curr_emp->_view}}
        </option>
      {{/foreach}}
      </select>
      </form>
      {{if $fichePaie->fiche_paie_id}}
      <br />
      <a class="buttonnew" href="?m={{$m}}&amp;tab=edit_paie&amp;fiche_paie_id=0" title="Créer une nouvelle fiche de paie">
        Créer une nouvelle fiche de paie
      </a>
      {{/if}}
    </td>
  </tr>
  <tr>
    <td class="halfPane">
      <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_fichePaie_aed" />
      <input type="hidden" name="m" value="dPgestionCab" />
      <input type="hidden" name="fiche_paie_id" value="{{$fichePaie->fiche_paie_id}}" />
      <input type="hidden" name="params_paie_id" value="{{$paramsPaie->params_paie_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        {{if $fichePaie->fiche_paie_id}}
        {{if $fichePaie->_locked}}
        <tr>
          <th class="title modify"colspan="2">{{$fichePaie->_view}} Cloturée</th>
        </tr>
        {{else}}
        <tr>
          <th class="title modify" colspan="2">Modifier la {{$fichePaie->_view}}</th>
        </tr>
        {{/if}}
        {{else}}
        <tr>
          <th class="title" colspan="2">Créer une fiche de paie</th>
        </tr>
        {{/if}}
        <tr>
          <th>{{mb_label object=$fichePaie field="debut"}}</th>
          <td class="date">{{mb_field object=$fichePaie field="debut" form="editFrm" register=true}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$fichePaie field="fin"}} </th>
          <td class="date">{{mb_field object=$fichePaie field="fin" form="editFrm" register=true}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$fichePaie field="salaire"}}</th>
          <td>{{mb_field object=$fichePaie field="salaire"}}</td> 
        </tr>
        <tr>
          <th>{{mb_label object=$fichePaie field="heures"}}</th>
          <td>{{mb_field object=$fichePaie field="heures"}}h</td> 
        </tr>
        <tr>
          <th>{{mb_label object=$fichePaie field="heures_comp"}}</th>
          <td>{{mb_field object=$fichePaie field="heures_comp"}}h</td> 
        </tr>
        <tr>
          <th>{{mb_label object=$fichePaie field="heures_sup"}}</th>
          <td>{{mb_field object=$fichePaie field="heures_sup"}}h</td> 
        </tr>
        <tr>
          <th>{{mb_label object=$fichePaie field="precarite"}}</th>
          <td>{{mb_field object=$fichePaie field="precarite"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$fichePaie field="anciennete"}}</th>
          <td>{{mb_field object=$fichePaie field="anciennete"}}</td> 
        </tr>
        <tr>
          <th>{{mb_label object=$fichePaie field="conges_payes"}}</th>
          <td>{{mb_field object=$fichePaie field="conges_payes"}}</td> 
        </tr>
        <tr>
          <th>{{mb_label object=$fichePaie field="prime_speciale"}}</th>
          <td>{{mb_field object=$fichePaie field="prime_speciale"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            {{if !$fichePaie->_locked}}
            <button class="submit" type="submit">Sauver</button>
            {{if $fichePaie->fiche_paie_id}}
            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la ',objName:'{{$fichePaie->_view|smarty:nodefaults|JSAttribute}}'})">
              Supprimer
            </button>
            <button class="print" type="button" onclick="printFiche(this.form.fiche_paie_id.value)">
              Imprimer
            </button>
            <button class="tick" type="button" onclick="saveFiche()">
              Cloturer
            </button>
            {{/if}}
            {{else}}
            <button class="print" type="button" onclick="printFiche(this.form.fiche_paie_id.value)">
              Imprimer
            </button>
            {{/if}}
          </td>
        </tr>
      </table>
      </form>
    </td>
    <td class="halfPane">
      <table class="form">
        <tr>
          <th class="title" colspan="3">Anciennes Fiches de paie</th>
        </tr>
        {{foreach from=$listFiches item=curr_fiche}}
        <tr>
          <td class="text">
            <a href="?m=dPgestionCab&amp;tab=edit_paie&amp;fiche_paie_id={{$curr_fiche->fiche_paie_id}}" title="Editer cette fiche" >
              {{$curr_fiche->_view}}
            </a>
          </td>
          <td>
            <button class="print" type="button" onclick="printFiche({{$curr_fiche->_id}})">
              Imprimer
            </button>
          </td>
          <td>
            {{if $curr_fiche->_locked}}
            CLOTUREE
            {{else}}
            <form name="editFrm{{$curr_fiche->fiche_paie_id}}" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
            <input type="hidden" name="dosql" value="do_fichePaie_aed" />
            <input type="hidden" name="m" value="dPgestionCab" />
            <input type="hidden" name="fiche_paie_id" value="{{$curr_fiche->fiche_paie_id}}" />
            <input type="hidden" name="del" value="0" />
            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la ',objName:'{{$curr_fiche->_view|smarty:nodefaults|JSAttribute}}'})">
              Supprimer
            </button>
            </form>
            {{/if}}
          </td>
        </tr>
        {{foreachelse}}
        <tr>
          <td>
            Liste vide
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
  </tr>
</table>