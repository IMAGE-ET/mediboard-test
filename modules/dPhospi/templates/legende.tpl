{{if $conf.dPhospi.CLit.alt_icons_sortants}}
  {{assign var=suffixe_icons value="2"}}
{{else}}
  {{assign var=suffixe_icons value=""}}
{{/if}}

<table class="tbl">
  <tr>
    <th class="title">Icone</th>
    <th class="title">Description</th>
  </tr>
  <tr>
    <th colspan="2">Etat des patients</th>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/X{{$suffixe_icons}}.png" name="sortant ce soir" /></td>
    <td class="text">{{tr}}dPhospi-legend-ambu-leaving-tonight{{/tr}}</td>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/O{{$suffixe_icons}}.png" name="sortant demain" /></td>
    <td class="text">{{tr}}dPhospi-legend-hospi-leaving-tomorrow{{/tr}}</td>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/Oo{{$suffixe_icons}}.png" name="sortant aujourd'hui" /></td>
    <td class="text">{{tr}}dPhospi-legend-hospi-leaving-today{{/tr}}</td>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/OC{{$suffixe_icons}}.png" name="déplacé demain" /></td>
    <td class="text">{{tr}}dPhospi-legend-hospi-moved-tomorrow{{/tr}}</td>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/OoC{{$suffixe_icons}}.png" name="déplacé aujourd'hui" /></td>
    <td class="text">{{tr}}dPhospi-legend-hospi-moved-today{{/tr}}</td>
  </tr>
  <tr>
    <td style="text-align: right; font-weight: bold;" class="septique">M. X y</td>
    <td class="text">{{tr}}dPhospi-legend-patient-sceptic{{/tr}}</td>
  </tr>
  <tr>
    <td style="text-align: right; font-weight: bold;" class="patient-not-arrived">M. X y 27/03 17h00</td>
    <td class="text">{{tr var1="17h00" var2="27/03"}}dPhospi-legend-patient-arriving-time%s-date%s{{/tr}}</td>
  </tr>
  <tr>
    <td style="text-align: right;"><strong>M. X y</strong></td>
    <td class="text">{{tr}}dPhospi-legend-patient-present{{/tr}}</td>
  </tr>
  <tr>
    <td class="hatching" style="text-align: right; font-weight: bold;">M. X y</td>
    <td class="text">{{tr}}dPhospi-legend-patient-exit-confirmed{{/tr}}</td>
  </tr>
  <tr>
    <th colspan="2">Alertes</th>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/double.png" name="chambre double possible" /></td>
    <td class="text">{{tr}}dPhospi-legend-double-bed-possible{{/tr}}</td>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/seul.png" name="chambre simple obligatoire" /></td>
    <td class="text">{{tr}}dPhospi-legend-simple-bed-only{{/tr}}</td>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/surb.png" name="collision" /></td>
    <td class="text">{{tr}}dPhospi-legend-collision-double-patient-one-bed{{/tr}}</td>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/sexe.png" name="conflit de sexe" /></td>
    <td class="text">{{tr}}dPhospi-legend-men-woman-same-room{{/tr}}</td>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/age.png" name="ecart d'age important" /></td>
    <td class="text">{{tr}}dPhospi-legend-age-superior-15-years-old{{/tr}}</td>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/prat.png" name="conflit de praticiens" /></td>
    <td class="text">{{tr}}dPhospi-legend-conflict-praticien-2patient-2prat-same-speciality{{/tr}}</td>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/path.png" name="conflit de pathologie" /></td>
    <td class="text">{{tr}}dPhospi-legend-uncompatible-pathology-same-room{{/tr}}</td>
  </tr>
  <tr>
    <td style="text-align: right;"><img src="modules/dPhospi/images/annule.png" name="Chambre plus utilisée" /></td>
    <td class="text">{{tr}}dPhospi-legend-room-abandonned{{/tr}}</td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <button type="button" class="cancel" onclick="Control.Modal.close();">{{tr}}Close{{/tr}}</button>
    </td>
  </tr>
</table>