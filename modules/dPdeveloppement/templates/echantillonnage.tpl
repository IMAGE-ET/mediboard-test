{{include file="inc_echantillonnage_fonctions.tpl"}}
<form action="?m={{$m}}" name="echantillonage" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="dosql" value="do_echantillonnage" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  
  <table class="form">
    <tr>
      <td>
        <ul id="tab-echantillonnage" class="control_tabs">
          <li><a href="#etape1">Etape 1 - Etablissement</a></li>
          <li><a href="#etape2" id="btn_etape2" style="display: none;">Etape 2 - Cabinet / Services / Salles</a></li>
          <li><a href="#etape3" id="btn_etape3" style="display: none;">Etape 3 - Patients / Praticiens</a></li>
          <li><a href="#etape4" id="btn_etape4" style="display: none;">Etape 4 - Consultations / Interventions</a></li>
        </ul>
        <hr class="control_tabs" />
        <div id="etape1" style="display: none;">{{include file="inc_echantillonnage_etape1.tpl"}}</div>
        <div id="etape2" style="display: none;"></div>
        <div id="etape3" style="display: none;"></div>
        <div id="etape4" style="display: none;"></div>
      </td>
    </tr>
  </table>
</form>
