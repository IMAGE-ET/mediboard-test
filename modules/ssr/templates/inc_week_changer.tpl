{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
  * @package Mediboard
  * @subpackage ssr
  * @version $Revision: 7951 $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

<table style="width: 100%;">
  <tr>
    <td style="width: 12em; text-align: left;">
    	<button type="button" class="left" onclick="Planification.showWeek('{{$prev_week}}')">
    		Semaine précédente
			</button>
    </td>
		
		<td style="text-align: center; vertical-align: middle;">
      <big>
      	<strong>
      		{{tr}}Week{{/tr}} {{$planning->date|date_format:'%U'}},
					{{assign var=month_min value=$planning->date_min|date_format:'%B'}}
          {{assign var=month_max value=$planning->date_max|date_format:'%B'}}
					{{$month_min}}{{if $month_min != $month_max}}-{{$month_max}}{{/if}}
          {{$planning->date|date_format:'%Y'}}
				</strong>

	      <form name="DateSelect" action="?" method="get" onsubmit="return Planification.showWeek($V(this.date))">
	        <input type="hidden" name="m" value="{{$m}}" />
	        <script type="text/javascript">
	        Main.add(function () {
	          Calendar.regField(getForm("DateSelect").date, null, { noView: true} );
	        });
	        </script>
	
	        <input type="hidden" name="date" class="date" value="{{$planning->date}}" onchange="this.form.onsubmit()" />
	      </form>

			</big>
			
		</td>
		
    <td style="width: 12em; text-align: right;">
      <button type="button" class="right rtl" onclick="Planification.showWeek('{{$next_week}}')">
      	Semaine suivante
			</button>
    </td>
  </tr>
</table>