<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePropertyCharacteristicsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],

            // Identifiant technique unique (ex: "swimming_pool", "has_elevator")
            'key'        => ['type' => 'VARCHAR', 'constraint' => 80],
            'label'      => ['type' => 'VARCHAR', 'constraint' => 150],

            // Icône Bootstrap Icons (ex: "bi-water", "bi-elevator")
            'icon'       => ['type' => 'VARCHAR', 'constraint' => 60, 'default' => 'bi-check-circle'],

            // Type de saisie
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['boolean', 'number', 'text', 'select'],
                'default'    => 'boolean',
            ],

            // Unité affichée pour les nombres (m², km², années…)
            'unit'       => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],

            // Options JSON pour type=select (ex: ["Individuel","Collectif","Mixte"])
            'options'    => ['type' => 'JSON', 'null' => true],

            // Types de bien concernés (JSON array ou null = tous)
            // ex: ["apartment","house","villa"]
            'applies_to' => ['type' => 'JSON', 'null' => true],

            // Types pour lesquels c'est obligatoire (JSON array ou null = jamais obligatoire)
            'required_for' => ['type' => 'JSON', 'null' => true],

            // Ordre d'affichage
            'sort_order' => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 100],

            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('key');
        $this->forge->addKey('sort_order');
        $this->forge->createTable('property_characteristics', true);

        // Données initiales
        $now = date('Y-m-d H:i:s');
        $rows = [
            // Équipements communs
            ['key' => 'has_elevator',    'label' => 'Ascenseur',       'icon' => 'bi-arrow-up-square',     'type' => 'boolean', 'sort_order' => 10],
            ['key' => 'has_parking',     'label' => 'Parking',         'icon' => 'bi-p-square',             'type' => 'boolean', 'sort_order' => 11],
            ['key' => 'has_garage',      'label' => 'Garage',          'icon' => 'bi-car-front',            'type' => 'boolean', 'sort_order' => 12],
            ['key' => 'has_garden',      'label' => 'Jardin',          'icon' => 'bi-tree',                 'type' => 'boolean', 'sort_order' => 13],
            ['key' => 'has_pool',        'label' => 'Piscine',         'icon' => 'bi-water',                'type' => 'boolean', 'sort_order' => 14, 'applies_to' => json_encode(['villa','house'])],
            ['key' => 'has_terrace',     'label' => 'Terrasse',        'icon' => 'bi-sun',                  'type' => 'boolean', 'sort_order' => 15],
            ['key' => 'has_balcony',     'label' => 'Balcon',          'icon' => 'bi-columns-gap',          'type' => 'boolean', 'sort_order' => 16, 'applies_to' => json_encode(['apartment'])],
            ['key' => 'has_storage',     'label' => 'Cave / Cellier',  'icon' => 'bi-box',                  'type' => 'boolean', 'sort_order' => 17],
            ['key' => 'has_security',    'label' => 'Sécurité / Gardien','icon' => 'bi-shield-check',       'type' => 'boolean', 'sort_order' => 18],
            ['key' => 'has_concierge',   'label' => 'Concierge',       'icon' => 'bi-person-badge',         'type' => 'boolean', 'sort_order' => 19],
            ['key' => 'has_ac',          'label' => 'Climatisation',   'icon' => 'bi-thermometer-snow',     'type' => 'boolean', 'sort_order' => 20],
            ['key' => 'has_heating',     'label' => 'Chauffage central','icon' => 'bi-thermometer-sun',     'type' => 'boolean', 'sort_order' => 21],
            ['key' => 'has_solar',       'label' => 'Panneaux solaires','icon' => 'bi-sun-fill',            'type' => 'boolean', 'sort_order' => 22],
            ['key' => 'has_fiber',       'label' => 'Fibre optique',   'icon' => 'bi-wifi',                 'type' => 'boolean', 'sort_order' => 23],
            ['key' => 'has_sea_view',    'label' => 'Vue mer',         'icon' => 'bi-binoculars',           'type' => 'boolean', 'sort_order' => 30],
            // Numérique
            ['key' => 'land_area',       'label' => 'Surface terrain', 'icon' => 'bi-rulers',               'type' => 'number',  'unit' => 'm²',  'sort_order' => 40, 'applies_to' => json_encode(['villa','house','land'])],
            ['key' => 'age',             'label' => 'Année de construction','icon' => 'bi-calendar3',       'type' => 'number',  'unit' => '',    'sort_order' => 41],
            ['key' => 'parking_spaces',  'label' => 'Nb places parking','icon' => 'bi-p-circle',            'type' => 'number',  'unit' => '',    'sort_order' => 42],
            // Texte  / select
            ['key' => 'orientation',     'label' => 'Orientation',     'icon' => 'bi-compass',              'type' => 'select',  'sort_order' => 50, 'options' => json_encode(['Nord','Sud','Est','Ouest','Nord-Est','Nord-Ouest','Sud-Est','Sud-Ouest'])],
            ['key' => 'condition',       'label' => 'État du bien',    'icon' => 'bi-stars',                'type' => 'select',  'sort_order' => 51, 'options' => json_encode(['Neuf','Excellent','Bon état','À rénover','En construction'])],
            ['key' => 'heating_type',    'label' => 'Type de chauffage','icon' => 'bi-fire',                'type' => 'select',  'sort_order' => 52, 'options' => json_encode(['Individuel gaz','Collectif','Électrique','Climatisation réversible','Autre'])],
        ];

        foreach ($rows as $row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
            if (!isset($row['applies_to']))   $row['applies_to']   = null;
            if (!isset($row['required_for'])) $row['required_for'] = null;
            if (!isset($row['unit']))         $row['unit']         = null;
            if (!isset($row['options']))      $row['options']      = null;
            $this->db->table('property_characteristics')->insert($row);
        }
    }

    public function down(): void
    {
        $this->forge->dropTable('property_characteristics', true);
    }
}
