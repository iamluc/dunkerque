<?php

use AppBundle\Entity\Manifest;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Knp\FriendlyContexts\Context\EntityContext as BaseEntityContext;

class EntityContext extends BaseEntityContext implements KernelAwareContext
{
    use KernelDictionary;

    /**
     * @Given I have users:
     */
    public function iHaveUsers(TableNode $table)
    {
        $entityName = $this->resolveEntity('users')->getName();
        $encoder = $this->getContainer()->get('security.password_encoder');

        $rows = $table->getRows();
        $headers = array_shift($rows);

        foreach ($rows as $row) {
            $values = array_combine($headers, $row);
            $entity = new $entityName;
            $reflection = new \ReflectionClass($entity);

            // Encode password
            $values['password'] = $encoder->encodePassword($entity, $values['password']);
            $values['enabled'] = true;

            do {
                $this
                    ->getRecordBag()
                    ->getCollection($reflection->getName())
                    ->attach($entity, $values);
                $reflection = $reflection->getParentClass();
            } while (false !== $reflection);

            $this
                ->getEntityHydrator()
                ->hydrate($this->getEntityManager(), $entity, $values)
                ->completeRequired($this->getEntityManager(), $entity);

            $this->getEntityManager()->persist($entity);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given I have manifests:
     */
    public function iHaveManifests(TableNode $table)
    {
        $manifestsPath = __DIR__.'/../fixtures/manifests/';
        $em = $this->getEntityManager();

        foreach ($table->getRows() as $row) {

            $content = file_get_contents($manifestsPath.$row[0]);
            $repository = $em->getRepository('AppBundle:Repository')->findOneByName(json_decode($content, true)['name']);

            $manifest = new Manifest($repository);
            $manifest->setContent($content);

            $em->persist($manifest);
        }

        $em->flush();
    }
}
