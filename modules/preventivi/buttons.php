<?php

include_once __DIR__.'/../../core.php';

if (!in_array($record['stato'], ['Bozza', 'Rifiutato', 'In attesa di conferma'])) {
    echo '
	<div class="dropdown">
	<button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
		<i class="fa fa-magic"></i>&nbsp;'.tr('Crea').'...
		<span class="caret"></span>
	</button>
	<ul class="dropdown-menu dropdown-menu-right">

			<li>
				<a data-href="'.$rootdir.'/modules/ordini/crea_documento.php?id_module='.$id_module.'&id_record='.$id_record.'&documento=ordine" data-toggle="modal" data-title="'.tr('Crea ordine').'" data-target="#bs-popup"><i class="fa fa-file-o"></i>&nbsp;'.tr('Ordine').'
				</a>
			</li>

		</ul>
	</div>';
} else {
    echo '
    <form action="" method="post" id="form_crearevisione">
        <input type="hidden" name="backto" value="record-edit">
        <input type="hidden" name="op" value="add_revision">
        <input type="hidden" name="id_record" value="'.$id_record.'">
        
        <button type="button" class="btn btn-warning" onclick="if(confirm(\'Vuoi creare un nuova revisione?\')){$(\'#form_crearevisione\').submit();}"><i class="fa fa-copy"></i> Crea nuova revisione...</button>
    </form>';
}
