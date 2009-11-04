<table class="main">
  <tr>
    <td class="halfPane" rowspan="2">
      <a class="button new" href="?m=dPmateriel&amp;tab=vw_idx_fournisseur&amp;fournisseur_id=0">
        Ajouter un nouveau fournisseur
      </a>
      <table class="tbl">
        <tr>
          <th>Société</th>
          <th>Correspondant</th>
          <th>Adresse</th>
          <th>Téléphone</th>
          <th>E-Mail</th>
        </tr>
        {{foreach from=$listFournisseur item=curr_fournisseur}}
        <tr {{if $curr_fournisseur->_id == $fournisseur->_id}}class="selected"{{/if}}>
          <td class="text">
            <a href="?m=dPmateriel&amp;tab=vw_idx_fournisseur&amp;fournisseur_id={{$curr_fournisseur->_id}}" title="Modifier le fournisseur">
              {{$curr_fournisseur->societe}}
            </a>
          </td>
          <td class="text">{{$curr_fournisseur->nom}} {{$curr_fournisseur->prenom}}</td>
          <td class="text">
            {{$curr_fournisseur->adresse|nl2br}}<br />{{$curr_fournisseur->codepostal}} {{$curr_fournisseur->ville}}
          </td>
          <td>{{$curr_fournisseur->telephone}}</td>
          <td>{{$curr_fournisseur->mail}}</td>
        </tr>
        {{/foreach}}       
        
      </table>
    </td>
    <td class="halfPane">
      {{if $can->edit}}
			{{mb_include_script module="dPpatients" script="autocomplete"}}
			<script type="text/javascript">
			Main.add(function () {
			  InseeFields.initCPVille("editFournisseur", "codepostal", "ville", "telephone");
			});
			</script>

      <form name="editFournisseur" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_fournisseur_aed" />
  	  <input type="hidden" name="fournisseur_id" value="{{$fournisseur->_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $fournisseur->_id}}
          <th class="title modify" colspan="2">Modification du fournisseur {{$fournisseur->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Création d'un fournisseur</th>
          {{/if}}
        </tr>
        <tr>
          <th>{{mb_label object=$fournisseur field="societe"}}</th>
          <td>{{mb_field object=$fournisseur field="societe"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$fournisseur field="adresse"}}</th>
          <td>{{mb_field object=$fournisseur field="adresse"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$fournisseur field="codepostal"}}</th>
          <td>{{mb_field object=$fournisseur field="codepostal"}}</td>
        </tr>
        <tr> 
          <th>{{mb_label object=$fournisseur field="ville"}}</th>
          <td>{{mb_field object=$fournisseur field="ville"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$fournisseur field="telephone"}}</th>
          <td>{{mb_field object=$fournisseur field="telephone"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$fournisseur field="mail"}}</th>
          <td>{{mb_field object=$fournisseur field="mail"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$fournisseur field="nom"}}</th>
          <td>{{mb_field object=$fournisseur field="nom"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$fournisseur field="prenom"}}</th>
          <td>{{mb_field object=$fournisseur field="prenom"}}</td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            <button class="submit" type="submit">Valider</button>
            {{if $fournisseur->_id}}
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le fournisseur',objName:'{{$fournisseur->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{/if}}
          </td>
        </tr> 
      </table>
      </form>
      {{/if}}
    </td>
  </tr>
  <tr>
    <td class="halfPane">
      {{if $fournisseur->_id}}
      <button class="new" type="button" onclick="window.location='?m=dPmateriel&amp;tab=vw_idx_refmateriel&amp;reference_id=0&amp;fournisseur_id={{$fournisseur->_id}}'">
        Créer une nouvelle référence pour ce fournisseur
      </button>
      {{/if}}
      <table class="tbl">
        <tr>
          <th class="title" colspan="4">Référence(s) correspondante(s)</th>
        </tr>
        <tr>
           <th>Matériel</th>
           <th>Quantité</th>
           <th>Prix</th>
           <th>Prix Unitaire</th>
         </tr>
         {{foreach from=$fournisseur->_ref_references item=curr_refmateriel}}
         <tr>
           <td class="text">{{$curr_refmateriel->_ref_materiel->nom}}</td>
           <td>{{$curr_refmateriel->quantite}}</td>
           <td>{{$curr_refmateriel->prix}}</td>
           <td>{{$curr_refmateriel->_prix_unitaire|string_format:"%.2f"}}</td>
         </tr>
         {{foreachelse}}
         <tr>
           <td class="button" colspan="4">Aucune référence trouvée</td>
         </tr>
         {{/foreach}}
       </table>
    </td>
  </tr>
</table>