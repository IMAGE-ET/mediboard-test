<form name="editFicheAutonomie" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="ssr" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="fiche_autonomie_id" value="{{$fiche_autonomie->_id}}" />
  <input type="hidden" name="dosql" value="do_fiche_autonomie_aed" />
  <input type="hidden" name="del" value="0" />
  
  <table class="form">
    <tr>
      <th class="category" colspan="10">{{tr}}CFicheAutonomie-autonomie-perso{{/tr}}</th>
    </tr>
    <tr>
      <th>{{mb_label object=$fiche_autonomie field="alimentation"}}</th>
      <td>{{mb_field object=$fiche_autonomie field="alimentation" typeEnum="radio" separator="</td><td>"}}</td>
			<td/>
    </tr>
    <tr>
      <th>{{mb_label object=$fiche_autonomie field="toilette"}}</th>
      <td>{{mb_field object=$fiche_autonomie field="toilette" typeEnum="radio" separator="</td><td>"}}</td>
      <td/>
    </tr>
    <tr>
      <th>{{mb_label object=$fiche_autonomie field="habillage_haut"}}</th>
      <td>{{mb_field object=$fiche_autonomie field="habillage_haut" typeEnum="radio" separator="</td><td>"}}</td>
      <td/>
    </tr>
    <tr>
      <th>{{mb_label object=$fiche_autonomie field="habillage_bas"}}</th>
      <td>{{mb_field object=$fiche_autonomie field="habillage_bas" typeEnum="radio" separator="</td><td>"}}</td>
      <td/>
    </tr>
    <tr>
      <th>{{mb_label object=$fiche_autonomie field="utilisation_toilette"}}</th>
      <td>{{mb_field object=$fiche_autonomie field="utilisation_toilette" typeEnum="radio" separator="</td><td>"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$fiche_autonomie field="transfert_lit"}}</th>
      <td>{{mb_field object=$fiche_autonomie field="transfert_lit" typeEnum="radio" separator="</td><td>"}}</td>
      <td/>
    </tr>
    <tr>
      <th>{{mb_label object=$fiche_autonomie field="locomotion"}}</th>
      <td>{{mb_field object=$fiche_autonomie field="locomotion" typeEnum="radio" separator="</td><td>"}}</td>
      <td/>
    </tr>
    <tr>
      <th>{{mb_label object=$fiche_autonomie field="locomotion_materiel"}}</th>
      <td>{{mb_field object=$fiche_autonomie field="locomotion_materiel" typeEnum="radio" separator="</td><td>"}}</td>
      <td/>
    </tr>
    <tr>
      <th>{{mb_label object=$fiche_autonomie field="escalier"}}</th>
      <td>{{mb_field object=$fiche_autonomie field="escalier" typeEnum="radio" separator="</td><td>"}}</td>
      <td/>
    </tr>

    <tr>
      <th class="category" colspan="10">{{tr}}CFicheAutonomie-soins-cutanes{{/tr}}</th>
    </tr>

    <tr>
      <th>{{mb_label object=$fiche_autonomie field="soins_cutanes"}}</th>
      <td colspan="10">{{mb_field object=$fiche_autonomie field="soins_cutanes"}}</td>
    </tr>

    <tr>
      <th>{{mb_label object=$fiche_autonomie field="pansement"}}</th>
      <td>{{mb_field object=$fiche_autonomie field="pansement" typeEnum="radio" separator="</td><td>" default=""}}</td>
      <td colspan="2" />
    </tr>
    <tr>
      <th>{{mb_label object=$fiche_autonomie field="escarre"}}</th>
      <td>{{mb_field object=$fiche_autonomie field="escarre" typeEnum="radio" separator="</td><td>" default=""}}</td>
      <td colspan="2" />
    </tr>

    <tr>
      <th class="category" colspan="10">{{tr}}CFicheAutonomie-capacite_relationnelle{{/tr}}</th>
    </tr>
    <tr>
      <th>{{mb_label object=$fiche_autonomie field="comprehension"}}</th>
      <td>{{mb_field object=$fiche_autonomie field="comprehension" typeEnum="radio" separator="</td><td>"}}</td>
      <td colspan="2" />
    </tr>
    <tr>
      <th>{{mb_label object=$fiche_autonomie field="expression"}}</th>
      <td>{{mb_field object=$fiche_autonomie field="expression" typeEnum="radio" separator="</td><td>"}}</td>
      <td colspan="2" />
    </tr>
    <tr>
      <th>{{mb_label object=$fiche_autonomie field="memoire"}}</th>
      <td>{{mb_field object=$fiche_autonomie field="memoire" typeEnum="radio" separator="</td><td>"}}</td>
      <td colspan="2" />
    </tr>
    <tr>
      <th>{{mb_label object=$fiche_autonomie field="resolution_pb"}}</th>
      <td>{{mb_field object=$fiche_autonomie field="resolution_pb" typeEnum="radio" separator="</td><td>"}}</td>
      <td colspan="2" />
    </tr>

    <tr>
      <th class="category" colspan="10">{{tr}}CFicheAutonomie-etat_psychique{{/tr}}</th>
    </tr>
    <tr>
      <th>{{mb_label object=$fiche_autonomie field="etat_psychique"}}</th>
      <td colspan="10">{{mb_field object=$fiche_autonomie field="etat_psychique"}}</td>
    </tr>
		
    <tr>
      <th class="category" colspan="10">{{tr}}CFicheAutonomie-devenir_envisage{{/tr}}</th>
    </tr>
    <tr>
      <th>{{mb_label object=$fiche_autonomie field="devenir_envisage"}}</th>
      <td>
          Domicile <input type="radio" name="_devenir_envisage" value="1" {{if !$fiche_autonomie->devenir_envisage}}checked="checked"{{/if}} onchange="$V(this.form.devenir_envisage,''); $('devenir').hide();"/>
          Autres   <input type="radio" name="_devenir_envisage" value="0" {{if  $fiche_autonomie->devenir_envisage}}checked="checked"{{/if}} onchange="$('devenir').show();"/>
      </td>
      <td colspan="4">
        <div id="devenir" {{if !$fiche_autonomie->devenir_envisage}}style="display: none"{{/if}}>
          {{mb_field object=$fiche_autonomie field="devenir_envisage"}}
        </div>
      </td>
    </tr>
    <tr>
      <td class="button" colspan="6">
        <button class="submit" type="submit">
          {{tr}}Save{{/tr}}
        </button>
      </td>
    </tr>
  </table>
</form>