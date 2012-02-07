<script type="text/javascript">
  toggleCheck = function(chap, checked) {
    $("area_selected_lines").select(".input_"+chap).each(function(elt) {
      elt.checked = checked ? "checked" : "";
    });
  }
</script>
{{assign var=numCols value=2}}
<table class="form">
  <tr>
    <th class="title" colspan="{{$numCols}}">
      <button type="button" class="cancel notext" onclick="Control.Modal.close()" style="float: right;">{{tr}}Close{{/tr}}</button>
      Impression partielle
    </th>
  </tr>
  {{foreach from=$all_lines item=_lines_by_chap name=chaps}}
     {{foreach from=$_lines_by_chap item=_line name="lines"}}
       {{if $_line instanceof CPrescriptionLineElement}}
         {{assign var=name_chap value=$_line->_chapitre}}
       {{else}}
         {{assign var=name_chap value="med"}}
       {{/if}}
       {{if $smarty.foreach.lines.first}}
         <tr>
           <th class="category" colspan="{{$numCols}}">
             <label>
               {{if $_line instanceof CPrescriptionLineElement}}
                 {{tr}}CCategoryPrescription.chapitre.{{$_line->_chapitre}}{{/tr}}
               {{else}}
                 Médicaments
               {{/if}}
               <input type="checkbox" name="check_{{$name_chap}}"
                 onclick="toggleCheck('{{$name_chap}}', this.checked) "/>
             </label>
           </th>
         </tr>
         <tr>
       {{/if}}
       
       {{assign var=i value=$smarty.foreach.lines.iteration}}
       <td class="text">
         <label>
           <input type="checkbox" class="input_{{$name_chap}}"
             name="selected_lines[{{$_line->_guid}}]" value="{{$_line->_guid}}" /> {{$_line->_view}}
         </label>
       </td>
       {{if (($i % $numCols) == 0)}}</tr>
         {{if !$smarty.foreach.lines.last && !$smarty.foreach.chaps.last}}
           <tr>
         {{/if}}
       {{/if}}
       
    {{/foreach}}
  {{/foreach}}
</table>
<div class="button">
  <button type="button" class="tick" onclick="hideIframe(); this.form.submit();">{{tr}}Validate{{/tr}}</button>
</div>