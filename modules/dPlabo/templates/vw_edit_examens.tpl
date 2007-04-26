{{* $Id$ *}}

<script type="text/javascript">
function pageMain() {
  regFieldCalendar('editExamen', 'deb_application');
  regFieldCalendar('editExamen', 'fin_application');
}
</script>

<table class="main">
  <tr>
    <td class="halfPane">
    
      <!-- Sélection du catalogue -->
      <form name="selectCatalogue" action="index.php" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <label for="catalogue_labo_id" title="Selectionner le catalogue que vous désirez afficher">
        Choisissez un catalogue
      </label>
      <select name="catalogue_labo_id" onchange="this.form.submit()">
        <option value="0">&mdash; aucun</option>
        {{foreach from=$listCatalogues item="curr_catalogue"}}
        <option value="{{$curr_catalogue->_id}}" {{if $curr_catalogue->_id == $catalogue->_id}}selected="selected"{{/if}}>
          {{$curr_catalogue->_view}}
        </option>
        {{foreach from=$curr_catalogue->_ref_catalogues_labo item="curr_sub_catalogue"}}
        <option value="{{$curr_sub_catalogue->_id}}" {{if $curr_sub_catalogue->_id == $catalogue->_id}}selected="selected"{{/if}}>
          &mdash; {{$curr_sub_catalogue->_view}}
        </option>
        {{/foreach}}
        {{/foreach}}
      </select>
      </form>
      
      <!-- Liste des examens pour le catalogue courant -->
      <table class="tbl">
        <tr>
          <th>Identifiant</th>
          <th>Libellé</th>
          <th>Type</th>
          <th>Unité</th>
          <th>Min</th>
          <th>Max</th>
        </tr>
        {{foreach from=$catalogue->_ref_examens_labo item="curr_examen"}}
        <tr {{if $curr_examen->_id == $examen->_id}} class="selected" {{/if}}>
          <td>
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;examen_labo_id={{$curr_examen->_id}}">
              {{$curr_examen->identifiant}}
            </a>
          </td>
          <td>
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;examen_labo_id={{$curr_examen->_id}}">
              {{$curr_examen->libelle}}
            </a>
          </td>
          <td>
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;examen_labo_id={{$curr_examen->_id}}">
              {{$curr_examen->type}}
            </a>
          </td>
          <td>
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;examen_labo_id={{$curr_examen->_id}}">
              {{$curr_examen->unite}}
            </a>
          </td>
          <td>
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;examen_labo_id={{$curr_examen->_id}}">
              {{if $curr_examen->min}}
                {{$curr_examen->min}} {{$curr_examen->unite}}
              {{/if}}
            </a>
          </td>
          <td>
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;examen_labo_id={{$curr_examen->_id}}">
              {{if $curr_examen->max}}
                {{$curr_examen->max}} {{$curr_examen->unite}}
              {{/if}}
            </a>
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    
    <td class="halfPane">
      
      <!-- Edition de l'examen sélectionné -->
      {{if $can->edit}}
      <a class="buttonnew" href="?m={{$m}}&amp;tab={{$tab}}&amp;examen_labo_id=0">
        Ajouter un nouvel examen
      </a>
      <form name="editExamen" action="./index.php?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_examen_aed" />
      <input type="hidden" name="examen_labo_id" value="{{$examen->_id}}" />
      <input type="hidden" name="del" value="0" />
      
      <table class="form">
        <tr>
          {{if $examen->_id}}
          <th class="title modify" colspan="2">Modification de l'examen {{$examen->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Création d'un examen</th>
          {{/if}}
        </tr>
      </table>

      <div class="accordionMain" id="accordionExamen">
      
        <div id="acc_infos">
          <div  class="accordionTabTitleBar" id="IdentiteHeader">
            {{tr}}mod-dPlabo-inc-acc_infos{{/tr}}
          </div>
          <div class="accordionTabContentBox" id="IdentiteContent"  >
          {{include file="inc_examen/acc_infos.tpl"}}
          </div>
        </div>
        
        <div id="acc_realisation">
          <div  class="accordionTabTitleBar" id="IdentiteHeader">
            {{tr}}mod-dPlabo-inc-acc_realisation{{/tr}}
          </div>
          <div class="accordionTabContentBox" id="IdentiteContent"  >
          {{include file="inc_examen/acc_realisation.tpl"}}
          </div>
        </div>
        
        <div id="acc_conservation">
          <div  class="accordionTabTitleBar" id="IdentiteHeader">
            {{tr}}mod-dPlabo-inc-acc_conservation{{/tr}}
          </div>
          <div class="accordionTabContentBox" id="IdentiteContent"  >
          {{include file="inc_examen/acc_conservation.tpl"}}
          </div>
        </div>
        
      </div>

      <script language="Javascript" type="text/javascript">
      var oAccord = new Rico.Accordion($('accordionExamen'), { 
        panelHeight: 300, 
        showDelay: 50, 
        showSteps: 3 
      } );
      </script>
            
      <table class="form">
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $examen->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l \'examen',objName:'{{$examen->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr>
      </table>

      {{/if}}
      
      <!-- Liste des packs associés -->
      {{if $examen->_id}}
      <table class="tbl">
        <tr>
          <th class="title">Packs d'examens associés</th>
        </tr>
        <tr>
          <th>Nom du pack</th>
        </tr>
        {{foreach from=$examen->_ref_packs_labo item=_pack}}
        <tr>
          <td>
            <a href="?m={{$m}}&amp;tab=vw_edit_packs&amp;pack_exmaen_labo_id={{$_pack->_id}}">
              {{$_pack->_view}}
            </a>
          </td>
        </tr>
        {{/foreach}}
      </table>
      {{/if}}

    </td>
  </tr>
</table>