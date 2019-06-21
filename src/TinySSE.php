<?php namespace Lusito\TinySSE;

/**
 * Tiny Server Sent Event support class
 * 
 * @warning PHP will use one thread per request. Keep use at a minimum.
 */
class TinySSE
{

    /**
     * In order to detect disconnects, every nth frame needs to send a comment
     * 
     * @var int
     */
    private $flushFrame;

    /**
     * This counts until flushFrame is reached
     * 
     * @var int
     */
    private $flushFrameCount = 0;

    /**
     * Set up all configuration parameters and output headers
     * 
     * @param int $flushFrame in order to detect disconnects, every nth frame needs to send a comment
     */
    public function __construct($flushFrame = 10)
    {
        $this->flushFrame = $flushFrame;

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');
    }

    /**
     * Ensure everything is flushed
     */
    private function flush()
    {
        ob_flush();
        flush();
    }

    /**
     * Reset the time limit and sleep for a specified time.
     * Also checks for disconnect in regular intervals by sending a comment.
     * 
     * @param float $sleepSeconds the number of seconds to sleep
     * @param float $timeLimit set_time_limit will be called with this value before sleeping, must be > sleepSeconds
     * @return bool FALSE if the user disconnected (only if ignore_user_abort is set to true)
     */
    public function sleep($sleepSeconds = 1, $timeLimit = 30)
    {
        // Without writing and flushing something, connection_aborted will not work, so flush a comment every nth frame.
        if (($this->flushFrameCount++) >= $this->flushFrame)
            $this->sendComment('noop');
        if (connection_aborted())
            return false;

        set_time_limit($timeLimit);
        usleep($sleepSeconds * 1000000);
        return true;
    }

    /**
     * Send a comment and flush
     * 
     * @param string $comment the comment
     */
    public function sendComment($comment)
    {
        $this->flushFrameCount = 0;
        $lines = explode("\n", $comment);
        foreach ($lines as $line)
            echo ": $line\n";
        echo "\n";
        $this->flush();
    }

    /**
     * Send an event and flush
     * 
     * @param string $data the data to be send (required)
     * @param string $event the event name (optional, default false)
     * @param string $id the id of the event (optional, default false)
     */
    public function sendEvent($data, $event = false, $id = false)
    {
        $this->flushFrameCount = 0;
        if ($id)
            echo "id: $id\n";
        if ($event)
            echo "event: $event\n";
        $lines = explode("\n", $data);
        foreach ($lines as $line)
            echo "data: $line\n";
        echo "\n";
        $this->flush();
    }
}
