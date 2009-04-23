{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<!-- Formulaire d'elements les plus utilisés -->
<form action="?" method="get" name="search{{$element}}" onsubmit="return false;">
  <select name="favoris" onchange="Prescription.addLineElement(this.value,'{{$element}}'); this.value = '';" style="width: 145px;">
    <option value="">&mdash; les plus utilisés</option>
    {{if array_key_exists($element, $listFavoris)}}
    {{foreach from=$listFavoris.$element item=curr_element}}
    <option value="{{$curr_element->_id}}">
      {{$curr_element->libelle}}
    </option>
    {{/foreach}}
    {{/if}}
  </select>
  
  <!-- Boutons d'ajout d'elements et de commentaires -->
  {{if $dPconfig.dPprescription.CPrescription.add_element_category}}
  <button class="new" onclick="$('add_{{$element}}').show();">Ajouter un élément</button>
  {{/if}}
  <button class="new" onclick="$('add_line_comment_{{$element}}').show();" type="button">Ajouter un commentaire</button>
 <br />
 
  <!-- Selecteur d'elements -->
  <input type="text" name="{{$element}}" value="" />
  <input type="hidden" name="element_id" onchange="Prescription.addLineElement(this.value,'{{$element}}');" />
  <div style="display:none;" class="autocomplete" id="{{$element}}_auto_complete"></div>
  <button class="search" type="button" onclick="ElementSelector.init{{$element}}('{{$element}}')">Rechercher</button>
  <script type="text/javascript">   
    ElementSelector.init{{$element}} = function(type){
      this.sForm = "search{{$element}}";
      this.sLibelle = "{{$element}}";
      this.sElement_id = "element_id";
      this.sType = type;
      this.selfClose = false;
      this.pop();
    }
  </script>
</form>

<br />

<!-- Div d'ajout d'element dans la prescription (et dans la nomenclature) -->
{{if $dPconfig.dPprescription.CPrescription.add_element_category}}
<div id="add_{{$element}}" style="display: none">
  {{if !$categories.$element|@count}}
    <div class="big-info">
      Impossible de rajouter des éléments de prescription car cette section ne possède pas de catégorie
    </div>
  {{else}}
    <button class="cancel notext" type="button" onclick="$('add_{{$element}}').hide();">Cacher</button>
      <form name="add{{$element}}" method="post" action="" onsubmit="document.addLineElement._chapitre.value='{{$element}}'; return onSubmitFormAjax(this);">
      <input type="hidden" name="m" value="dPprescription" />
      <input type="hidden" name="dosql" value="do_element_prescription_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="element_prescription_id" value="" />
      <input type="hidden" name="callback" value="Prescription.addLineElement" />
      <select name="category_prescription_id">
      {{foreach from=$categories.$element item=cat}}
        <option value="{{$cat->_id}}">{{$cat->_view}}</option>
      {{/foreach}}
      </select>
      <input name="libelle" type="text" size="80" />
      <button class="submit notext" type="button" 
              onclick="this.form.onsubmit()">Ajouter</button>
    </form>
  {{/if}}
</div>
{{/if}}

<!-- Div d'ajout de commentaires -->
<div id="add_line_comment_{{$element}}" style="display: none">
  {{if !$categories.$element|@count}}
    <div class="big-info">
      Impossible de rajouter des commentaires car cette section ne possède pas de catégorie
    </div>
  {{else}}
    <button class="cancel notext" type="button" onclick="$('add_line_comment_{{$element}}').hide();">Cacher</button>
    <form name="addLineComment{{$element}}" method="post" action="" 
          onsubmit="return Prescription.onSubmitCommentaire(this,'{{$prescription->_id}}','{{$element}}');">
      <input type="hidden" name="m" value="dPprescription" />
      <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="prescription_line_comment_id" value="" />
      <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
      <input type="hidden" name="praticien_id" value="{{$app->user_id}}" />
      <input type="hidden" name="creator_id" value="{{$app->user_id}}" />
      <select name="category_prescription_id">
        {{foreach from=$categories.$element item=cat}}
        <option value="{{$cat->_id}}">{{$cat->_view}}</option>
        {{/foreach}}
      </select>
      <input name="commentaire" type="text" size="70" />
      <button class="submit notext" type="button" 
              onclick="if(document.selPraticienLine){
                         this.form.praticien_id.value = document.selPraticienLine.praticien_id.value;
                       }                        
                       this.form.onsubmit();">Ajouter</button>
    </form>
  {{/if}}
</div>