<table class="main">
  <tr>

  	<!-- Praticiens -->
    <td class="halfPane">
      <table class="form">
        <tr>
          <th colspan="2" class="title">
            Praticiens
          </th>
        </tr>
        
        {{foreach from=$praticiens item=curr_prat}}
        {{include file=inc_idsherpa.tpl mbobject=$curr_prat}}
        {{/foreach}}

        <tr>
          <th colspan="2" class="title">
            Personnels
          </th>
        </tr>
        {{foreach from=$persusers item=curr_pers}}
        {{include file=inc_idsherpa.tpl mbobject=$curr_pers}}
        {{/foreach}}

      </table>
    </td>

  	<!-- Salles et Services -->
    <td class="halfPane">
      <table class="form">

      	<!-- Salles -->
        <tr>
          <th colspan="2" class="title">
            Salles d'opérations
          </th>
        </tr>
        {{foreach from=$salles item=_salle}}
        {{include file=inc_idsherpa.tpl mbobject=$_salle}}
        {{/foreach}}

      	<!-- Services -->
        <tr>
          <th colspan="2" class="title">
            Services
          </th>
        </tr>
        {{foreach from=$services item="curr_service"}}
        <tr>
          <th class="category" colspan="2">{{$curr_service->_view}}</th>
        </tr>
        {{foreach from=$curr_service->_ref_chambres item=curr_chambre}}
        {{foreach from=$curr_chambre->_ref_lits item=curr_lit}}
        {{include file=inc_idsherpa.tpl mbobject=$curr_lit}}
        {{/foreach}}
        {{/foreach}}
        {{/foreach}}
      </table>
    </td>

  	<!-- Etablissements externes -->
    <td class="halfPane">
      <table class="form">
        <tr>
          <th colspan="2" class="title">
            Etablissements externes
          </th>
        </tr>
        {{foreach from=$listEtabExternes item=_etab}}
        {{include file=inc_idsherpa.tpl mbobject=$_etab}}
        {{/foreach}}
      </table>
    </td>

  </tr>
</table>