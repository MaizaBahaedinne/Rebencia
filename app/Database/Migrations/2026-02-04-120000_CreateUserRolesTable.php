<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserRolesTable extends Migration
{
    public function up()
    {
        // Create user_roles pivot table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'role_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'is_default' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 = rôle par défaut (utilisé au login)',
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 = rôle actif actuellement',
            ],
            'assigned_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'assigned_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'role_id'], false);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('user_roles');

        // Migrate existing data from users.role_id to user_roles
        $db = \Config\Database::connect();
        $users = $db->table('users')->get()->getResultArray();
        
        foreach ($users as $user) {
            if (!empty($user['role_id'])) {
                $db->table('user_roles')->insert([
                    'user_id' => $user['id'],
                    'role_id' => $user['role_id'],
                    'is_default' => 1, // Premier rôle = rôle par défaut
                    'is_active' => 1,
                    'assigned_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('user_roles');
    }
}
