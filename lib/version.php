<?php

class version {

    protected $version = array();

    public function version($version) {
        if (!is_array($version)) {
            $version = explode(' ', trim(preg_replace('/\D/', ' ', $version)));
        }
        $this->version = $version;
    }

    public function get($level) {
        if (isset($this->version[$level-1])) {
            $value = $this->version[$level-1];
            if (!empty($value)) {
                return $value;
            }
        }
        throw new Exception("version does not have requested precision level $level", __LINE__);
    }

    public function set($level, $value) {
        $this->version[$level-1] = trim($value);
    }

    public function clear($level) {
        if (isset($this->version[$level-1])) {
            unset($this->version[$level-1]);
        }
    }

    public function string($precision, $prefix = '', $delim = '.', $altdelim = '-', $altdelimindex = 4) {
        $print = $prefix;
        for($i = 1; $i <= $precision; $i++) {
            if ($i > 1) {
                $print .= ($altdelim && $altdelimindex && $i >= $altdelimindex) ? $altdelim : $delim;
            }
            $print .= $this->get($i);
        }
        return $print;
    }
}
