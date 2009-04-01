<?php
class PhpSlim_Server
{
    private $_executor;
    private $_socket;
    private $_logger;
    private $_socketLogger;

    public function setLogger(
        PhpSlim_Logger $logger, PhpSlim_Logger $socketLogger = null
    )
    {
        $this->_logger = $logger;
        $this->_socketLogger = $socketLogger;
    }

    public function run($port)
    {
        $this->_executor = new PhpSlim_ListExecutor();
        $this->_socket = new PhpSlim_SocketService('localhost', $port);
        if (!empty($this->_socketLogger)) {
            $this->_socket->setLogger($this->_socketLogger);
        }
        $this->_socket->init();
        $this->serveSlim();
        $this->_socket->close();
    }

    private function serveSlim()
    {
        $this->_socket->write("Slim -- V0.0\n");
        while (true) {
            $command = $this->readCommand();
            if (strtolower($command) == 'bye') {
                return;
            }
            $response = $this->processCommand($command);
            $this->sendResponse($response);
        }
    }

    private function readCommand()
    {
        $length = $this->_socket->read(6);
        // Skip colon
        $this->_socket->read(1);
        return $this->_socket->read($length);
    }

    private function processCommand($command)
    {
        $this->log('Processing: ' . $command);
        $instructions = PhpSlim_ListDeserializer::deserialize($command);
        $results = $this->_executor->execute($instructions);
        $response = PhpSlim_ListSerializer::serialize($results);
        $this->log('Result: ' . $response);
        return $response;
    }

    private function sendResponse($response)
    {
        $this->_socket->write(sprintf('%06d:%s', strlen($response), $response));
    }

    private function getLogger()
    {
        if (empty($this->_logger)) {
            $this->_logger = new PhpSlim_Logger_Null;
        }
        return $this->_logger;
    }

    private function log($string)
    {
        $this->getLogger()->log($string);
    }
}
