<?php

use Plugins\ReceiptFE\Interaction;

echo '
<p>'.tr('Le ricevute delle Fatture Elettroniche permettono di individuare se una determinata fattura tramessa è stata accettata dal Sistema Di Interscambio').'.</p>';
if (Interaction::isEnabled()) {
    echo '
<p>'.tr('Tramite il pulsante _BTN_ è possibile procedere al recupero delle ricevute, aggiornando automaticamente lo stato delle relative fatture e allegandole ad esse', [
    '_BTN_' => '<b>'.tr('Ricerca ricevute').'</b>',
]).'.</p>';
}
echo '
<div class="card card-outline card-success">
    <div class="card-header">
        <h3 class="card-title">
            '.tr('Carica un XML').'

            <span class="tip" title="'.tr('Formati supportati: XML e P7M').'.">
                <i class="fa fa-question-circle-o"></i>
            </span>

        </h3>
    </div>
    <div class="card-body" id="upload">
        <div class="row">
            <div class="col-md-9">
                {[ "type": "file", "name": "blob", "required": 1 ]}
            </div>

            <div class="col-md-3">
                <button type="button" class="btn btn-primary float-right" onclick="upload(this)">
                    <i class="fa fa-upload"></i> '.tr('Carica ricevuta').'
                </button>
            </div>
        </div>
    </div>
</div>';

echo '
<div class="card card-outline card-info">
    <div class="card-header">
        <h3 class="card-title">
            '.tr('Ricevute da importare').'</span>
        </h3>';

// Ricerca automatica
if (Interaction::isEnabled()) {
    echo '
        <div class="float-right">
            <button type="button" class="btn btn-warning" onclick="importAll(this)">
                <i class="fa fa-cloud-download"></i> '.tr('Importa tutte le ricevute').'
            </button>

            <button type="button" class="btn btn-primary" onclick="search(this)">
                <i class="fa fa-refresh"></i> '.tr('Ricerca ricevute').'
            </button>
        </div>';
}

echo '
    </div>
    <div class="card-body" id="list">';

if (Interaction::isEnabled()) {
    echo '
        <p>'.tr('Per vedere le ricevute da importare utilizza il pulsante _BUTTON_', [
            '_BUTTON_' => '<b>"'.tr('Ricerca ricevute').'"</b>',
        ]).'.</p>';
} else {
    include __DIR__.'/list.php';
}

    echo '

    </div>
</div>';

echo '
<script>
function search(button) {
    var restore = buttonLoading(button);

    $("#list").load("'.fileurl('list.php').'?id_module='.$id_module.'&id_plugin='.$id_plugin.'", function() {
        buttonRestore(button, restore);
    });
}
function upload(btn) {
    if ($("#blob").val()) {
        var restore = buttonLoading(btn);

        $("#upload").ajaxSubmit({
            url: globals.rootdir + "/actions.php",
            data: {
                op: "save",
                id_module: "'.$id_module.'",
                id_plugin: "'.$id_plugin.'",
            },
            type: "post",
            success: function(data){
                importMessage(data);

                buttonRestore(btn, restore);
            },
            error: function(xhr, error, thrown) {
                ajaxError(xhr, error, thrown);

                buttonRestore(btn, restore);
            }
        });
    } else {
        Swal.fire({
            title: "'.tr('Selezionare un file!').'",
            type: "error",
        })
    }
}

function importMessage(data) {
    data = JSON.parse(data);

    var ricevuta = "<br>'.tr('Ricevuta').': " + data.file;

    if(data.fattura) {
        Swal.fire({
            title: "'.tr('Importazione completata!').'",
            html: "'.tr('Fattura aggiornata correttamente').':" + data.fattura + ricevuta,
            type: "success",
        });
    } else {
        Swal.fire({
            title: "'.tr('Importazione fallita!').'",
            html: "<i>'.tr('Fattura relativa alla ricevuta non rilevata. Controlla che esista una fattura di vendita corrispondente caricata a gestionale.').'</i>" + ricevuta,
            type: "error",
        });
    }
}

function importAll(btn) {
    Swal.fire({
        title: "'.tr('Importare tutte le ricevute?').'",
        html: "'.tr('Importando le ricevute, verranno aggiornati gli stati di invio delle fatture elettroniche. Continuare?').'",
        showCancelButton: true,
        confirmButtonText: "'.tr('Procedi').'",
        type: "info",
    }).then(function (result) {
        var restore = buttonLoading(btn);

        $.ajax({
            url: globals.rootdir + "/actions.php",
            data: {
                op: "import",
                id_module: "'.$id_module.'",
                id_plugin: "'.$id_plugin.'",
            },
            type: "post",
            success: function(data){
                data = JSON.parse(data);

                if(data.length == 0){
                    var html = "'.tr('Non sono state trovate ricevute da importare').'.";
                } else {
                    var html = "'.tr('Sono state elaborate le seguenti ricevute:').'";

                    data.forEach(function(element) {
                        var text = "";
                        if(element.fattura) {
                            text += element.fattura;
                        } else {
                            text += "<i>'.tr('Fattura relativa alla ricevuta non rilevata. Controlla che esista una fattura di vendita corrispondente caricata a gestionale.').'</i>";
                        }

                        text += " (" + element.file + ")";

                        html += "<small><li>" + text + "</li></small>";
                    });

                    html += "<br><small>'.tr("Se si sono verificati degli errori durante la procedura e il problema continua a verificarsi, contatta l'assistenza ufficiale").'</small>";
                }

                Swal.fire({
                    title: "'.tr('Operazione completata!').'",
                    html: html,
                    type: "info",
                })

                $("#list").load("'.fileurl('list.php').'?id_module='.$id_module.'&id_plugin='.$id_plugin.'", function() {
                    buttonRestore(button, restore);
                });

            },
            error: function(xhr, error, thrown) {
                ajaxError(xhr, error, thrown);

                buttonRestore(btn, restore);
            }
        });
    });
}
</script>';