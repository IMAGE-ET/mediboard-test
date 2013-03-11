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
    <td style="background-color:{{if $conf.ref_pays == 1}}#0F0{{else}}#F00{{/if}}"></td>
    <td class="text">{{tr}}CRPU.ccmu.1.desc{{/tr}}</td>
  </tr>
  {{if $conf.ref_pays == 1}}
  <tr>
    <td style="background-color:#0F0"></td>
    <td class="text">{{tr}}CRPU.ccmu.P.desc{{/tr}}</td>
  </tr>
  {{/if}}
  <tr>
    <td style="background-color:{{if $conf.ref_pays == 1}}#9F0{{else}}#F60{{/if}}"></td>
    <td class="text">{{tr}}CRPU.ccmu.2.desc{{/tr}}</td>
  </tr>
  <tr>
    <td style="background-color:{{if $conf.ref_pays == 1}}#FF0{{else}}#FF0{{/if}}"></td>
    <td class="text">{{tr}}CRPU.ccmu.3.desc{{/tr}}</td>
  </tr>  
  <tr>
    <td style="background-color:{{if $conf.ref_pays == 1}}#FFCD00{{else}}#9F0{{/if}}"></td>
    <td class="text">{{tr}}CRPU.ccmu.4.desc{{/tr}}</td>
  </tr>
  {{if $conf.ref_pays == 1}}
  <tr>
    <td style="background-color:#F60"></td>
    <td class="text">{{tr}}CRPU.ccmu.5.desc{{/tr}}</td>
  </tr>
  <tr>
    <td style="background-color:#F00"></td>
    <td class="text">{{tr}}CRPU.ccmu.D.desc{{/tr}}</td>
  </tr>
  {{/if}}
  <tr>
    <th colspan="2">{{tr}}CRPU-_attente{{/tr}}</th>
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
      Attente depuis moins de {{$conf.dPurgences.attente_first_part|date_format:"%Hh%M"}}
    </td>
  </tr>
  <tr>
    <td>
      <img src="images/icons/attente_second_part.png"></img>
    </td>
    <td>
      Attente entre {{$conf.dPurgences.attente_first_part|date_format:"%Hh%M"}} et {{$conf.dPurgences.attente_second_part|date_format:"%Hh%M"}}
    </td>
  </tr>
  <tr>
    <td>
      <img src="images/icons/attente_third_part.png"></img>
    </td>
    <td>
      Attente entre {{$conf.dPurgences.attente_second_part|date_format:"%Hh%M"}} et {{$conf.dPurgences.attente_third_part|date_format:"%Hh%M"}}
    </td>
  </tr>
  <tr>
    <td>
      <img src="images/icons/attente_fourth_part.png"></img>
    </td>
    <td>
      Attente de plus de {{$conf.dPurgences.attente_third_part|date_format:"%Hh%M"}}
    </td>
  </tr>
</table>