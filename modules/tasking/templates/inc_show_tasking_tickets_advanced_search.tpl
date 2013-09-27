{{*
 * $Id$
 *  
 * @category Tasking
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<tr {{if $toggle == 0}}style="display: none;"{{/if}}>
  <th>{{mb_label object=$ticket field=priority}}</th>
  <td>
    {{mb_field object=$ticket field=_priority onchange="\$V(this.form.elements.start, 0)"}}
  </td>

  <th>{{mb_label object=$ticket field=funding}}</th>
  <td>
    {{mb_field object=$ticket field=_funding onchange="\$V(this.form.elements.start, 0)"}}
  </td>
</tr>

<tr {{if $toggle == 0}}style="display: none;"{{/if}}>
  <th></th>
  <td colspan="3">
    <fieldset style="display: inline-block;">
      <legend>{{mb_label object=$ticket field=estimate}}</legend>
      <select name="select_estimate" onchange="$V(this.form.elements.start, 0)">
        <option value="=" {{if $select_estimate == "="}}selected="selected"{{/if}}>=</option>
        <option value="<" {{if $select_estimate == "<"}}selected="selected"{{/if}}><</option>
        <option value="<=" {{if $select_estimate == "<="}}selected="selected"{{/if}}><=</option>
        <option value=">" {{if $select_estimate == ">"}}selected="selected"{{/if}}>></option>
        <option value=">=" {{if $select_estimate == ">="}}selected="selected"{{/if}}>>=</option>
        <option value="!=" {{if $select_estimate == "!="}}selected="selected"{{/if}}>!=</option>
      </select>
      {{mb_field object=$ticket field=estimate increment=true form="search-tickets" onchange="\$V(this.form.elements.start, 0)"}}
    </fieldset>

    <fieldset style="display: inline-block;">
      <legend class="creation_date">{{mb_label object=$ticket field=creation_date}}</legend>
      {{mb_field object=$ticket field=_creation_date_min register=true form="search-tickets" prop=dateTime onchange="\$V(this.form.elements.start, 0)"}}
      <b>&raquo;</b>
      {{mb_field object=$ticket field=_creation_date_max register=true form="search-tickets" prop=dateTime onchange="\$V(this.form.elements.start, 0)"}}
    </fieldset>

    <fieldset style="display: inline-block;">
      <legend class="due_date">{{mb_label object=$ticket field=due_date}}</legend>
      {{mb_field object=$ticket field=_due_date_min register=true form="search-tickets" prop=dateTime onchange="\$V(this.form.elements.start, 0)"}}
      <b>&raquo;</b>
      {{mb_field object=$ticket field=_due_date_max register=true form="search-tickets" prop=dateTime onchange="\$V(this.form.elements.start, 0)"}}
    </fieldset>

    <fieldset style="display: inline-block;">
      <legend class="closing_date">{{mb_label object=$ticket field=closing_date}}</legend>
      {{mb_field object=$ticket field=_closing_date_min register=true form="search-tickets" prop=dateTime onchange="\$V(this.form.elements.start, 0)"}}
      <b>&raquo;</b>
      {{mb_field object=$ticket field=_closing_date_max register=true form="search-tickets" prop=dateTime onchange="\$V(this.form.elements.start, 0)"}}
    </fieldset>
  </td>
</tr>