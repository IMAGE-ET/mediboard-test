{{$mediboardScriptStorage|smarty:nodefaults}}
<script type="text/javascript" src="modules/dPrepas/javascript/dPrepas.js"></script>

<button type="button" onclick="loadServices();">Recup Services</button>
<button type="button" onclick="recupdata();">Recup data dPrepas</button>
<button class="trash" type="button" onclick="return MbStorage.clear()">Tout Supprimer</button>
<br />
<select id="directory" size="3"></select>
<button class="tick" type="button" onclick="createPlanning()">Go</button>

<div id="divPlanningRepas"></div>
<div id="divRepas" style="display:none">
  <form name="editRepas" action="#" method="post">
  <input type="hidden" name="m" value="dPrepas" />
  <input type="hidden" name="dosql" value="do_repas_aed" />
  <input type="hidden" name="repas_id" value="" />
  <input type="hidden" name="_tmp_repas_id" value="" />
  <input type="hidden" name="affectation_id" value="" />
  <input type="hidden" name="typerepas_id" value="" />
  <input type="hidden" name="date" value="" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="_del" value="0" />
  
  <table class="form">
    <tr>
      <th class="title" colspan="3" id="thRepasTitle"></th>
    </tr>
    <tr>
      <th><strong>Chambre</strong></th>
      <td id="tdRepasChambre"></td>
      <td rowspan="5" class="halfPane" id="listPlat"></td>
    </tr>
    <tr>
      <th><strong>{{tr}}Date{{/tr}}</strong></th>
      <td id="tdRepasDate"></td>
    </tr>
    <tr>
      <th><strong>Type de Repas</strong></th>
      <td id="tdRepasTypeRepas"></td>
    </tr>
    <tr>
      <td colspan="2" class="button">
        <button type="button" class="submit" onclick="vwPlats('');">Ne pas prévoir de repas</button>
      </td>
    </tr>
    <tr>
      <td colspan="2" class="button" id="tdlistMenus">
      </td>
    </tr>
  </table>
  </form>
</div>

<div style="display:none">
  <table id="templateListRepas" class="tbl">
    <tr>
      <th class="category">Menu</th>
      <th class="category">Diabétique</th>
      <th class="category">Sans sel</th>
      <th class="category">Sans résidu</th>
    </tr>
  </table>
  
  <table id="templateListPlats" class="form">
    <tbody>
    <tr>
      <th id="thPlatTitle" class="category" colspan="2"></th>
    </tr>
    {{foreach from=$plats->_enums.type item=curr_typePlat}}
    <tr>
      <th>
        <label for="{{$curr_typePlat}}">{{tr}}CPlat.type.{{$curr_typePlat}}{{/tr}}</label>
      </th>
      <td id="{{$curr_typePlat}}" class="text"></td>
    </tr>
    {{/foreach}}
    </tbody>
  </table>
  
  <table id="templateNoRepas" class="form">
    <tbody>
    <tr>
      <th id="thPlatTitle" class="category" colspan="2">
        Ne pas prévoir de repas
        {{foreach from=$plats->_enums.type item=curr_typePlat}}
        <input type="hidden" name="{{$curr_typePlat}}" value="" />
        {{/foreach}}
      </th>
    </tr>
    </tbody>
  </table>
  
  <button id="templateButtonMod" onclick="saveRepas();" type="button" class="modify">{{tr}}Modify{{/tr}}</button>
  <button id="templateButtonDel" onclick="confirmDeletionOffline(this.form, saveRepas,{typeName:'{{tr escape="javascript"}}CRepas.one{{/tr}}'})" type="button" class="trash">{{tr}}Delete{{/tr}}</button>
  <button id="templateButtonAdd" onclick="saveRepas();" type="button" class="submit">{{tr}}Create{{/tr}}</button>
  <a id="templateHrefBack" class="button" style="float:left;" href="#" onclick="view_planning();" >
    <img align="top" src="images/icons/prev.png" alt="Fichier précédent" />Retour
  </a>
</div>




