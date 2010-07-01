<script type="text/javascript">
  
Main.add(function () {
  var oFormAutonomie = getForm("editFicheAutonomie");
	var options = {
    objectClass: "CFicheAutonomie", 
    userId: "{{$app->user_id}}"
  }

  new AideSaisie.AutoComplete(oFormAutonomie.soins_cutanes , options);
  new AideSaisie.AutoComplete(oFormAutonomie.etat_psychique, options);  
  new AideSaisie.AutoComplete(oFormAutonomie.antecedents   , options);  
  new AideSaisie.AutoComplete(oFormAutonomie.traitements   , options);  
});

</script>


<form name="editFicheAutonomie" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="ssr" />
  <input type="hidden" name="dosql" value="do_fiche_autonomie_aed" />
  <input type="hidden" name="del" value="0" />

  {{mb_key object=$fiche_autonomie}}
  {{mb_field object=$fiche_autonomie field=sejour_id  hidden=1}}
  
  <table class="form">
    <tr>
      <th class="category" colspan="10">{{tr}}CFicheAutonomie-autonomie-perso{{/tr}}</th>
    </tr>
    <tbody class="hoverable">
	    <tr>
	      <th>{{mb_label object=$fiche_autonomie field="alimentation" typeEnum="radio"}}</th>
	      <td>{{mb_field object=$fiche_autonomie field="alimentation" typeEnum="radio"}}</td>
	    </tr>
    </tbody>

    <tbody class="hoverable">
	    <tr>
	      <th>{{mb_label object=$fiche_autonomie field="toilette" typeEnum="radio"}}</th>
	      <td>{{mb_field object=$fiche_autonomie field="toilette" typeEnum="radio"}}</td>
	    </tr>
    </tbody>

    <tbody class="hoverable">
	    <tr>
	      <th>{{mb_label object=$fiche_autonomie field="habillage_haut" typeEnum="radio"}}</th>
	      <td>{{mb_field object=$fiche_autonomie field="habillage_haut" typeEnum="radio"}}</td>
	    </tr>
    </tbody>

    <tbody class="hoverable">
	    <tr>
	      <th>{{mb_label object=$fiche_autonomie field="habillage_bas" typeEnum="radio"}}</th>
	      <td>{{mb_field object=$fiche_autonomie field="habillage_bas" typeEnum="radio"}}</td>
	    </tr>
    </tbody>

    <tbody class="hoverable">
	    <tr>
	      <th>{{mb_label object=$fiche_autonomie field="toilettes" typeEnum="radio"}}</th>
	      <td>{{mb_field object=$fiche_autonomie field="toilettes" typeEnum="radio"}}</td>
	    </tr>
    </tbody>

    <tbody class="hoverable">
	    <tr>
	      <th>{{mb_label object=$fiche_autonomie field="utilisation_toilette" typeEnum="radio"}}</th>
	      <td>{{mb_field object=$fiche_autonomie field="utilisation_toilette" typeEnum="radio"}}</td>
	    </tr>
    </tbody>

    <tbody class="hoverable">
	    <tr>
	      <th>{{mb_label object=$fiche_autonomie field="transfert_lit" typeEnum="radio"}}</th>
	      <td>{{mb_field object=$fiche_autonomie field="transfert_lit" typeEnum="radio"}}</td>
	    </tr>
    </tbody>

    <tbody class="hoverable">
	    <tr>
	      <th>{{mb_label object=$fiche_autonomie field="locomotion" typeEnum="radio"}}</th>
	      <td>{{mb_field object=$fiche_autonomie field="locomotion" typeEnum="radio"}}</td>
	    </tr>
    </tbody>

    <tbody class="hoverable">
	    <tr>
	      <th>{{mb_label object=$fiche_autonomie field="locomotion_materiel" typeEnum="radio"}}</th>
	      <td>{{mb_field object=$fiche_autonomie field="locomotion_materiel" typeEnum="radio"}}</td>
	    </tr>
    </tbody>

    <tbody class="hoverable">
	    <tr>
	      <th>{{mb_label object=$fiche_autonomie field="escalier" typeEnum="radio"}}</th>
	      <td>{{mb_field object=$fiche_autonomie field="escalier" typeEnum="radio"}}</td>
	    </tr>
    </tbody>

    <tbody class="hoverable">
	    <tr>
	      <th class="category" colspan="10">{{tr}}CFicheAutonomie-soins_cutanes{{/tr}}</th>
	    </tr>
    </tbody>

    <tbody class="hoverable">
	    <tr>
	      <th>{{mb_label object=$fiche_autonomie field="pansement" typeEnum="radio"}}</th>
	      <td>{{mb_field object=$fiche_autonomie field="pansement" typeEnum="radio" default=""}}</td>
	    </tr>
    </tbody>

    <tbody class="hoverable">
	    <tr>
	      <th>{{mb_label object=$fiche_autonomie field="escarre" typeEnum="radio"}}</th>
	      <td>{{mb_field object=$fiche_autonomie field="escarre" typeEnum="radio" default=""}}</td>
	    </tr>
    </tbody>

    <tbody class="hoverable">
	    <tr>
	      <th>{{mb_label object=$fiche_autonomie field="soins_cutanes"}}</th>
	      <td>{{mb_field object=$fiche_autonomie field="soins_cutanes"}}</td>
	    </tr>
    </tbody>

    <tr>
      <th class="category" colspan="10">{{tr}}CFicheAutonomie-capacite_relationnelle{{/tr}}</th>
    </tr>

    <tbody class="hoverable">
	    <tr>
	      <th>{{mb_label object=$fiche_autonomie field="comprehension" typeEnum="radio"}}</th>
	      <td>{{mb_field object=$fiche_autonomie field="comprehension" typeEnum="radio"}}</td>
	    </tr>
    </tbody>

    <tbody class="hoverable">
	    <tr>
	      <th>{{mb_label object=$fiche_autonomie field="expression" typeEnum="radio"}}</th>
	      <td>{{mb_field object=$fiche_autonomie field="expression" typeEnum="radio"}}</td>
	    </tr>
    </tbody>

    <tbody class="hoverable">
	    <tr>
	      <th>{{mb_label object=$fiche_autonomie field="memoire" typeEnum="radio"}}</th>
	      <td>{{mb_field object=$fiche_autonomie field="memoire" typeEnum="radio"}}</td>
	    </tr>
    </tbody>

    <tbody class="hoverable">
	    <tr>
	      <th>{{mb_label object=$fiche_autonomie field="resolution_pb" typeEnum="radio"}}</th>
	      <td>{{mb_field object=$fiche_autonomie field="resolution_pb" typeEnum="radio"}}</td>
	    </tr>
    </tbody>

    <tr>
      <th class="category" colspan="10">{{tr}}CFicheAutonomie-etat_psychique{{/tr}}</th>
    </tr>
		
    <tbody class="hoverable">
	    <tr>
	      <th>{{mb_label object=$fiche_autonomie field="etat_psychique"}}</th>
	      <td>{{mb_field object=$fiche_autonomie field="etat_psychique"}}</td>
	    </tr>
    </tbody>
		
    <tr>
      <th class="category" colspan="10">{{tr}}CFicheAutonomie-antecedents{{/tr}} &amp; {{tr}}CFicheAutonomie-traitements{{/tr}}</th>
    </tr>
    
    <tbody class="hoverable">
      <tr>
        <th>{{mb_label object=$fiche_autonomie field="antecedents"}}</th>
        <td>{{mb_field object=$fiche_autonomie field="antecedents"}}</td>
      </tr>
    </tbody>

    <tbody class="hoverable">
      <tr>
        <th>{{mb_label object=$fiche_autonomie field="traitements"}}</th>
        <td>{{mb_field object=$fiche_autonomie field="traitements"}}</td>
      </tr>
    </tbody>

    <tr>
      <th class="category" colspan="10">{{tr}}CFicheAutonomie-devenir_envisage{{/tr}}</th>
    </tr>
		
    <tbody class="hoverable">
	    <tr>
	      <th>{{mb_label object=$fiche_autonomie field="devenir_envisage"}}</th>
	      <td>
	        Domicile <input type="radio" name="_devenir_envisage" value="1" {{if !$fiche_autonomie->devenir_envisage}}checked="checked"{{/if}} onchange="$V(this.form.devenir_envisage,''); $('devenir').hide();"/>
	        Autres   <input type="radio" name="_devenir_envisage" value="0" {{if  $fiche_autonomie->devenir_envisage}}checked="checked"{{/if}} onchange="$('devenir').show();"/>
	
	        <div id="devenir" {{if !$fiche_autonomie->devenir_envisage}}style="display: none"{{/if}}>
	          {{mb_field object=$fiche_autonomie field="devenir_envisage"}}
	        </div>
	      </td>
	    </tr>
    </tbody>

    <tr>
      <td class="button" colspan="6">
        <button class="submit" type="submit">
          {{tr}}Save{{/tr}}
        </button>
      </td>
    </tr>
  </table>
</form>