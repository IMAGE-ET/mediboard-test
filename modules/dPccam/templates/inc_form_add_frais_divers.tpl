{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage CCAM
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function(){
  var form = getForm("formAddFraisDivers");
  
  var url = new Url("system", "httpreq_field_autocomplete");
  url.addParam("class", "CFraisDivers");
  url.addParam("field", "type_id");
  url.addParam("limit", 30);
  url.addParam("view_field", "code");
  url.addParam("show_view", true);
  url.addParam("input_field", "type_id_autocomplete_view");
  url.addParam("wholeString", false);
  url.autoComplete(form.type_id_autocomplete_view, null, {
    minChars: 1,
    method: "get",
    select: "view",
    dropdown: true,
    afterUpdateElement: function(field,selected){
      var form = field.form;
      $V(form.type_id, selected.getAttribute("id").split("-")[2]);
      $V(form.montant_base, selected.down(".tarif").innerHTML.strip());
      $V(form.facturable, selected.down(".facturable").innerHTML.strip());
    }
  });
});

updateMontant = function(form){
  var oldValue = $V(form._last_coefficient);
  var newValue = $V(form.coefficient);
  
  if (oldValue)
    $V(form.montant_base, $V(form.montant_base) / oldValue * newValue);
    
  $V(form._last_coefficient, newValue);
}

removeFraisDivers = function(id, form) {
  if (!confirm("Voulez vous réelement supprimer de frais divers ?")) return;
  
  form.del.value = 1;
  form.frais_divers_id.value = id;
  
  return onSubmitFormAjax(form, {check: function(){return true}, onComplete: refreshFraisDivers})
}
</script>

<form name="formAddFraisDivers" method="post" action="?" onsubmit="return onSubmitFormAjax(this, {onComplete: refreshFraisDivers})">
  <input type="hidden" name="m" value="dPccam" />
  <input type="hidden" name="dosql" value="do_frais_divers_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="_last_coefficient" value="{{$frais_divers->coefficient}}" />
  
  {{mb_field object=$frais_divers field=object_id hidden=true}}
  {{mb_field object=$frais_divers field=object_class hidden=true}}
  {{mb_key object=$frais_divers}}
  
  <table class="main form">
    <tr>
      <th class="category">{{mb_label class=CFraisDivers field=quantite}}</th>
      <th class="category">{{mb_label class=CFraisDivers field=type_id}}</th>
      <th class="category">{{mb_label class=CFraisDivers field=coefficient}}</th>
      <th class="category">{{mb_label class=CFraisDivers field=facturable}}</th>
      <th class="category">{{mb_label class=CFraisDivers field=montant_base}}</th>
      <th class="category">{{mb_label class=CFraisDivers field=execution}}</th>
      <th class="category">{{mb_label class=CFraisDivers field=executant_id}}</th>
      <th class="category narrow"></th>
    </tr>
    
    <tr>
      <td>{{mb_field object=$frais_divers field=quantite increment=true form=formAddFraisDivers size=2}}</td>
      <td>
        <input type="text" name="type_id_autocomplete_view" class="autocomplete" />
        {{mb_field object=$frais_divers field=type_id hidden=true}}
      </td>
      <td>{{mb_field object=$frais_divers field=coefficient increment=true form=formAddFraisDivers size=2 onchange="updateMontant(this.form)"}}</td>
      <td>{{mb_field object=$frais_divers field=facturable typeEnum=select}}</td>
      <td>{{mb_field object=$frais_divers field=montant_base}}</td>
      <td>{{mb_field object=$frais_divers field=execution form="formAddFraisDivers" register=true}}</td>
      <td>
        <select name="executant_id" style="width: 120px;" class="{{$frais_divers->_props.executant_id}}">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{mb_include module=mediusers template=inc_options_mediuser list=$frais_divers->_list_executants selected=$frais_divers->executant_id}}
        </select>
      </td>
      <td>
        <button type="submit" class="submit notext">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
    
    {{foreach from=$object->_back.frais_divers item=_frais}}
      <tr>
        <td>{{mb_value object=$_frais field=quantite}}</td>
        <td>{{mb_value object=$_frais field=type_id}}</td>
        <td>{{mb_value object=$_frais field=coefficient}}</td>
        <td>{{mb_value object=$_frais field=facturable}}</td>
        <td>{{mb_value object=$_frais field=montant_base}}</td>
        <td>{{mb_value object=$_frais field=execution}}</td>
        <td>{{mb_value object=$_frais field=executant_id}}</td>
        <td>
          <button type="button" class="trash notext" onclick="removeFraisDivers({{$_frais->_id}}, this.form)">
            {{tr}}Delete{{/tr}}
          </button>
        </td>
      </tr>
    {{/foreach}}
  </table>
</form>