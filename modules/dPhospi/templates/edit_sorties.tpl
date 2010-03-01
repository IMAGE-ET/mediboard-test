<script type="text/javascript">

Main.add(function () {
  Calendar.regField(getForm("typeVue").date, null, {noView: true});
});

function saveSortie(oFormSortie, oFormAffectation){
  if(oFormSortie){
    oFormAffectation.sortie.value = oFormSortie.sortie.value;
  }
}

</script>

<table class="main">
  <tr>
    <th>
      <form name="typeVue" action="?m={{$m}}" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <label for="vue" title="Choisir un type de vue">Type de vue</label>
        <select name="vue" onchange="this.form.submit()">
          <option value="0" {{if $vue == 0}} selected="selected"{{/if}}>Tout afficher</option>
          <option value="1" {{if $vue == 1}} selected="selected"{{/if}}>Ne pas afficher les validés</option>
        </select>
        {{$date|date_format:$dPconfig.longdate}}
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </form>
    </th>
  <tr>
    <td colspan="3">
      <table width="100%">
        <tr>
          <td class="halfPane">
            <table class="tbl">
              <tr>
                <th class="title" colspan="6">
                  Effectuer des déplacements ({{$deplacements|@count}})
                </th>
              </tr>
              <tr>
                <th>Effectuer</th>
                <th>{{mb_colonne class="CAffectation" field="_patient" order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=$tab"}}</th>
                <th>{{mb_colonne class="CAffectation" field="_praticien" order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=$tab"}}</th>
                <th>{{mb_colonne class="CAffectation" field="_chambre" order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=$tab"}}</th>
                <th>Destination</th>
                <th>{{mb_colonne class="CAffectation" field="sortie" order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=$tab"}}</th>
                </tr>
              {{foreach from=$deplacements item=curr_sortie}}
              <tr>
                <td>
                <form name="editFrm{{$curr_sortie->affectation_id}}" action="?m={{$m}}" method="post" onsubmit="saveSortie(document.editSortie{{$curr_sortie->affectation_id}}, this);">
                <input type="hidden" name="m" value="{{$m}}" />
                <input type="hidden" name="del" value="0" />
                <input type="hidden" name="dosql" value="do_affectation_aed" />
                <input type="hidden" name="affectation_id" value="{{$curr_sortie->affectation_id}}" />
                <input type="hidden" name="sortie" value="{{$curr_sortie->sortie}}" />
                {{if $curr_sortie->effectue}}
               
                <input type="hidden" name="effectue" value="0" />
                <button type="submit" class="cancel">
                Annuler le déplacement
                </button>
                {{else}}
                <input type="hidden" name="effectue" value="1" />
                <button type="submit" class="tick">
                Effectuer le déplacement
                </button>
                {{/if}}
                </form>
                </td>
                {{if $curr_sortie->effectue}}
                <td class="text" style="background-image:url(images/icons/ray.gif); background-repeat:repeat;">
                {{else}}
                <td class="text">
                {{/if}}
                  <b>{{$curr_sortie->_ref_sejour->_ref_patient->_view}}</b>
                </td>
                <td class="text" style="background:#{{$curr_sortie->_ref_sejour->_ref_praticien->_ref_function->color}}">
                  {{$curr_sortie->_ref_sejour->_ref_praticien->_view}}
                </td>
                <td class="text">
                  {{$curr_sortie->_ref_lit->_view}}
                </td>
                <td class="text">
                  {{$curr_sortie->_ref_next->_ref_lit->_view}}
                </td>
                <td>
                {{if $curr_sortie->effectue}}
                  {{$curr_sortie->sortie|date_format:$dPconfig.time}}
                {{else}}
                <form name="editSortie{{$curr_sortie->affectation_id}}" action="">
                  <select name="sortie">
                  {{assign var="curr_id" value=$curr_sortie->_id}}
                  {{foreach from=$timing.$curr_id|smarty:nodefaults item="time"}}
                  <option value="{{$time}}" {{if $time==$curr_sortie->sortie}}selected = "selected"{{/if}}>
                   {{$time|date_format:$dPconfig.time}}
                  </option>
                  {{/foreach}}
                  </select>
                </form>
                {{/if}}                  
                </td>
              </tr>
              {{/foreach}}
            </table>
          </td>
          <td class="halfPane">
            <table class="tbl">
              <tr>
                <th class="title" colspan="5">
                  Autoriser des sorties ({{$sortiesComp|@count}} hospis - {{$sortiesAmbu|@count}} ambus)
                </th>
              </tr>
              <tr>
                <th>Autoriser</th>
                <th>{{mb_colonne class="CAffectation" field="_patient" order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=$tab"}}</th>
                <th>{{mb_colonne class="CAffectation" field="_praticien" order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=$tab"}}</th>
                <th>{{mb_colonne class="CAffectation" field="_chambre" order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=$tab"}}</th>
                <th>{{mb_colonne class="CAffectation" field="sortie" order_col=$order_col order_way=$order_way url="?m=$m&amp;tab=$tab"}}</th>
              </tr>
              <tr><th colspan="5">Hospitalisations complètes</th></tr>
              {{foreach from=$sortiesComp item=curr_sortie}}
              <tr>
                <td>
                <form name="editFrm{{$curr_sortie->affectation_id}}" action="?m={{$m}}" method="post">
                <input type="hidden" name="m" value="{{$m}}" />
                <input type="hidden" name="del" value="0" />
                <input type="hidden" name="dosql" value="do_affectation_aed" />
                <input type="hidden" name="affectation_id" value="{{$curr_sortie->affectation_id}}" />
                {{if $curr_sortie->confirme}}
                <input type="hidden" name="confirme" value="0" />
                <button type="submit" class="cancel">
                Annuler la sortie
                </button>
                {{else}}
                <input type="hidden" name="confirme" value="1" />
                <button type="submit" class="tick">
                Autoriser la sortie
                </button>
                {{/if}}
                </form>
                </td>
                {{if $curr_sortie->confirme}}
                <td class="text" style="background-image:url(images/icons/ray.gif); background-repeat:repeat;">
                {{else}}
                <td class="text">
                {{/if}}
                 {{if $canPlanningOp->read}}
                 <a class="action" style="float: right"  title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$curr_sortie->_ref_sejour->patient_id}}">
                     <img src="images/icons/edit.png" alt="modifier" />
                 </a>
                 <a class="action" style="float: right"  title="Modifier le séjour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$curr_sortie->_ref_sejour->_id}}">
                   <img src="images/icons/planning.png" alt="modifier" />
                 </a>
                 {{/if}}
                  <b>{{$curr_sortie->_ref_sejour->_ref_patient->_view}}</b>
                </td>
                <td class="text" style="background:#{{$curr_sortie->_ref_sejour->_ref_praticien->_ref_function->color}}">
                  {{$curr_sortie->_ref_sejour->_ref_praticien->_view}}
                </td>
                <td class="text">
                  {{$curr_sortie->_ref_lit->_view}}
                </td>
                <td>{{$curr_sortie->sortie|date_format:$dPconfig.time}}</td>
              </tr>
              {{/foreach}}
              <tr><th colspan="5">Ambulatoires</th></tr>
              {{foreach from=$sortiesAmbu item=curr_sortie}}
              <tr>
                <td>
                <form name="editFrm{{$curr_sortie->affectation_id}}" action="?m={{$m}}" method="post">
                <input type="hidden" name="m" value="{{$m}}" />
                <input type="hidden" name="del" value="0" />
                <input type="hidden" name="dosql" value="do_affectation_aed" />
                <input type="hidden" name="affectation_id" value="{{$curr_sortie->affectation_id}}" />
                {{if $curr_sortie->confirme}}
                <input type="hidden" name="confirme" value="0" />
                <button type="submit" class="cancel">
                Annuler la sortie
                </button>
                {{else}}
                <input type="hidden" name="confirme" value="1" />
                <button type="submit" class="tick">
                Autoriser la sortie
                </button>
                {{/if}}
                </form>
                </td>
                {{if $curr_sortie->confirme}}
                <td class="text" style="background-image:url(images/icons/ray.gif); background-repeat:repeat;">
                {{else}}
                <td class="text">
                {{/if}}
                 {{if $canPlanningOp->read}}
                 <a class="action" style="float: right"  title="Modifier le dossier administratif" href="?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id={{$curr_sortie->_ref_sejour->patient_id}}">
                     <img src="images/icons/edit.png" alt="modifier" />
                 </a>
                 <a class="action" style="float: right"  title="Modifier le séjour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$curr_sortie->_ref_sejour->_id}}">
                   <img src="images/icons/planning.png" alt="modifier" />
                 </a>
                 {{/if}}
                  <b>{{$curr_sortie->_ref_sejour->_ref_patient->_view}}</b>
                </td>
                <td class="text" style="background:#{{$curr_sortie->_ref_sejour->_ref_praticien->_ref_function->color}}">
                  {{$curr_sortie->_ref_sejour->_ref_praticien->_view}}
                </td>
                <td class="text">
                  {{$curr_sortie->_ref_lit->_view}}
                </td>
                <td>{{$curr_sortie->sortie|date_format:$dPconfig.time}}</td>
              </tr>
              {{/foreach}}
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>