<script type="text/javascript">

function pageMain() {
  PairEffect.initGroup("serviceEffect");
}

</script>
<table class="main">
<tr>
  <td class="halfPane">
    <a class="buttonnew" href="?m={{$m}}&amp;tab={{$tab}}&amp;chambre_id=0">
      Créer un chambre
    </a>
    <table class="tbl">   
    <tr>
      <th colspan="4">Liste des chambres</th>
    </tr>  
    <tr>
      <th>Intitulé</th>
      <th>Caracteristiques</th>
      <th>Lits disponibles</th>
    </tr> 
	{{foreach from=$services item=curr_service}}
	<tr id="service{{$curr_service->_id}}-trigger">
	  <td colspan="4">{{$curr_service->_view}}</td>
	</tr>
    <tbody class="serviceEffect" id="service{{$curr_service->_id}}">
     {{foreach from=$curr_service->_ref_chambres item=curr_chambre}}
      <tr {{if $curr_chambre->_id == $chambreSel->_id}}class="selected"{{/if}}>
        <td><a href="?m={{$m}}&amp;tab={{$tab}}&amp;chambre_id={{$curr_chambre->_id}}&amp;lit_id=0">{{$curr_chambre->nom}}</a></td>
        <td class="text">{{$curr_chambre->caracteristiques|nl2br}}</td>
        <td>
        {{foreach from=$curr_chambre->_ref_lits item=curr_lit}}
          <a href="?m={{$m}}&amp;tab={{$tab}}&amp;chambre_id={{$curr_lit->chambre_id}}&amp;lit_id={{$curr_lit->_id}}">
            {{$curr_lit->nom}}
          </a>
        {{/foreach}}
        </td>
      </tr>
      {{/foreach}}
    </tbody>
    {{/foreach}}   
    </table>
  </td>
  <td class="halfPane">
    <form name="editChambre" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
    <input type="hidden" name="dosql" value="do_chambre_aed" />
    <input type="hidden" name="chambre_id" value="{{$chambreSel->_id}}" />
    <input type="hidden" name="del" value="0" />
    <table class="form">
    <tr>
      {{if $chambreSel->chambre_id}}
      <th class="title modify" colspan="2">
        Modification de la chambre &lsquo;{{$chambreSel->nom}}&rsquo;
      {{else}}
      <th class="title" colspan="2">
        Création d'une chambre
      </th>
      {{/if}}
    </tr>
    <tr>
      <th>{{mb_label object=$chambreSel field="nom"}}</th>
      <td>{{mb_field object=$chambreSel field="nom"}}</td>
    </tr>
	<tr>
	 <th>{{mb_label object=$chambreSel field="service_id"}}</th>
	  <td>
        <select name="service_id" class="{{$chambreSel->_props.service_id}}">
          <option value="">&mdash; Choisir un service &mdash;</option>
        {{foreach from=$services item=curr_service}}
          <option value="{{$curr_service->_id}}" {{if $curr_service->_id == $chambreSel->service_id}}selected="selected"{{/if}}>
            {{$curr_service->nom}}
          </option>
        {{/foreach}}
        </select>
	  </td>
	</tr>    
    <tr>
      <th>{{mb_label object=$chambreSel field="caracteristiques"}}</th>
      <td>{{mb_field object=$chambreSel field="caracteristiques"}}</td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        {{if $chambreSel->_id}}
        <button class="submit" type="submit">Valider</button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la chambre',objName:'{{$chambreSel->nom|smarty:nodefaults|JSAttribute}}'})">
          Supprimer
        </button>
        {{else}}
        <button class="submit" name="btnFuseAction" type="submit">Créer</button>
        {{/if}}
      </td>
    </tr>
    </table>
	</form>
  {{if $chambreSel->chambre_id}}
  <a class="buttonnew" href="?m={{$m}}&amp;tab={{$tab}}&amp;chambre_id={{$chambreSel->_id}}&amp;lit_id=0">
    Ajouter un lit
  </a>
  <form name="editLit" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_lit_aed" />
  <input type="hidden" name="lit_id" value="{{$litSel->_id}}" />
  <input type="hidden" name="chambre_id" value="{{$chambreSel->_id}}" />
  <input type="hidden" name="del" value="0" />
    <table class="form">
    <tr>
      <th class="category" colspan="2">Lits</th>
    {{foreach from=$chambreSel->_ref_lits item=curr_lit}}
    <tr>
      <th>Lit</th>
      <td><a href="?m={{$m}}&amp;tab={{$tab}}&amp;chambre_id={{$curr_lit->chambre_id}}&amp;lit_id={{$curr_lit->lit_id}}">{{$curr_lit->nom}}</a></td>
    </tr>
	  {{/foreach}}
    <tr>
      <th>{{mb_label object=$litSel field="nom"}}</th>
      <td>
        {{mb_field object=$litSel field="nom"}}
        {{if $litSel->lit_id}}
        <button class="modify" type="submit">Modifier</button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le lit',objName:'{{$litSel->nom|smarty:nodefaults|JSAttribute}}'})">
          Supprimer
        </button>
        {{else}}
        <button class="submit" type="submit">Créer</button>
        {{/if}}
      </td>
    </tr>
    </table>
  </form>
  {{/if}}    
  </td>
</tr>
</table>
