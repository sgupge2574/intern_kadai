<?php

namespace Fuel\Migrations;

use Fuel\Core\DBUtil;


class Create_users_projects_tasks
{
	public function up()
	{
		// users テーブル
		DBUtil::create_table('users', array(
			'id' => array('type' => 'int', 'auto_increment' => true),
			'username' => array('type' => 'varchar', 'constraint' => 255),
			'password' => array('type' => 'varchar', 'constraint' => 255),
			'created_at' => array('type' => 'datetime', 'default' => 'CURRENT_TIMESTAMP'),
            'updated_at' => array('type' => 'datetime', 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
		), array('id'));

		DBUtil::create_index('users', array('username'), 'username_unique', 'UNIQUE');

		// projects テーブル
		DBUtil::create_table('projects', array(
			'id' => array('type' => 'int', 'auto_increment' => true),
			'user_id' => array('type' => 'int'),
			'name' => array('type' => 'varchar', 'constraint' => 255),
			'created_at' => array('type' => 'datetime', 'default' => 'CURRENT_TIMESTAMP'),
	        'updated_at' => array('type' => 'datetime', 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
		), array('id'));

		DBUtil::create_foreign_key('projects', array(
			'key' => 'user_id',
			'reference' => array(
				'table' => 'users',
				'column' => 'id'
			)
		));

		// tasks テーブル
		DBUtil::create_table('tasks', array(
			'id' => array('type' => 'int', 'auto_increment' => true),
			'project_id' => array('type' => 'int'),
			'name' => array('type' => 'varchar', 'constraint' => 255),
			'due_date' => array('type' => 'date', 'null' => true),
			'status' => array('type' => 'tinyint', 'constraint' => 1, 'default' => 0),
			'created_at' => array('type' => 'datetime', 'default' => DBUtil::expr('CURRENT_TIMESTAMP')),
			'updated_at' => array('type' => 'datetime', 'default' => DBUtil::expr('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')),
		), array('id'));

		DBUtil::create_foreign_key('tasks', array(
			'key' => 'project_id',
			'reference' => array(
				'table' => 'projects',
				'column' => 'id'
			)
		));
	}

	public function down()
	{
		DBUtil::drop_table('tasks');
		DBUtil::drop_table('projects');
		DBUtil::drop_table('users');
	}
}