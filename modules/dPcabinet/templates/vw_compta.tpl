<!-- $Id$ -->


<script type="text/javascript">
function checkRapport(){
  var form = document.printFrm;
  
  if(!(checkForm(form))){
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
          <th><label for="deb" title="Date de d�but de la recherche">D�but</label></th>
          <td class="date" colspan="2">
            <div id="printFrm_deb_da">{{$deb|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="deb" title="notNull date" value="{{$deb}}" />
            <img id="printFrm_deb_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de d�but"/>
          </td>
        </tr>
        <tr>
          <th><label for="fin" title="Date de fin de la recherche">Fin</label></th>
          <td class="date" colspan="2">
            <div id="printFrm_fin_da">{{$fin|date_format:"%d/%m/%Y"}}</div>
            <input type="hidden" name="fin" title="notNull date moreEquals|deb" value="{{$fin}}" />
            <img id="printFrm_fin_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de fin"/>
          </td>
        </tr>
        <tr>
          <th class="category" colspan="2">Crit�res d'affichage</th>
        </tr>
        <tr>
          <th><label for="chir" title="Praticien">Praticien</label></th>
          <td>
            <select name="chir">
              <!-- <option value="">&mdash; Tous &mdash;</option> -->
              {{foreach from=$listPrat item=curr_prat}}
              <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};" value="{{$curr_prat->user_id}}">{{$curr_prat->_view}}</option>
              {{/foreach}}
            </select>
          </td>
        <tr>
          <th><label for="etat" title="Etat des paiements">Etat des paiements</label></th>
          <td>
            <select name="etat">
              <option value="-1">&mdash; Tous &mdash;</option>
              <option value="1">Pay�s</option>
              <option value="0">Impay�s</option>
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="type" title="Type de paiement">Type de paiement</label></th>
          <td>
            <select name="type">
              <option value="0">Tout type</option>
              <option value="cheque">Ch�ques</option>
              <option value="CB">CB</option>
              <option value="especes">Esp�ces</option>
              <option value="tiers">Tiers-payant</option>
              <option value="autre">Autre</option>
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="aff" title="Type d'affichage">Type d'affichage</label></th>
          <td>
            <select name="aff">
              <option value="1">Liste compl�te</option>
              <option value="0">Totaux</option>
            </select>
          </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="search" type="submit" onclick="document.printFrm.a.value='print_rapport';">Validation paiements</button>
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
            <a class="buttonnew" href="index.php?m={{$m}}&amp;tarif_id=null">Cr�er un nouveau tarif</a>
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
                  <a href="index.php?m={{$m}}&amp;tarif_id={{$curr_tarif->tarif_id}}">{{$curr_tarif->secteur1}} �</a>
                </td>
                <td>
                  <a href="index.php?m={{$m}}&amp;tarif_id={{$curr_tarif->tarif_id}}">{{$curr_tarif->secteur2}} �</a>
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
                  <a href="index.php?m={{$m}}&amp;tarif_id={{$curr_tarif->tarif_id}}">{{$curr_tarif->secteur1}} �</a>
                </td>
                <td>
                  <a href="index.php?m={{$m}}&amp;tarif_id={{$curr_tarif->tarif_id}}">{{$curr_tarif->secteur2}} �</a>
                </td>
              </tr>
              {{/foreach}}
            </table>
          </td>
          <td>
            <form name="editFrm" action="./index.php?m={{$m}}" method="post" onsubmit="return checkForm(this)">
            <input type="hidden" name="dosql" value="do_tarif_aed" />
            {{mb_field object=$tarif field="tarif_id" hidden=1 spec=""}}
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="chir_id" value="{{$mediuser->user_id}}" />
            {{mb_field object=$mediuser field="function_id" hidden=1 spec=""}}
            <table class="form">
              {{if $tarif->tarif_id}}
              <tr><th class="category" colspan="2">Editer ce tarif</th></tr>
              {{else}}
              <tr><th class="category" colspan="2">Cr�er un nouveau tarif</th></tr>
              {{/if}}
              <tr>
                <th>{{mb_label object=$tarif field="_type"}}</th>
                <td>
                  <select name="_type">
                    <option value="chir" {{if $tarif->chir_id}} selected="selected" {{/if}}>Tarif personnel</option>
                    <option value="function" {{if $tarif->function_id}} selected="selected" {{/if}}>Tarif de cabinet</option>
                  </select>
                </td>
              </tr>
              <tr>
                <th>{{mb_label object=$tarif field="description"}}</th>
                <td>
                  {{mb_field object=$tarif field="description"}}
                </td>
              </tr>
              <tr>
                <th>{{mb_label object=$tarif field="secteur1"}}</th>
                <td>
                  {{mb_field object=$tarif field="secteur1" size="6"}} �
                </td>
              </tr>
              <tr>
                <th>{{mb_label object=$tarif field="secteur2"}}</th>
                <td>
                  {{mb_field object=$tarif field="secteur2" size="6"}} �
                </td>
              </tr>
              <tr>
                <td class="button" colspan="2">
                  {{if $tarif->tarif_id}}
                  <button class="modify" type="submit">Modifier</button>
                  <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le tarif',objName:'{{$tarif->description|smarty:nodefaults|JSAttribute}}'})">
                    Supprimer
                  </button>
                  {{else}}
                  <button class="submit" type="submit" name="btnFuseAction">Cr�er</button>
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