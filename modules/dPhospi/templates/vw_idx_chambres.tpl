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
	<tr id="service{{$curr_service->service_id}}-trigger">
	  <td colspan="4">{{$curr_service->nom}}</td>
	</tr>
    <tbody class="serviceEffect" id="service{{$curr_service->service_id}}">
     {{foreach from=$curr_service->_ref_chambres item=curr_chambre}}
      <tr>
        <td><a href="?m={{$m}}&amp;tab={{$tab}}&amp;chambre_id={{$curr_chambre->chambre_id}}&amp;lit_id=0">{{$curr_chambre->nom}}</a></td>
        <td class="text">{{$curr_chambre->caracteristiques|nl2br}}</td>
        <td>
        {{foreach from=$curr_chambre->_ref_lits item=curr_lit}}
          <a href="?m={{$m}}&amp;tab={{$tab}}&amp;chambre_id={{$curr_lit->chambre_id}}&amp;lit_id={{$curr_lit->lit_id}}">{{$curr_lit->nom}}</a>
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
    <input type="hidden" name="chambre_id" value="{{$chambreSel->chambre_id}}" />
    <input type="hidden" name="del" value="0" />

    <table class="form">

    <tr>
      <th class="category" colspan="2">
      {{if $chambreSel->chambre_id}}
        Modification du chambre &lsquo;{{$chambreSel->nom}}&rsquo;
      {{else}}
        Création d'un chambre
      {{/if}}
      </th>
    </tr>

    <tr>
      <th><label for="nom" title="intitulé du chambre, obligatoire.">Intitulé</label></th>
      <td><input type="text" name="nom" title="{{$chambreSel->_props.nom}}" value="{{$chambreSel->nom}}" /></td>
    </tr>

	<tr>
     <th><label for="service_id" title="Service auquel la chambre est rattaché, obligatoire.">Service</label></th>
	  <td>
        <select name="service_id" title="{{$chambreSel->_props.service_id}}">
          <option value="">&mdash; Choisir un service &mdash;</option>
        {{foreach from=$services item=curr_service}}
          <option value="{{$curr_service->service_id}}" {{if $curr_service->service_id == $chambreSel->service_id}}selected="selected"{{/if}}>{{$curr_service->nom}}</option>
        {{/foreach}}
        </select>
	  </td>
	</tr>
	    
    <tr>
      <th><label for="caracteristiques" title="Caracteristiques du chambre.">Caractéristiques</label></th>
      <td>
        <textarea name="caracteristiques" rows="4">{{$chambreSel->caracteristiques}}</textarea></td>
    </tr>

    <tr>
      <td class="button" colspan="2">
        {{if $chambreSel->chambre_id}}
        <button class="submit" type="submit">Valider</button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la chambre',objName:'{{$chambreSel->nom|escape:javascript}}'})">
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
  <a class="buttonnew" href="?m={{$m}}&amp;tab={{$tab}}&amp;chambre_id={{$curr_lit->chambre_id}}&amp;lit_id=0">
    Ajouter un lit
  </a>

  <form name="editLit" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

  <input type="hidden" name="dosql" value="do_lit_aed" />
  <input type="hidden" name="lit_id" value="{{$litSel->lit_id}}" />
  <input type="hidden" name="chambre_id" value="{{$chambreSel->chambre_id}}" />
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
      <th><label for="nom" title="Nom du lit">Nom</label></th>
      <td>
        <input type="text" name="nom" title="{{$litSel->_props.nom}}" value="{{$litSel->nom}}" />
        {{if $litSel->lit_id}}
        <button class="modify" type="submit">Modifier</button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le lit',objName:'{{$litSel->nom|escape:javascript}}'})">
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
