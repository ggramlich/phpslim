<?php
abstract class PhpSlim_Socket
{
    protected $_host;
    protected $_port;
    private $_logger;

    protected $_socketResource;
    protected $_communicationSocket;

    public function __construct($host, $port)
    {
        $this->create(AF_INET, SOCK_STREAM, SOL_TCP);
        $this->_host = $host;
        $this->_port = $port;
    }

    abstract public function init();

    public function setLogger(PhpSlim_Logger $logger)
    {
        $this->_logger = $logger;
    }

    private function create($domain, $type, $protocol)
    {
        $this->_socketResource = socket_create($domain, $type, $protocol);
        if ($this->_socketResource < 0) {
            $this->raiseError("socket_create() failed");
        }
    }

    public function read($len)
    {
        // First check, if there is any data available.
        // Without this check socket_read might hang and not even return "".
        if (!$this->hasReadableData()) {
            $this->raiseError(
                "Socket::read() was called, " .
                "but no readable data was available."
            );
        }
        $len = (int) $len;
        usleep($len * 20);
        $input = socket_read($this->_communicationSocket, $len);
        $this->log("Read: $input");
        return $input;
    }

    public function hasReadableData()
    {
        $read = array($this->_communicationSocket);
        $write = null;
        $except = null;
        $result = socket_select($read, $write, $except, 1);
        if (false === $result) {
            $this->raiseError("socket_select() failed");
        }
        return $result > 0;
    }

    public function write($data, $len = null)
    {
        if (is_null($len)) {
            $len = strlen($data);
        }
        $this->log("Write: $data");
        return socket_write($this->_communicationSocket, $data, $len);
    }

    public function close()
    {
        if ($this->_communicationSocket !== $this->_socketResource) {
            self::closeResource($this->_communicationSocket);
        }
        return self::closeResource($this->_socketResource);
    }

    private static function closeResource($resource)
    {
        if (!empty($resource)) {
            return socket_close($resource);
        }
    }

    private function getLastSocketError()
    {
        return socket_strerror(socket_last_error());
    }

    private function getLogger()
    {
        if (empty($this->_logger)) {
            $this->_logger = new PhpSlim_Logger_Null;
        }
        return $this->_logger;
    }

    protected function log($string)
    {
        $this->getLogger()->log($string);
    }

    protected function raiseError($message, $includeSocketError = true)
    {
        if ($includeSocketError) {
            $message .= ': ' . $this->getLastSocketError();
        }
        $this->log($message);
        throw new PhpSlim_SlimError($message);
    }

}
