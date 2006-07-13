<!-- $Id$ -->


<script type="text/javascript">
function checkRapport(){
  var form = document.printFrm;
    
  if (form.deb.value > form.fin.value) {
    alert("Date de début superieure à la date de fin");
    return false;
  }

  var url = new Url();
  url.setModuleAction("dPcabinet", form.a.value);
  url.addElement(form.deb);
  url.addElement(form.a);
  url.addElement(form.deb);
  url.addElement(form.fin);
  url.addElement(form.chir);
  url.addElement(form.etat);
  url.addElement(form.type);
  url.addElement(form.aff);
  url.popup(700, 550, "Rapport");
  
  return false;
}

function pageMain() {
  regFieldCalendar("printFrm", "deb");
  regFieldCalendar("printFrm", "fin");
}

</script>


<table class="main">
  <tr>
    <td class="halfPane">
      <form name="printFrm" action="./index.php" method="get" onSubmit="return checkRapport()">
      <input type="hidden" name="a" value="" />
      <input type="hidden" name="dialog" value="1" />
      <table class="form">
        <tr><th class="title" colspan="2">Edition de rapports</th></tr>
        <tr><th class="category" colspan="2">Choix de la periode</th></tr>
        <tr>
          <th><label for="deb" title="Date de début de la recherche">Début</label></th>
          <td class="date" colspan="2">
            <div id="printFrm_deb_da">{{$deb|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="deb" value="{{$deb}}" />
            <img id="printFrm_deb_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de début"/>
          </td>
        </tr>
        <tr>
          <th><label for="fin" title="Date de fin de la recherche">Fin</label></th>
          <td class="date" colspan="2">
            <div id="printFrm_fin_da">{{$fin|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="fin" value="{{$fin}}" />
            <img id="printFrm_fin_trigger" src="./images/calendar.gif" alt="calendar" title="Choisir une date de fin"/>
          </td>
        </tr>
        <tr>
          <th class="category" colspan="2">Critères d'affichage</th>
        </tr>
        <tr>
          <th>Praticien :</th>
          <td>
            <select name="chir">
              <!-- <option value="0">&mdash; Tous &mdash;</option> -->
              {{foreach from=$listPrat item=curr_prat}}
              <option value="{{$curr_prat->user_id}}">{{$curr_prat->_view}}</option>
              {{/foreach}}
            </select>
          </td>
        <tr>
          <th>Etat des paiements :</th>
          <td>
            <select name="etat">
              <option value="-1">&mdash; Tous &mdash;</option>
              <option value="1">Payés</option>
              <option value="0">Impayés</option>
            </select>
          </td>
        </tr>
        <tr>
          <th>Type de paiement :</th>
          <td>
            <select name="type">
              <option value="0">Tout type</option>
              <option value="cheque">Chèques</option>
              <option value="CB">CB</option>
              <option value="especes">Espèces</option>
              <option value="tiers">Tiers-payant</option>
              <option value="autre">Autre</option>
            </select>
          </td>
        </tr>
        <tr>
          <th>Type d'affichage :</th>
          <td>
            <select name="aff">
              <option value="1">Liste complète</option>
              <option value="0">Totaux</option>
            </select>
          </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <input type="submit" value="Validation paiements" onclick="document.printFrm.a.value='print_rapport';" />
            <button class="print" type="submit" onclick="document.printFrm.a.value='print_compta';">Impression compta</button>
          </td>
        </tr>
      </table>
      </form>
    </td>
    <td class="halfPane">
      <table align="center">
      {{if $tarif->tarif_id}}
        <tr>
          <td colspan="3">
            <a class="buttonnew" href="index.php?m={{$m}}&amp;tarif_id=null">Créer un nouveau tarif</a>
          </td>
        </tr>
      {{/if}}
        <tr>
          <td>
            <table class="tbl">
              <tr>
                <th colspan="3">Tarifs du praticien</th>
              </tr>
              <tr>
                <th>Nom</th>
                <th>Secteur 1</th>
                <th>Secteur 2</th>
              </tr>
              {{foreach from=$listeTarifsChir item=curr_tarif}}
              <tr>
                <td>
                  <a href="index.php?m={{$m}}&amp;tarif_id={{$curr_tarif->tarif_id}}">{{$curr_tarif->description}}</a>
                </td>
                <td>
                  <a href="index.php?m={{$m}}&amp;tarif_id={{$curr_tarif->tarif_id}}">{{$curr_tarif->secteur1}} €</a>
                </td>
                <td>
                  <a href="index.php?m={{$m}}&amp;tarif_id={{$curr_tarif->tarif_id}}">{{$curr_tarif->secteur2}} €</a>
                </td>
              </tr>
              {{/foreach}}
            </table>
          </td>
          <td>
            <table class="tbl">
              <tr><th colspan="3">Tarifs du cabinet</th></tr>
              <tr>
                <th>Nom</th>
                <th>Secteur 1</th>
                <th>Secteur 2</th>
              </tr>
              {{foreach from=$listeTarifsSpe item=curr_tarif}}
              <tr>
                <td>
                  <a href="index.php?m={{$m}}&amp;tarif_id={{$curr_tarif->tarif_id}}">{{$curr_tarif->description}}</a>
                </td>
                <td>
                  <a href="index.php?m={{$m}}&amp;tarif_id={{$curr_tarif->tarif_id}}">{{$curr_tarif->secteur1}} €</a>
                </td>
                <td>
                  <a href="index.php?m={{$m}}&amp;tarif_id={{$curr_tarif->tarif_id}}">{{$curr_tarif->secteur2}} €</a>
                </td>
              </tr>
              {{/foreach}}
            </table>
          </td>
          <td>
            <form name="editFrm" action="./index.php?m={{$m}}" method="post">
            <input type="hidden" name="dosql" value="do_tarif_aed" />
            <input type="hidden" name="tarif_id" value="{{$tarif->tarif_id}}" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="chir_id" value="{{$mediuser->user_id}}" />
            <input type="hidden" name="function_id" value="{{$mediuser->function_id}}" />
            <table class="form">
              {{if $tarif->tarif_id}}
              <tr><th class="category" colspan="2">Editer ce tarif</th></tr>
              {{else}}
              <tr><th class="category" colspan="2">Créer un nouveau tarif</th></tr>
              {{/if}}
              <tr>
                <th>Type :</th>
                <td>
                  <select name="_type">
                    <option value="chir" {{if $tarif->chir_id}} selected="selected" {{/if}}>Tarif personnel</option>
                    <option value="function" {{if $tarif->function_id}} selected="selected" {{/if}}>Tarif de cabinet</option>
                  </select>
                </td>
              </tr>
              <tr>
                <th>Nom :</th>
                <td>
                  <input type="text" name="description" value="{{$tarif->description}}" />
                </td>
              </tr>
              <tr>
                <th>Secteur1 :</th>
                <td>
                  <input type="text" name="secteur1" value="{{$tarif->secteur1}}" size="6" /> €
                </td>
              </tr>
              <tr>
                <th>Secteur2 :</th>
                <td>
                  <input type="text" name="secteur2" value="{{$tarif->secteur2}}" size="6" /> €
                </td>
              </tr>
              <tr>
                <td class="button" colspan="2">
                  {{if $tarif->tarif_id}}
                  <button class="modify" type="submit">Modifier</button>
                  <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le tarif',objName:'{{$tarif->description|escape:javascript}}'})">
                    Supprimer
                  </button>
                  {{else}}
                  <button class="submit" type="submit" name="btnFuseAction">Créer</button>
                  {{/if}}
                </td>
              </tr>
            </table>
            </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>