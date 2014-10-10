<?php

class git {

    public $remotes = array();
    public $workpath = '';
    public $gitpath = '';
    public $interactive = true;

    protected $scriptpath = '';

    public function git($workpath = '') {
        $this->scriptpath = getcwd();
        $this->workpath = $workpath;
    }

    public function test() {
        chdir($this->workpath);

        echo "I am " . prompt::ask("Who are you?");
        if (prompt::askif("What's it going to be?")) {
            echo "yes";
        } else {
            echo "no";
        }

        chdir($this->scriptpath);
    }

    static function initrepository($workpath, $gitpath = '') {
        //add prompts
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
