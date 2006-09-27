<script type="text/javascript">
function printIncident(ficheId){
  var url = new Url;
  url.setModuleAction("dPqualite", "print_fiche"); 
  url.addParam("fiche_ei_id", ficheId);
  url.popup(700, 500, url, "printFicheEi");
  return;
}

function refuseFiche(){

}
</script>
<table class="main">
  <tr>
    <td class="halfPane">
      {{if $listFichesEnCours|@count}}
      <table class="form">
        <tr>
          <th class="category" colspan="4">
            Fiche d'EI en Attente
          </th>
        </tr>
        <tr>
          <th class="category">Date de l'événement</th>
          <th class="category">Auteur de la fiche</th>
        </tr>
        {{foreach from=$listFichesEnCours item=currFiche}}
        <tr>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
              {{$currFiche->date_incident|date_format:"%A %d %B %Y à %Hh%M"}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
              {{$currFiche->_ref_user->_view}}
            </a>
          </td>
        </tr>
        {{/foreach}}
      </table><br /><br />
      {{/if}}
        
      <table class="form">
        <tr>
          <th class="category" colspan="4">Fiches d'EI en cours de traitement</th>
        </tr>
        <tr>
          <th class="category">Date de l'événement</th>
          <th class="category">Auteur</th>
          <th class="category">Urgence</th>
          <th class="category">Etat</th>
        </tr>        
        {{foreach from=$listFichesTermine item=currFiche}}
        <tr>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
              {{$currFiche->date_incident|date_format:"%d %b %Y à %Hh%M"}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
              {{$currFiche->_ref_user->_view}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
              {{$currFiche->degre_urgence}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_incidentvalid&amp;fiche_ei_id={{$currFiche->fiche_ei_id}}">
              {{$currFiche->_etat_actuel}}
            </a>
          </td>
        </tr>
        {{foreachelse}}
        <tr>
          <td colspan="4">
            Aucune Fiche traitée actuellement
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    <td class="halfPane">
      {{if $fiche->fiche_ei_id}}
      
      <form name="ProcEditFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_ficheEi_aed" />
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="fiche_ei_id" value="{{$fiche->fiche_ei_id}}" />
      <input type="hidden" name="valid_user_id" value="{{$user_id}}" />

      <table class="form">
        {{include file="inc_incident_infos.tpl"}}
        
      {{if $canAdmin && !$fiche->valid_user_id}}
        <tr>
          <th><label for="degre_urgence" title="Veuillez sélectionner le degré d'urgence">Degré d'Urgence</label></th>
          <td>
            <select name="degre_urgence" title="{{$fiche->_props.degre_urgence}}|notNull">
            <option value="">&mdash; Veuillez Choisir</option>
            {{html_options options=$fiche->_enumsTrans.degre_urgence}}
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="service_valid_user_id" title="Veuillez sélectionner le chef de service à qui transmettre la fiche">Chef de Service à qui transmettre la fiche</label></th>
          <td>
            <select name="service_valid_user_id" title="{{$fiche->_props.service_valid_user_id}}|notNull">
            <option value="">&mdash; Veuillez Choisir &mdash;</option>
            {{foreach from=$listUsersEdit item=currUser}}
            <option value="{{$currUser->user_id}}">{{$currUser->_view}}</option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <button class="edit" type="button" onclick="window.location.href='index.php?m={{$m}}&amp;tab=vw_incident&amp;fiche_ei_id={{$fiche->fiche_ei_id}}';">
              Editer la Fiche
            </button>
            <button class="modify" type="submit">
              Transmettre
            </button>
            <button class="trash" type="button" onclick="confirmDeletion(this.form, {typeName:'cette fiche d\'EI '})" title="Supprimer la Fiche d'EI">
              Supprimer
            </button>
          </td>
        </tr>
      {{/if}}
      
      {{if $fiche->service_valid_user_id && $fiche->service_valid_user_id==$user && !$fiche->service_date_validation}}
        <tr>
          <th colspan="2" class="category">
            Validation du Chef de Service
          </th>
        </tr>
        <tr>
          <th>
            <label for="service_actions" title="Veuillez décrire les actions mises en place">Actions mises en Place</label>
          </th>
          <td>
            <textarea name="service_actions" title="{{$fiche->_props.service_actions}}|notNull"></textarea>
          </td>
        </tr>
        <tr>
          <th>
            <label for="service_descr_consequences" title="Veuillez décrire les conséquences">Description des conséquences</label>
          </th>
          <td>
            <textarea name="service_descr_consequences" title="{{$fiche->_props.service_descr_consequences}}|notNull"></textarea>
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button">
            <button class="modify" type="submit">
              Transmettre
            </button>
          </td>
        </tr>
      {{/if}}
      
      {{if $canAdmin && $fiche->service_date_validation}}
        {{if !$fiche->qualite_date_validation}}
        <tr>
          <td colspan="2" class="button">
            <button class="modify" type="submit">
              Valider ces mesures
            </button>
          </td>
        </tr>
        {{else}}
      
        {{/if}}
      {{/if}}
      
      {{if $canAdmin && $fiche->valid_user_id}}
        <tr>
          <td colspan="2" class="button">
            <button class="print" type="button" onclick="printIncident({{$fiche->fiche_ei_id}});">
              Imprimer la fiche
            </button>
          </td>
        </tr>
      {{/if}}
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
            
      </table>
      </form>
      {{/if}}
    </td>
  </tr>
</table>