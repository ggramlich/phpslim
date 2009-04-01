<?php
class PhpSlim_SocketService extends PhpSlim_Socket
{
    public function init()
    {
        $this->bind();
        $this->serve();
    }

    private function bind()
    {
        $result = socket_bind(
            $this->_socketResource, $this->_host, $this->_port
        );
        if (false === $result) {
            $this->raiseError("socket_bind() failed");
        }
        $this->log('Bound socket ' . $this->_host . ':' . $this->_port);
    }

    private function serve()
    {
        $result = socket_listen($this->_socketResource);
        if (false === $result) {
            $this->raiseError("socket_listen() failed");
        }
        $result = socket_accept($this->_socketResource);
        if (false === $result) {
            $this->raiseError("socket_accept() failed");
        }
        $this->_communicationSocket = $result;
    }
}
