<?php

namespace API\App\v1;

use API\App\AppResource;

class Articoli extends AppResource
{
    public function getCleanupData($last_sync_at)
    {
        return $this->getDeleted('mg_articoli', 'id', $last_sync_at);
    }

    public function getModifiedRecords($last_sync_at)
    {
        $query = 'SELECT mg_articoli.id FROM mg_articoli WHERE deleted_at IS NULL';

        // Filtro per data
        if ($last_sync_at) {
            $query .= ' AND mg_articoli.updated_at > '.prepare($last_sync_at);
        }

        $records = database()->fetchArray($query);

        return array_column($records, 'id');
    }

    public function retrieveRecord($id)
    {
        // Gestione della visualizzazione dei dettagli del record
        $query = 'SELECT mg_articoli.id AS id,
            mg_articoli.codice,
            mg_articoli.descrizione,
            mg_articoli.prezzo_vendita,
            mg_articoli.prezzo_acquisto,
            mg_articoli.qta,
            mg_articoli.um,
            (SELECT nome FROM mg_categorie WHERE id = mg_articoli.id_categoria) AS categoria,
            (SELECT nome FROM mg_categorie WHERE id = mg_articoli.id_sottocategoria) AS sottocategoria
        FROM mg_articoli
        WHERE mg_articoli.id = '.prepare($id);

        $record = database()->fetchOne($query);

        return $record;
    }
}
