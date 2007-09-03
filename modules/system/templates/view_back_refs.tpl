{{* $Id: $ *}}

<table class="tbl">

<tr>
{{foreach from=$objects item=_object}}
  <th class="title">{{$_object->_view}}</th>
{{/foreach}}
</tr>

{{foreach from=$object->_back key=backName item=backObjects}}
{{assign var=backSpec value=$object->_backSpecs.$backName}}
<tr>
  <th class="category" colspan="{{$objects|@count}}">
    {{tr}}{{$backSpec->_initiator}}-back-{{$backName}}{{/tr}}	
  </th>
</tr>

<tr>
{{foreach from=$objects item=_object}}
  <td>
	{{foreach from=$_object->_back.$backName item=backRef}}
    <div onmouseover="ObjectTooltip.create(this, '{{$backRef->_class_name}}', '{{$backRef->_id}}')">
      {{$backRef->_view}}
    </div>
	{{foreachelse}}
	<em>Aucun objet</em>
	{{/foreach}}
  </td>
{{/foreach}}
</tr>

{{/foreach}}



</table>