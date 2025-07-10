class Model_Task extends \Orm\Model
{
    protected static $_table_name = 'tasks';

    protected static $_properties = array(
        'id',
        'project_id',
        'name',
        'due_date',
        'status',
        'created_at',
        'updated_at',
    );

    protected static $_belongs_to = array(
        'project' => array(
            'model_to' => 'Model_Project',
            'key_from' => 'project_id',
            'key_to' => 'id',
        ),
    );
}
