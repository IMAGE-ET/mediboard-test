<div>
  <span onmouseover="ObjectTooltip.createEx(this, '{{$acte->_guid}}');">
    {{$acte->_shortview}}
  </span>
  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$acte->_ref_executant}}
</div>
