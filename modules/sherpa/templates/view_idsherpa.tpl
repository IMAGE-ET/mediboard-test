<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create('tabs-classes', true);
});
</script>

<ul id="tabs-classes" class="control_tabs">
  <li><a href="#Praticiens">Praticiens</a></li>
  <li><a href="#Personnel">Personnel soignant</a></li>
  <li><a href="#Salles">Salles d'opérations</a></li>
  <li><a href="#Services">Services</a></li>
  <li><a href="#Prestations">Prestations</a></li>
  <li><a href="#Etablissements">Etablissements externes</a></li>
</ul>

<hr class="control_tabs" />

<!-- Praticiens -->
<div id="Praticiens" style="display: none;">
  <table class="form">
    {{assign var="infoPersonnel" value="0"}}
    {{foreach from=$praticiens item=curr_prat}}
    {{include file=inc_idsherpa.tpl mbobject=$curr_prat}}
    {{/foreach}}
  </table>
</div>

<!-- Personnel -->
<div id="Personnel" style="display: none;">
  <table class="form">
    {{foreach from=$persusers item=curr_pers}}  
    {{include file=inc_idsherpa.tpl mbobject=$curr_pers infoPersonnel="1"}}
    {{/foreach}}
  </table>
</div>

<!-- Salles -->
<div id="Salles" style="display: none;">
  <table class="form">
    {{foreach from=$salles item=_salle}}
    {{include file=inc_idsherpa.tpl mbobject=$_salle}}
    {{/foreach}}
  </table>
</div>

<!-- Services -->
<div id="Services" style="display: none;">
  <table class="form">
    <tr>
    {{foreach from=$services item="curr_service"}}
      <th class="category" colspan="2">{{$curr_service->_view}}</th>
    </tr>
    {{foreach from=$curr_service->_ref_chambres item=curr_chambre}}
    {{foreach from=$curr_chambre->_ref_lits item=curr_lit}}
    {{include file=inc_idsherpa.tpl mbobject=$curr_lit}}
    {{/foreach}}
    {{/foreach}}
    {{/foreach}}
  </table>
</div>

<!-- Prestations -->
<div id="Prestations" style="display: none;">
  <table class="form">
    {{foreach from=$prestations item=_prestation}}
    {{include file=inc_idsherpa.tpl mbobject=$_prestation}}
    {{/foreach}}
  </table>
</div>

<!-- Etablissements -->
<div id="Etablissements" style="display: none;">
  <table class="form">
    {{foreach from=$listEtabExternes item=_etab}}
    {{include file=inc_idsherpa.tpl mbobject=$_etab}}
    {{/foreach}}
  </table>
</div>
