<script type="text/javascirpt">
  Main.add(function() {
    Control.Tabs.create("sejours_non_affectes", true);
  });
</script>

<form name="chgFilter" action="?" method="get" onsubmit="return onSubmitFormAjax(this,null, 'list_affectations');">
  <input type="hidden" name="m" value="dPhospi" />
  <input type="hidden" name="a" value="ajax_vw_affectations" />

  {{mb_field object=$sejour field="_type_admission" style="width: 16em;" onchange="this.form.onsubmit()"}}

  <select name="triAdm" style="width: 16em;" onchange="this.form.onsubmit()">
    <option value="praticien" {{if $triAdm == "praticien"}}selected="selected"{{/if}}>Tri par praticien</option>
    <option value="date_entree" {{if $triAdm == "date_entree"}}selected="selected"{{/if}}>Tri par heure d'entrée</option>
  </select>
  <select name="filter_function" style="width: 16em;" onchange="this.form.onsubmit()">
    <option value=""> &mdash; Toutes les fonctions</option>
    {{foreach from=$functions_filter item=_function}}
      <option value="{{$_function->_id}}" {{if $_function->_id == $filter_function}}selected="selected"{{/if}} class="mediuser" style="border-color: #{{$_function->color}};">{{$_function}}</option>
    {{/foreach}}
  </select>
</form>

<div id="lit_bloque" class="clit_bloque draggable" style="display: inline-block;">
  <strong>[BLOQUER UN LIT]</strong>
</div>

<script type="text/javascript">
  new Draggable($('lit_bloque'), dragOptions);
</script>

</div>
<ul id="sejours_non_affectes" class="control_tabs">
  {{foreach from=$sejours_non_affectes key=group_name item=_sejours}}
    <li>
      <a {{if !$_sejours|@count}}class="empty"{{/if}} href="#{{$group_name}}">
        {{tr}}CSejour.groupe.{{$group_name}}{{/tr}}
        ({{$_sejours|@count}})
      </a>
    </li>
  {{/foreach}}
</ul>

<hr class="control_tabs" />

{{foreach from=$sejours_non_affectes key=group_name item=_sejours}}
  <div id="{{$group_name}}" {{if !$_sejours|@count}}class="empty"{{/if}}>
    
    {{foreach from=$_sejours item=_sejour}}
      {{assign var=patient value=$_sejour->_ref_patient}}
      {{assign var=praticien value=$_sejour->_ref_praticien}}
      <div class="draggable text sejour_non_affecte" style="border-left: 4px solid #{{$praticien->_ref_function->color}}"
        id="sejour_{{$_sejour->_id}}" data-patient_id="{{$patient->_id}}" data-sejour_id="{{$_sejour->_id}}">
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}');">
            {{$patient->nom}} {{$patient->prenom}}
          </span>
      </div>
      
      <script type="text/javascript">
        new Draggable($('sejour_{{$_sejour->_id}}'), dragOptions);
      </script>
    {{foreachelse}}
      {{tr}}CSejour.none{{/tr}}
    {{/foreach}}

  </div>
{{/foreach}}