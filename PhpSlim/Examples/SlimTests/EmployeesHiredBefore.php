<?php
class Examples_SlimTests_EmployeesHiredBefore
{
    private $_date;

    public function __construct($date)
    {
        $this->_date = $date;
    }

    public function table($table)
    {
        //optional function
    }

    public function query()
    {
        $list = array(
            array(
                'employee number' => '1429',
                'first name' => 'Bob',
                'last name' => 'Martin',
                'hire date' => '10-Oct-1974'
            ),
            array(
                'employee number' => '8832',
                'first name' => 'James',
                'last name' => 'Grenning',
                'hire date' => '15-Dec-1979'
            ),
        );
        return PhpSlim_TypeConverter::hashListToPairsList($list);
    }
}
