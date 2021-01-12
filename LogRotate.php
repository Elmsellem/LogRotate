<?php

if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

class LogRotate
{
    protected $tmp_log_file_size;
    protected $log_file_size;
    protected $log_file_name;
    protected $log_dir;
    protected $log_file;
    protected $tmp_log_file;
    protected $logs;

    public function __construct($log_file_size, $log_dir, $log_file_name = "output.log")
    {
        $this->log_file_size = $log_file_size;
        $tmp_log_file_size = (int)($log_file_size / 10);
        $tmp_log_file_size < 1024 && $tmp_log_file_size = 1024;

        $this->tmp_log_file_size = $tmp_log_file_size;
        $this->log_file_name = $log_file_name;

        $this->log_dir = rtrim($log_dir, "/\\");
        $this->log_file = $this->log_dir . DS . $this->log_file_name;
        $this->tmp_log_file = $this->log_dir . DS . 'tmp_' . $this->log_file_name;

        !file_exists($this->log_dir) && mkdir($this->log_dir, 0777, true);
    }

    public function write($logs)
    {
        $this->logs = $logs;
        if ($this->tmp_log_size() >= $this->tmp_log_file_size) {
            $this->write_logs();
        } else {
            $this->write_tmp_logs();
        }
    }

    protected function write_logs()
    {
        $tmp_logs = $this->get_tmp_logs();
        $logs = $this->get_logs() . $tmp_logs;
        $last_logs = mb_substr($logs, -$this->log_file_size);
        file_put_contents($this->log_file, $last_logs);

        $this->delete_tmp_file();
    }

    protected function write_tmp_logs()
    {
        file_put_contents($this->tmp_log_file, $this->logs, FILE_APPEND);
    }

    protected function get_tmp_logs(): string
    {
        $tmp_logs = @file_get_contents($this->tmp_log_file);
        return $tmp_logs . $this->logs;
    }

    protected function get_logs(): string
    {
        $logs = @file_get_contents($this->log_file);
        return $logs;
    }

    protected function delete_tmp_file(): bool
    {
        return unlink($this->tmp_log_file);
    }

    protected function tmp_log_size(): int
    {
        $fz = @filesize($this->tmp_log_file);
        return +$fz + mb_strlen($this->logs);
    }
}
