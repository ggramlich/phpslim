<?php
class PhpSlim_Java_Proxy
{
    public function getStatementExecutor()
    {
        $executor = new PhpSlim_StatementExecutor();
        return java_closure(new PhpSlim_Java_StatementExecutor($executor));
    }
}
