<?php
namespace AppBundle\Architecture;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * wrapper for container service getters
 *
 * @author laniger
 */
trait ContainerServices
{

    /**
     *
     * @return Neo4jClientWrapper
     */
    private function getNeo4jClient()
    {
        return $this->container->get('neo4j_client');
    }

    /**
     *
     * @return EncoderFactoryInterface
     */
    private function getPasswordEncoderFactory()
    {
        return $this->container->get('security.encoder_factory');
    }
}