<?php

use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileContext implements KernelAwareContext
{
    use KernelDictionary;

    /**
     * @param int    $count
     * @param string $connection
     *
     * @throws Exception
     *
     * @Then :count queries have been run
     */
    public function queriesHaveBeenRunOnConnection($count)
    {
        $connection = 'default';

        $queries = $this->getProfile()->getCollector('db')->getQueries();
        if (!array_key_exists($connection, $queries)) {
            throw new \Exception(sprintf('No connection named "%s" found', $connection));
        }

        if (((int) $count) !== ($actualCount = count($queries[$connection]))) {
            throw new \Exception(sprintf(
                '%d queries were executed on %s connection',
                $actualCount,
                $connection
            ));
        }
    }

    protected function getProfile()
    {
        $this->getContainer()->get('profiler')->enable();

        return $this->getContainer()->get('profiler')->collect(
            new Request(),
            new Response()
        );
    }
}
