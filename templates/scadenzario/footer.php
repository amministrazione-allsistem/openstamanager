<?php

echo '
<table style="color:#aaa; font-size:10px;">
<tr>
    <td align="left" style="width:97mm;">
        '.tr('Stampato con OpenSTAManager il _DATE_', ['_DATE_' => date('d/m/Y')]).'
    </td>

    <td class="text-right" style="width:97mm;">
        '.tr('Pagina _PAGE_ di _TOTAL_', [
        '_PAGE_' => '{PAGENO}',
        '_TOTAL_' => '{nb}',
    ]).'
    </td>
</tr>
</table>';
