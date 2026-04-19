<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTasksTable extends Migration
{
    public function up(): void
    {
        // --------------------------------------------------------
        // Table tasks
        // --------------------------------------------------------
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],

            'reference'   => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'title'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'TEXT',    'null' => true],

            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['bug','feature','improvement','task','question'],
                'default'    => 'task',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['backlog','todo','in_progress','review','done','cancelled'],
                'default'    => 'todo',
            ],
            'priority' => [
                'type'       => 'ENUM',
                'constraint' => ['low','medium','high','critical'],
                'default'    => 'medium',
            ],

            'created_by'  => ['type' => 'INT', 'unsigned' => true],
            'assigned_to' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'due_date'    => ['type' => 'DATE', 'null' => true],
            'labels'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],

            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->addKey('priority');
        $this->forge->addKey('assigned_to');
        $this->forge->addForeignKey('created_by',  'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('assigned_to', 'users', 'id', 'SET NULL',  'CASCADE');
        $this->forge->createTable('tasks', true);

        // --------------------------------------------------------
        // Table task_comments
        // --------------------------------------------------------
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'task_id'    => ['type' => 'INT', 'unsigned' => true],
            'user_id'    => ['type' => 'INT', 'unsigned' => true],
            'content'    => ['type' => 'TEXT'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('task_id');
        $this->forge->addForeignKey('task_id', 'tasks', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('task_comments', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('task_comments', true);
        $this->forge->dropTable('tasks', true);
    }
}
