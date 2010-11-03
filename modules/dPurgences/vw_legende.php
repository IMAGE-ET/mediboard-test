<table class="tbl">
  <tr>
    <th colspan="2">Urgences</th>
  </tr>
  <tr>
    <th>Couleur</th>
    <th>Description</th>
  </tr>
  <tr>
    <td></td>
    <td class="text">Patient non pris en charge</td>
  </tr>
  <tr>
    <td style="background-color:#cfc"></td>
    <td class="text">Patient pris en charge</td>
  </tr>
  <tr>
    <td style="border-right: 5px solid black"></td>
    <td class="text">Patient sorti</td>
  </tr>
  <tr>
    <th colspan="2">Degrés d'urgences</th>
  </tr>
  <tr>
    <th>Couleur</th>
    <th>Description</th>
  </tr>
  <tr>
    <td style="background-color:#0F0"></td>
    <td class="text">Etat stable</td>
  </tr>
  <tr>
    <td style="background-color:#0F0"></td>
    <td class="text">Psy (Etat stable, terrain psy)</td>
  </tr>
  <tr>
    <td style="background-color:#9F0"></td>
    <td class="text">Etat stable, examens complémentaires</td>
  </tr>
  <tr>
    <td style="background-color:#FF0"></td>
    <td class="text">Etat pouvant s'aggraver</td>
  </tr>  
  <tr>
    <td style="background-color:#FFCD00"></td>
    <td class="text">Pronostic vital engagé</td>
  </tr>
  <tr>
    <td style="background-color:#F60"></td>
    <td class="text">Réanimation</td>
  </tr>
  <tr>
    <td style="background-color:#F00"></td>
    <td class="text">Décès</td>
  </tr>
  <tr>
    <th colspan="2">Temps d'attente</th>
  </tr>
  <tr>
    <th>Image</th>
    <th>Description</th>
  </tr>
  <tr>
    <td>
      <img src="images/icons/attente_first_part.png"></img>
    </td>
    <td>
      Attente depuis moins de <?php echo mbTransformTime(CAppUI::conf("dPurgences attente_first_part"), null, '%Hh%M')?>
    </td>
  </tr>
  <tr>
    <td>
      <img src="images/icons/attente_second_part.png"></img>
    </td>
    <td>
      Attente entre <?php echo mbTransformTime(CAppUI::conf("dPurgences attente_first_part"), null, '%Hh%M')?> et <?php echo mbTransformTime(CAppUI::conf("dPurgences attente_second_part"), null, '%Hh%M') ?>
    </td>
  </tr>
  <tr>
    <td>
      <img src="images/icons/attente_third_part.png"></img>
    </td>
    <td>
      Attente entre <?php echo mbTransformTime(CAppUI::conf("dPurgences attente_second_part"), null, '%Hh%M')?> et <?php echo mbTransformTime(CAppUI::conf("dPurgences attente_third_part"), null, '%Hh%M')?>
    </td>
  </tr>
  <tr>
    <td>
      <img src="images/icons/attente_fourth_part.png"></img>
    </td>
    <td>
      Attente de plus de <?php echo mbTransformTime(CAppUI::conf("dPurgences attente_third_part"), null, '%Hh%M')?>
    </td>
  </tr>
</table>