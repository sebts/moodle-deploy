<?php

class namer {

    protected $version = null;

    public function namer($version = null) {
        $this->version = $version;
    }

    public function featurebranch($name, $version = null) {
        $this->checkversion($version);
        return $version->string(2) . '/feature/' . $name;
    }

    public function featuretag($name, $version = null) {
        $this->checkversion($version);
        return 'feature/' . $name . '/' . $version->string(4);
    }

    public function integrationbranch($version = null) {
        $this->checkversion($version);
        return $version->string(2) . '/integration';
    }

    public function integrationtag($version = null) {
        $this->checkversion($version);
        return 'integration/' . $version->string(4);
    }

    public function releasetag($version = null) {
        $this->checkversion($version);
        return $version->string(3, 'v');
    }

    protected function checkversion(&$version) {
        if ($version == null) {
            $version = $this->version;
            if ($version == null) {
                throw new Exception("version must not be null.");
            }
        }
    }
}
