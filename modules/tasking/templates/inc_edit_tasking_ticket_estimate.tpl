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

<th class="narrow">{{mb_label object=$tasking_ticket field=estimate}} ({{tr}}hours{{/tr}})</th>
<td colspan="4">
  <label>
    <input type="radio" name="estimate" value="0" {{if !$tasking_ticket->estimate}}checked="checked"{{/if}} />
    0
  </label>
  <label>
    <input type="radio" name="estimate" value="1" {{if $tasking_ticket->estimate == 1}}checked="checked"{{/if}} />
    1
  </label>
  <label>
    <input type="radio" name="estimate" value="2" {{if $tasking_ticket->estimate == 2}}checked="checked"{{/if}} />
    2
  </label>
  <label>
    <input type="radio" name="estimate" value="3" {{if $tasking_ticket->estimate == 3}}checked="checked"{{/if}} />
    3
  </label>
  <label>
    <input type="radio" name="estimate" value="4" {{if $tasking_ticket->estimate == 4}}checked="checked"{{/if}} />
    4
  </label>
  <label>
    <input type="radio" name="estimate" value="6" {{if $tasking_ticket->estimate == 6}}checked="checked"{{/if}} />
    6
  </label>
  <label>
    <input type="radio" name="estimate" value="8" {{if $tasking_ticket->estimate == 8}}checked="checked"{{/if}} />
    8
  </label>
  <label>
    <input type="radio" name="estimate" value="12" {{if $tasking_ticket->estimate == 12}}checked="checked"{{/if}} />
    12
  </label>
  <label>
    <input type="radio" name="estimate" value="16" {{if $tasking_ticket->estimate == 16}}checked="checked"{{/if}} />
    16
  </label>
  <label>
    <input type="radio" name="estimate" value="24" {{if $tasking_ticket->estimate == 24}}checked="checked"{{/if}} />
    24
  </label>
  <label>
    <input type="radio" name="estimate" value="32" {{if $tasking_ticket->estimate == 32}}checked="checked"{{/if}} />
    32
  </label>
  &mdash;
  <label>
    Autre :
    <input type="text" name="_other_estimate" style="width: 5em;"
           value="{{if !preg_match("/^(0|1|2|3|4|6|8|12|16|24|32)\s*h$/", $tasking_ticket->estimate)}}{{$tasking_ticket->estimate|regex_replace:"/\s*h/":""}}{{/if}}" /> h
  </label>
</td>