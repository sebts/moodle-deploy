<?php

class git {

    protected $cfg = null; //gitconfig

    public function git(gitconfig $config) {
        $this->cfg = $config;
        if (empty($this->cfg->workpath)) {
            throw new Exception('Workpath must be configured.', __LINE__);
        }
        if (empty($this->cfg->scriptpath)) {
            throw new Exception('Scriptpath must be configured.', __LINE__);
        }
    }

    protected function cdrepo() {
        $this->initrepository();
        chdir($this->cfg->workpath);
    }

    protected function cdout() {
        chdir($this->cfg->scriptpath);
    }

    protected function initrepository() {
        $workpath = $this->cfg->workpath;
        $gitpath = $this->cfg->gitpath;

        if ($this->cfg->interactive) {
            if (is_dir($workpath) && !file_exists("$workpath/.git")) {
                prompt::state("Work path '$workpath' already exists, but is not under git control.");
                if (!prompt::askif("Are you sure you want to initialize your workpath in this directory?")) {
                    throw new Exception("Please set the correct workpath in the config.php file.");
                }
            }
        }

        $options = '';
        if (!empty($gitpath) && $gitpath != $workpath) {
            $options = "--separate-git-dir=\"$gitpath\"";
        }
        exec("git init $options \"$workpath\"", $output, $exit);
        if ($exit != 0) {
            throw new Exception('Failed to set initialize repository at "' . $workpath . "\" with options \"$options\"", __LINE__);
        }
    }

    static function initremote($remote, $url) {
        if (!$remote) {
            throw new Exception("Param remote is empty.", __LINE__);
        }
        if (!$url) {
            throw new Exception("Param url is empty.", __LINE__);
        }

        $exit = 0;
        exec("git remote -v", $remotes, $exit);
        $remotes = preg_grep("/^$remote/", $remotes);
        if (sizeof($remotes) == 0) {
            echo "Adding remote '$remote' as url '$url'" . PHP_EOL;
            exec("git remote add $remote $url", $output, $exit);
        } else {
            if(sizeof(preg_grep("|^$remote\s$url|", $remotes)) < 2) {
                echo "!!! Changing remote '$remote' to url '$url'" . PHP_EOL;
                exec("git remote set-url $remote $url", $output, $exit);
            }
        }
        if ($exit != 0) {
            throw new Exception("Failed to set remote $remote to $url.", __LINE__);
        }
        git::validateremote($remote, $url);
    }

    static function validateremote($remote, $url = '') {
        exec("git remote show $remote", $output, $exit);
        if ($exit != 0) {
            if (empty($url)) {
                $url = git::getremoteurl($remote);
            }
            throw new Exception("Failed to connect remote '$remote' at url '$url'.", __LINE__);
        }
    }

    static function getremoteurl($remote) {
        exec("git remote -v", $output);
        $pattern = "|^$remote\t(.*?) \(fetch\)$|";
        $entry = preg_grep($pattern, $output);
        if (isset($entry[0])) {
            preg_match($pattern, $setting[0], $matches);
            if (isset($matches[1])) {
                return $matches[1];
            }
        }
        return '';
    }

    static function fetch($repository, $refspec, $options = "--quiet") {
        if (!$repository) {
            throw new Exception("Param repository is empty.", __LINE__);
        }
        if (!$refspec) {
            throw new Exception("Param refspec is empty.", __LINE__);
        }

        exec("git fetch $options $repository $refspec", $output, $exit);
        if ($exit != 0) {
            throw new Exception("Unable to fetch $refspec from $repository.", __LINE__);
        }
    }
}
