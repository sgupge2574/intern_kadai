<?php
class Model_User extends \Orm\Model
{
    protected static $_table_name = 'users';

    protected static $_properties = array(
        'id',
        'password',
        'username',
        'created_at',
        'updated_at',
    );

    protected static $_has_many = array(
        'projects' => array(
            'model_to' => 'Model_Project',
            'key_from' => 'id',
            'key_to' => 'user_id',
        ),
    );
}
