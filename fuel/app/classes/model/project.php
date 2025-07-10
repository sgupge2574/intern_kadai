<?php
class Model_Project extends \Orm\Model
{
    protected static $_table_name = 'projects';

    protected static $_properties = array(
        'id',
        'user_id',
        'name',
        'created_at',
        'updated_at',
    );

    protected static $_belongs_to = array(
        'user' => array(
            'model_to' => 'Model_User',
            'key_from' => 'user_id',
            'key_to' => 'id',
        ),
    );

    protected static $_has_many = array(
        'tasks' => array(
            'model_to' => 'Model_Task',
            'key_from' => 'id',
            'key_to' => 'project_id',
        ),
    );
}
