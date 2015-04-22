{{*
 * $Id$
 *  
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<tr class="clear">
  <th colspan="{{$_materiel+$_extra+$_duree+$_coordonnees+12}}">
    <h1 style="margin: auto;">
      <a href="#" onclick="window.print()">
        Planning du {{$filter->_datetime_min|date_format:$conf.date}} {{$filter->_datetime_min|date_format:$conf.time}}
        au {{$filter->_datetime_max|date_format:$conf.date}} {{$filter->_datetime_max|date_format:$conf.time}}
        -
        {{$numOp}} intervention(s)
        {{if $operations|@count && $_hors_plage}}
          (dont {{$operations|@count}} hors plage)
        {{/if}}
      </a>
    </h1>
  </th>
</tr>