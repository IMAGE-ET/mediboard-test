<!-- $Id: print_compta.tpl 2031 2007-06-08 09:13:23Z lryo $ -->

{{include file="../../dPpatients/templates/inc_intermax.tpl"}}

<table class="main">
  <tr>
    <td class="halfPane">
      <table>
        <tr>
          <th colspan="2">
            <a href="#" onclick="window.print()">
              Rapport du {{$filter->_date_min|date_format:"%d/%m/%Y"}}
              {{if $filter->_date_min != $filter->_date_max}}
              au {{$filter->_date_max|date_format:"%d/%m/%Y"}}
              {{/if}}
            </a>
          </th>
        </tr>
        {{if $prat->user_id}}
        <tr><th colspan="2">Dr. {{$prat->_view}}</th></tr>
        {{/if}}
        <tr>
          <td>{{mb_label object=$filter field=S_FSE_ETAT}}</td>
          <td>{{mb_value object=$filter field=S_FSE_ETAT}}</td>
        </tr>
      </table>
    </td>

    <td class="halfPane">
      <table class="form">
        <tr><th class="category" colspan="2">Récapitulatif</th></tr>
        <tr><th>Nombre FSE :</th><td>{{$total.count}}</td></tr>
        <tr><th>Total facturé :</th><td>{{$total.S_FSE_TOTAL_FACTURE|string_format:"%.2f"}}&euro;</td></tr>
      </table>
    </td>
  </tr>

  {{foreach from=$days key=_day item=_fses}}
  <tr>
    <td coslpan="2"><strong>{{$_day|date_format:"%a %d %b %Y"}}</strong></td>
  </tr>
  <tr>
    <td colspan="2">
      <table class="tbl">
        <tr>
          <th width="15%">{{mb_label object=$filter field=S_FSE_NUMERO_FSE}}</th>
          <th width="15%">{{mb_label object=$filter field=S_FSE_MODE_SECURISATION}}</th>
          <th width="15%">{{mb_label object=$filter field=S_FSE_ETAT}}</th>
          <th width="15%">{{mb_label object=$filter field=S_FSE_TOTAL_FACTURE}}</th>
        </tr>
        {{foreach from=$_fses item=_fse}}
        <tr>
          <td>
            <button class="search" type="button" onclick="Intermax.Triggers['Consulter FSE']('{{$_fse->_id}}');">
			        {{mb_value object=$_fse field=S_FSE_NUMERO_FSE}} 
			      </button>
          <td>{{mb_value object=$_fse field=S_FSE_MODE_SECURISATION}}</td>
          {{if $_fse->_annulee}}
          <td colspan="2" class="cancelled">{{mb_value object=$_fse field=S_FSE_ETAT}}</td>
          {{else}}
          <td>{{mb_value object=$_fse field=S_FSE_ETAT}}</td>
          <td>{{mb_value object=$_fse field=S_FSE_TOTAL_FACTURE}}</td>
          {{/if}}
        </tr>
        {{/foreach}}
        <tr>
          <td colspan="3" style="font-weight:bold; text-align:right">Total pour {{$cumuls.$_day.count}} FSE</td>
          <td style="font-weight:bold;">{{$cumuls.$_day.S_FSE_TOTAL_FACTURE|string_format:"%.2f"}}&euro;</td>
        </tr>
      </table>
    </td>
  </tr>
  {{/foreach}}
</table>
      