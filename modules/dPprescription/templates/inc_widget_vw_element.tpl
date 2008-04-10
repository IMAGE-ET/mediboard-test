{{assign var=nb_lines_element value=0}}
{{assign var=nb_lines_comment value=0}}
{{foreach from=$prescription->_ref_lines_elements_comments.$element key=name item=elementsCat}}
  {{assign var=lines_element value=$elementsCat.element|@count}}
  {{assign var=lines_comment value=$elementsCat.comment|@count}}
  {{assign var=nb_lines_element value=$nb_lines_element+$lines_element}}  
  {{assign var=nb_lines_comment value=$nb_lines_comment+$lines_comment}}
{{/foreach}}
{{assign var=nb_element value=$nb_lines_element+$nb_lines_comment}}
<td>
  {{$nb_element}}
</td>
