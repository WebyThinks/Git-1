<?php
namespace MWCavender\Git;

use MWCavender\Common\Executer;

class GitRepo extends Executer
{
    /**
     * Magic method to allow us to call protected methods
     * @param  string $name The method name.
     * @param  array $arguments An array of arguments passed with the method.
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this, $name), $arguments);
    }

    /**
     * Magic method that will allow us to call our first method statically
     * @param  string $name The method name.
     * @param  array $arguments An array of arguments passed with the method.
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        $instance = new self;

        return call_user_func_array(array($instance, $name), $arguments);
    }

    /**
     * Construct our object and set the basics.
     * @param string $repo_path [optional] The path of the repository to work with.
     *                          Leave blank to use the CWD.
     */
    public function __construct($repo_path = null)
    {
        $this->setApplication('git');

        if ($repo_path !== null) {
            $this->setWorkingDirectory($repo_path);
        }
    }

    /**
     * Check out a treeish (branch, tag, sha1) in an existing repo
     * @param  string $branch The treeish to check out (e.g. master, 1.0.1, fe147dc)
     * @return GetRepo Returns $this for method chaining.
     */
    protected function checkout($branch)
    {
        $this->addArgument('checkout')->addArgument($branch);

        return $this->execute();
    }

    /**
     * Clone a repo (clone is a keyword in PHP, so cloneRepo was chosen)
     * @param  string $repo The repo to clone.
     * @param  string $target [optional] The target directory to clone the repo to.
     *                        Leave blank to clone into the default directory name in the CWD.
     * @return GitRepo Returns $this for method chaining.
     */
    protected function cloneRepo($repo, $target = null)
    {
        $this->addArgument('clone')->addArgument($repo);

        if ($target !== null) {
            $target = realpath($target);
            $this->addArgument($target);
        }

        return $this->execute();
    }

    /**
     * Gets an array of local branch names in the repo.
     * @return array An array of branch name. Note: the "* " for the current branch is stripped.
     */
    public function getBranches()
    {
        $output = $this->addArgument('branch')->execute()->getOutput();

        $branches = explode(PHP_EOL, $output);

        // let's remove any blark lines and string the "* " on the current branch
        foreach ($branches as $i => &$branch) {
            if (empty($branch)) {
                unset($branches[$i]);
            }

            $branch = str_replace('* ', '', $branch);
        }

        return $branches;
    }

    /**
     * Gets the current branch's name.
     * @return string
     */
    public function getCurrentBranch()
    {
        $output = $this->addArgument('branch')->execute()->getOutput();

        $branches = explode(PHP_EOL, $output);

        // let's remove any blank lines
        foreach ($branches as $branch) {
            if (strlen($branch) > 0 && substr($branch, 0, 1) === '*') {
                return substr($branch, 2);
            }
        }

        return '';
    }

    /**
     * Gets the name of the most recent tag reachable.
     * @return string
     */
    public function getCurrentTag()
    {
        $tag = $this->addArgument('describe')->execute()->getOutput();

        // @todo add ability to grab extended data if not exactly on a tag

        return $tag;
    }

    /**
     * Gets an array of tag names in the repo.
     * @return array An array of tag names.
     */
    public function getTags()
    {
        $output = $this->addArgument('tag')->execute()->getOutput();

        $tags = explode(PHP_EOL, $output);

        // lets remove and blank lines
        foreach ($tags as $i => $tag) {
            if (empty($tag)) {
                unset($tags[$i]);
            }
        }

        return $tags;
    }

    /**
     * Fetch and merge from the remote.
     * @param  string $remote [optional] The remote name.
     * @param  string $branch [optional] The branch name.
     * @return GitRepo Returns $this for method chaning.
     */
    protected function pull($remote = null, $branch = null)
    {
        $this->addArgument('pull');

        if ($remote !== null) {
            $this->addArgument($remote);
        }

        if ($branch !== null) {
            $this->addArgument($branch);
        }

        return $this->execute();
    }
}