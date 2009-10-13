<!-- $Id: print_bilan_lot.tpl 6603 2009-07-07 09:49:44Z mytto $ -->

<table class="main">
  <tr>
    <td class="halfPane">
      <table class="form">
        <tr>
          <th colspan="2" class="category">
            <a href="#Print" onclick="window.print()">
              Rapport du {{$lot->_date_min|date_format:$dPconfig.date}}
              {{if $lot->_date_min != $lot->_date_max}}
              au {{$lot->_date_max|date_format:$dPconfig.date}}
              {{/if}}
            </a>
          </th>
        </tr>
				
        {{if $prat->_id}}
        <tr>
          <th>{{mb_label object=$prat field=_user_last_name}}</th>
          <td>{{mb_value object=$prat field=_view}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$prat field=adeli}}</th>
          <td>{{mb_value object=$prat field=adeli}}</td>
        </tr>
        {{/if}}
        <tr>
          <th>{{mb_label object=$lot field=S_LOT_ETAT}}</th>
          <td>{{mb_value object=$lot field=S_LOT_ETAT}}</td>
        </tr>
      </table>
    </td>

    <td class="halfPane">
      <table class="form">
        <tr>
        	<th class="category" colspan="2">Récapitulatif</th>
				</tr>
        <tr>
          <th>Nombre {{tr}}CLmFichier{{/tr}}</th>
          <td>{{$fichiers|@count}}</td>
				</tr>
				<tr>
          <th>Nombre {{tr}}CLmLot{{/tr}}</th>
          <td>{{$lots|@count}}</td>
				</tr>
      </table>
    </td>
  </tr>

  {{foreach from=$days key=_day item=_fichiers}}
  <tr>
    <td colspan="2"><strong>{{$_day|date_format:$dPconfig.longdate}}</strong></td>
  </tr>
	
  <tr>
    <td colspan="2">
      <table class="tbl">
        <tr>
          <th width="15%">{{mb_label object=$lot field=S_LOT_NUMERO}}</th>
          <th width="15%">{{mb_title object=$lot field=S_LOT_ETAT}}</th>
          <th width="05%">{{mb_title object=$lot field=S_LOT_DATE}}</th>
          <th width="05%">{{mb_title object=$lot field=S_LOT_NB_TRANS}}</th>
          <th width="05%">{{mb_title object=$fichier field=S_FIC_DATE_EMISSION}}</th>
          <th width="05%">{{tr}}Action{{/tr}}</th>
        </tr>
				
        {{foreach from=$_fichiers item=_fichier}}
				<tbody class="hoverable">
					
        <tr style="font-weight: bold;">
        	<td>
        	  <span class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$_fichier->_guid}}')">
        	  	{{$_fichier}}
						</span>
					</td>

          <td>{{mb_value object=$_fichier field=S_FIC_ETAT}}</td>
          <td></td>
          <td>{{mb_value object=$_fichier field=S_FIC_NB_TRANS}}</td>
          <td>{{mb_value object=$_fichier field=S_FIC_DATE_EMISSION}}</td>

					{{assign var=lots_count value=$_fichier->_back.lots|@count}}
					<td class="button" rowspan="{{$lots_count+1}}">
						{{if $_fichier->_resend_fixable}}
						<form name="Fix-{{$_fichier->_guid}}" method="post" onsubmit="return checkForm(this)"
						   action="?m={{$m}}&amp;{{$actionType}}={{$action}}&amp;dialog={{$dialog}}">
						  <input type="hidden" name="m" value="{{$m}}" />
              <input type="hidden" name="dosql" value="do_lmfichier_aed" />
              <input type="hidden" name="del" value="0" />
							{{mb_key object=$_fichier}}							
              <input type="hidden" name="_fix_resend" value="1" />
							
							<button class="change" type="submit">{{tr}}Fix{{/tr}}</button>
						</form>
						{{/if}}
					</td>
        </tr>
				
        {{foreach from=$_fichier->_back.lots item=_lot}}
        <tr>
          <td style="padding-left: 2em;">
				  	<span class="tooltip-trigger" onmouseover="ObjectTooltip.createEx(this, '{{$_lot->_guid}}')">
				  	  {{$_lot}}
				  	</span>
			    </td>
			      
          <td>{{mb_value object=$_lot field=S_LOT_ETAT}}</td>
          <td>{{mb_value object=$_lot field=S_LOT_DATE}}</td>
          <td>{{mb_value object=$_lot field=S_LOT_NB_TRANS}}</td>
          <td></td>
        </tr>
        {{/foreach}}
				
				</tbody>
        {{/foreach}}
			</table>
    </td>
  </tr>
  {{/foreach}}
</table>
      