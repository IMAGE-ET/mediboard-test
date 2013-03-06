<div id="calendar-container"></div>
{{if $can->edit}}

<form name="chgFilter" method="get" onsubmit="return onSubmitFormAjax(this, null, 'tableau');">
  <input type="hidden" name="m" value="dPhospi" />
  <input type="hidden" name="a" value="vw_affectations" />
  <table class="form">
    <tr>
      <td>
        {{mb_field object=$emptySejour field="_type_admission" style="width: 16em;" onchange="this.form.onsubmit()"}}
      </td>
    </tr>
    <tr>
      <td>
        <select name="triAdm" style="width: 16em;" onchange="this.form.onsubmit()">
          <option value="praticien"   {{if $triAdm == "praticien"}}  selected="selected"{{/if}}>Tri par praticien</option>
          <option value="date_entree" {{if $triAdm == "date_entree"}}selected="selected"{{/if}}>Tri par heure d'entrée</option>
          <option value="patient"     {{if $triAdm == "patient"}}    selected="selected"{{/if}}>Tri par patient</option>
        </select>
      </td>
    </tr>
    <tr>
      <td>
        <select name="filterFunction" style="width: 16em;" onchange="this.form.onsubmit()">
          <option value=""> &mdash; Toutes les fonctions</option>
          {{foreach from=$functions_filter item=_function}}
            <option value="{{$_function->_id}}" {{if $_function->_id == $filterFunction}}selected="selected"{{/if}} class="mediuser" style="border-color: #{{$_function->color}};">{{$_function}}</option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    {{if $prestations_journalieres|@count}}
      <tr>
        <td>
          <select name="prestation_id" style="width: 16em;" onchange="this.form.onsubmit()">
            <option value="">&mdash; {{tr}}All{{/tr}}</option>
            {{foreach from=$prestations_journalieres item=_prestation}}
              <option value="{{$_prestation->_id}}" {{if $_prestation->_id == $prestation_id}}selected{{/if}}>{{$_prestation}}</option>
            {{/foreach}}
          </select>
        </td>
      </tr>
    {{/if}}
  </table>
</form>

<form name="addAffectationsejour" action="?m={{$m}}" method="post">
<input type="hidden" name="m" value="dPhospi" />
<input type="hidden" name="dosql" value="do_affectation_aed" />
<input type="hidden" name="lit_id" value="" />
<input type="hidden" name="sejour_id" value="" />
      
<table class="sejourcollapse" id="sejour_bloque">
  <tr>
  <td class="selectsejour">
    <input type="radio" id="hospitalisation" onclick="selectHospitalisation()" />
    <script type="text/javascript">new Draggable('sejour_bloque', {revert:true})</script>
  </td>
  <td class="patient" onclick="flipSejour('bloque')">
    <strong><a name="sejourbloque">[BLOQUER UN LIT]</a></strong>
  </td>
  </tr>
  <tr>
    <td><em>Entrée</em></td>
    <td>{{mb_field object=$affectation field="entree" form="addAffectationsejour" register=true}}</td>
  </tr>
  <tr>
    <td><em>Sortie</em></td>
    <td>{{mb_field object=$affectation field="sortie" form="addAffectationsejour" register=true}}</td>
</tr>
<tr>
  <td class="date highlight" colspan="2">
    <label for="rques">Remarques</label> : 
    <textarea name="rques"></textarea>
  </td>
</tr>
</table>
</form>

{{foreach from=$groupSejourNonAffectes key=group_name item=sejourNonAffectes}}
  {{if $group_name == "couloir"}}
    {{mb_include module=hospi template=inc_affectations_couloir}}
  {{else}}
    {{mb_include module=hospi template=inc_affectations_liste}}
  {{/if}}
{{/foreach}}
{{/if}}