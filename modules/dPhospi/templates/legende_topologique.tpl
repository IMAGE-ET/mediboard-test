<table class="tbl">
  <tr>
    <th class="title">Icone</th>
    <th class="title">Description</th>
  </tr>
  <tr>
    <th colspan="2">Etat des patients</th>
  </tr>
  <tr>
    <td style="text-align: right; font-weight: bold;" class="septique">M. X y</td>
    <td class="text">Patient septique</td>
  </tr>
  <tr>
    <td style="text-align: right; font-weight: bold;" class="patient-not-arrived">M. X y</td>
    <td class="text">Patient qui doit arriver dans cette chambre</td>
  </tr>
  <tr>
    <td style="text-align: right;"><strong>M. X y</strong></td>
    <td class="text">Patient présent</td>
  </tr>
  <tr>
    <td style="background-image:url(images/icons/ray.gif); background-repeat:repeat; text-align: right; font-weight: bold;">M. X y</td>
    <td class="text">Patient dont la sortie aujourd'hui est confirmée</td>
  </tr>
  <tr>
    <td colspan="2" class="button">
      <button type="button" class="cancel" onclick="Control.Modal.close();">{{tr}}Close{{/tr}}</button>
    </td>
  </tr>
</table>