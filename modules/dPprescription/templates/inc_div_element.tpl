{{assign var=_prescription value=$prescription->_ref_lines_elements_comments}}
{{assign var=lines_element value=$_prescription.$element}}

{{assign var=nb_elt_element value=$lines_element.element|@count}}
{{assign var=nb_elt_comment value=$lines_element.comment|@count}}
{{assign var=nb_elt_total value=$nb_elt_element+$nb_elt_comment}}
<script type="text/javascript">

Prescription.refreshTabHeader("div_{{$element}}","{{$nb_elt_total}}");

</script>

  <form action="?" method="get" name="search{{$element}}" onsubmit="return false;">
    <select name="favoris" onchange="Prescription.addLineElement(this.value,'{{$element}}'); this.value = '';">
      <option value="">&mdash; produits les plus utilisés</option>
      {{foreach from=$listFavoris.$element item=curr_element}}
      <option value="{{$curr_element->_id}}">
        {{$curr_element->libelle}}
      </option>
      {{/foreach}}
    </select>
    
    {{if $dPconfig.dPprescription.CPrescription.add_element_category}}
    <button class="add" onclick="$('add_{{$element}}').show();">Ajouter un élément</button>
    {{/if}}
  
  
    <button class="add" onclick="$('add_line_comment_{{$element}}').show();">Ajouter une ligne de commentaire</button>
   <br />
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
  {{if $dPconfig.dPprescription.CPrescription.add_element_category}}
  <div id="add_{{$element}}" style="display: none">
    {{if !$categories.$element|@count}}
    <div class="big-info">
     Impossible de rajouter des éléments de prescription car cette section ne possède pas de catégorie
    </div>
    {{else}}
    <button class="remove notext" type="button" onclick="$('add_{{$element}}').hide();">Cacher</button>
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
 {{/if}}
 <div id="add_line_comment_{{$element}}" style="display: none">
   <button class="remove notext" type="button" onclick="$('add_line_comment_{{$element}}').hide();">Cacher</button>
   <form name="addLineComment{{$element}}" method="post" action="" 
         onsubmit="return Prescription.onSubmitCommentaire(this,'{{$prescription->_id}}','{{$element}}');">
      <input type="hidden" name="m" value="dPprescription" />
      <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="prescription_line_comment_id" value="" />
      <input type="hidden" name="prescription_id" value="{{$prescription->_id}}" />
      <input type="hidden" name="chapitre" value="{{$element}}" />
      <input name="commentaire" type="text" size="98" />
      <button class="submit notext" type="button" onclick="this.form.onsubmit();">Ajouter</button>
    </form>
 </div>


{{if $lines_element.element || $lines_element.comment}}

<table class="tbl">    
  <!-- Si il y a des elements de type element dans la prescription -->
  <tr>
    <th colspan="6">
      {{tr}}CCategoryPrescription.chapitre.{{$element}}{{/tr}}
    </th>
  </tr>
  {{foreach from=$lines_element.element item=_line_element}}
  
  <tbody class="hoverable">
  <tr>
    <td  style="width: 25px">
      <button type="button" class="trash notext" onclick="Prescription.delLineElement('{{$_line_element->_id}}','{{$element}}')">
        {{tr}}Delete{{/tr}}
      </button>
    </td>
    <td colspan="2">
     {{$_line_element->_ref_element_prescription->_view}}
    </td>
    <td>
      <form name="addCommentElement-{{$_line_element->_id}}" method="post" action="" onsubmit="return onSubmitFormAjax(this);">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="prescription_line_element_id" value="{{$_line_element->_id}}" />
        <input type="text" name="commentaire" value="{{$_line_element->commentaire}}" onchange="this.form.onsubmit();" />
      </form>
    </td>
    <td>
      {{$_line_element->_ref_element_prescription->_ref_category_prescription->_view}}
    </td>
    <td>
      <form name="addALD-{{$_line_element->_id}}" method="post" action="" onsubmit="return onSubmitFormAjax(this);">
        <input type="hidden" name="m" value="dPprescription" />
        <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="prescription_line_element_id" value="{{$_line_element->_id}}" />
        {{mb_field object=$_line_element field="ald" typeEnum="checkbox" onchange="submitFormAjax(this.form, 'systemMsg')"}}
        {{mb_label object=$_line_element field="ald" typeEnum="checkbox"}}
      </form>
    </td>
  </tr>
  </tbody>
  {{/foreach}}

  <!-- Parcours des commentaires --> 
  {{foreach from=$lines_element.comment item=_line_comment}}
  <tbody class="hoverable">
    <tr>
      <td style="width: 25px">
        <form name="delLineComment{{$element}}-{{$_line_comment->_id}}" action="" method="post">
          <input type="hidden" name="m" value="dPprescription" />
          <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
          <input type="hidden" name="del" value="1" />
          <input type="hidden" name="prescription_line_comment_id" value="{{$_line_comment->_id}}" />
          <button type="button" class="trash notext" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { Prescription.reload('{{$prescription->_id}}',null,'{{$element}}') } } );">
            {{tr}}Delete{{/tr}}
          </button>
        </form>
      </td>
      <td colspan="4">
        {{$_line_comment->commentaire}}
      </td>
      <td style="width: 25px">
        <form name="lineCommentALD{{$element}}-{{$_line_comment->_id}}" action="" method="post">
          <input type="hidden" name="m" value="dPprescription" />
          <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="prescription_line_comment_id" value="{{$_line_comment->_id}}" />
          {{mb_field object=$_line_comment field="ald" typeEnum="checkbox" onchange="submitFormAjax(this.form, 'systemMsg')"}}
          {{mb_label object=$_line_comment field="ald" typeEnum="checkbox"}}
        </form>
      </td>
    </tr>
  </tbody>
  {{/foreach}}
</table>
{{else}}
  <div class="big-info"> 
     Il n'y a aucun élément de type {{tr}}CCategoryPrescription.chapitre.{{$element}}{{/tr}} dans cette prescription.
  </div> 
{{/if}}
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