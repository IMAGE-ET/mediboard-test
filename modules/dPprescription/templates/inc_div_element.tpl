<div id="div_{{$element}}" style="display:none">
  <form action="?" method="get" name="search{{$element}}" onsubmit="return false;">
    <select name="favoris" onchange="Prescription.addLineElement(this.value); this.value = '';">
      <option value="">&mdash; produits les plus utilisés</option>
      {{foreach from=$listFavoris.$element item=curr_element}}
      <option value="{{$curr_element->_id}}">
        {{$curr_element->libelle}}
      </option>
      {{/foreach}}
    </select>
   <input type="text" name="{{$element}}" value="" />
   <input type="hidden" name="element_id" onchange="Prescription.addLineElement(this.value);" />
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
  <button class="add" onclick="$('add_{{$element}}').show();">Ajouter un élément</button>
  <br />
  <div id="add_{{$element}}" style="display: none">
    {{if !$categories.$element|@count}}
    <div class="big-info">
     Impossible de rajouter des éléments de prescription car cette section ne possède pas de catégorie
    </div>
    {{else}}
    <form name="add{{$element}}" method="post" action="" onsubmit="return onSubmitFormAjax(this);">
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
      <button class="submit notext" type="button" onclick="this.form.onsubmit()">Ajouter</button>
    </form>
    {{/if}}
 </div>
</div>

<script type="text/javascript">

prepareForm(document.search{{$element}});
  
url = new Url();
url.setModuleAction("dPprescription", "httpreq_do_element_autocomplete");
url.addParam("category", "{{$element}}");
url.autoComplete("search{{$element}}_{{$element}}", "{{$element}}_auto_complete", {
    minChars: 3,
    updateElement: function(element) { updateFieldsElement(element, 'search{{$element}}', '{{$element}}') }
} );

</script>