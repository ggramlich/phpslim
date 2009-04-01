<?php
class PhpSlim_Tests_SocketServiceTest extends PhpSlim_Tests_TestCase
{
    private $_port;
    private $_host;
    private $_ss;

    public function setup()
    {
        $this->_port = 12348;
        $this->_host = 'localhost';
    }

    // TODO FIXME
    // This does not work, did not get the mockedServer script to start
    public function _testOneConnection()
    {
        $this->runServer();
        $client = new PhpSlim_SocketClient($this->_host, $this->_port);
        $client->init();
        usleep(1000);
        $this->assertTrue($client->hasReadableData());
        $result = $client->read(5);
        $this->assertEquals('Hello', $result);
        $client->close();
    }

    private function runServer()
    {
        $command = dirname(__FILE__) . '/runMockServer.sh';
        exec($command . ' ' . $this->_port . ' &');
    }
}