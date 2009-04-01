<?php
class PhpSlim_SocketClient extends PhpSlim_Socket
{
    private $_connectRetries = 3;
    private $_usleepTime = 2000;

    public function init()
    {
        $this->connect();
    }

    private function connect()
    {
        $result = $this->tryToConnect();
        $tries = 0;
        // Allow for some retries. Makes it a little more stable.
        while ($result === false && $tries < $this->_connectRetries) {
            usleep($this->_usleepTime);
            $result = $this->tryToConnect();
            $tries++;
        }
        if (false === $result) {
            $this->raiseError("socket_connect() failed");
        }
        $this->_communicationSocket = $this->_socketResource;
    }

    private function tryToConnect()
    {
        return @socket_connect(
            $this->_socketResource, $this->_host, $this->_port
        );
    }
}