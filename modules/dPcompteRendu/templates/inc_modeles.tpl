<table class="tbl">
	<tr>
    <th>{{mb_colonne class=CCompteRendu field=nom          order_col=$order_col order_way=$order_way url="?m=dPcompteRendu&tab=vw_modeles"}}</th>
    <th>{{mb_colonne class=CCompteRendu field=object_class order_col=$order_col order_way=$order_way url="?m=dPcompteRendu&tab=vw_modeles"}}</th>
    <th>{{mb_colonne class=CCompteRendu field=type         order_col=$order_col order_way=$order_way url="?m=dPcompteRendu&tab=vw_modeles"}}</th>
	  <th>{{tr}}Action{{/tr}}</th>
	</tr>

	{{foreach from=$modeles item=_modele}}
	<tr>
	  <td>
      {{if $_modele->fast_edit_pdf}}
        <img style="float: right;" src="modules/dPcompteRendu/fcke_plugins/mbprintPDF/images/mbprintPDF.png"/>
      {{elseif $_modele->fast_edit}}
        <img style="float: right;" src="modules/dPcompteRendu/fcke_plugins/mbprinting/images/mbprinting.png"/>
      {{/if}}
      {{if $_modele->fast_edit || $_modele->fast_edit_pdf}}
        <img style="float: right;" src="images/icons/lightning.png"/>
      {{/if}}
	    <a href="?m={{$m}}&amp;tab=addedit_modeles&amp;compte_rendu_id={{$_modele->_id}}">
	   	  {{mb_value object=$_modele field=nom}}
	    </a>
	  </td>
	
	  <td>{{mb_value object=$_modele field=object_class}}</td>
	
	  <td>
      {{mb_value object=$_modele field=type}}
      <div class="compact">
        {{if $_modele->type == "body"}} 
          {{assign var=header value=$_modele->_ref_header}}
          {{if $header->_id}} 
            + 
            <span onmouseover="ObjectTooltip.createEx(this, '{{$header->_guid}}');">
              {{$header->nom}}
            </span>
          {{/if}}

          {{assign var=footer value=$_modele->_ref_footer}}
          {{if $footer->_id}} 
            + 
            <span onmouseover="ObjectTooltip.createEx(this, '{{$footer->_guid}}');">
              {{$footer->nom}}
            </span>
          {{/if}}
        {{/if}}
        
        {{if $_modele->type == "header"}} 
          {{assign var=count value=$_modele->_count.modeles_headed}}
          {{if $count}} 
            {{$_modele->_count.modeles_headed}} 
            {{tr}}CCompteRendu-back-modeles_headed{{/tr}}
          {{else}}
            <div class="empty">{{tr}}CCompteRendu-back-modeles_headed.empty{{/tr}}</div>
          {{/if}}
        {{/if}}
        
        {{if $_modele->type == "footer"}} 
          {{assign var=count value=$_modele->_count.modeles_footed}}
          {{if $count}} 
            {{$_modele->_count.modeles_footed}} 
            {{tr}}CCompteRendu-back-modeles_footed{{/tr}}
          {{else}}
            <div class="empty">{{tr}}CCompteRendu-back-modeles_footed.empty{{/tr}}</div>
          {{/if}}
        {{/if}}

      </div>
    </td>
	  
	  <td>
	    <form name="delete-{{$_modele->_guid}}" action="?m={{$m}}" method="post">
  	    <input type="hidden" name="m" value="{{$m}}" />
  	    <input type="hidden" name="del" value="1" />
  	    <input type="hidden" name="dosql" value="do_modele_aed" />
        <input type="hidden" name="_tab" value="_list" />
  	    {{mb_key object=$_modele}}
  	    <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le modèle',objName:'{{$_modele->nom|smarty:nodefaults|JSAttribute}}'})">
  	      {{tr}}Delete{{/tr}}
  	    </button>
	    </form>
	  </td>
	</tr>

	{{foreachelse}}
	<tr>
	  <td colspan="10" class="empty">{{tr}}CCompteRendu.none{{/tr}}</td>
	</tr>
  {{/foreach}}
</table>


