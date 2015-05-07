<hr />

{{mb_script module=sante400 script=hyperTextLink ajax=true}}

{{foreach from=$object->_ref_hypertext_links item=_hypertext_link}}
  <tr>
    <td>
      <a href="{{$_hypertext_link->link}}" target="_blank"> {{$_hypertext_link->name}} <i class="fa fa-external-link"></i> </a>
    </td>
  </tr>
{{/foreach}}