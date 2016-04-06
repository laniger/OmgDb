<?php
namespace AppBundle\Entity;

use laniger\Neo4jBundle\Architecture\Neo4jClientWrapper;
use laniger\Neo4jBundle\Architecture\Neo4jRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class AttributeRepository extends Neo4jRepository
{
    /**
     * @var User
     */
    private $user;

    public function __construct(Neo4jClientWrapper $client, TokenStorage $storage)
    {
        parent::__construct($client);
        $this->user = $storage->getToken()->getUser();
    }

    public function getForSchema(Schema $schema)
    {
        $attr = $this->getClient()->cypher('
            MATCH (s:schema)<-[:attribute_of]-(a:attribute)
            WHERE s.name = {name}
           RETURN a
            ORDER BY s.name
        ', [
            'name' => $schema->getName()
        ]);

        $attributes = [];

        if (isset($attr->getRows()['a'])) {
            foreach ($attr->getRows()['a'] as $row) {
                $a = $this->createFromRow($row);
                $a->setSchemaName($schema->getName());
                $attributes[] = $a;
            }
        }
        return $attributes;
    }

    public function newAttribute(Attribute $attr)
    {
        $this->getClient()->cypher('
            MATCH (u:user)<-[:created_by]-(s:schema)
            WHERE u.name = {username}
              AND s.name = {schemaname}
           CREATE (a:attribute)-[:attribute_of]->(s)
              SET a.name = {attrname},
                  a.datatype = {datatype},
                  a.created_at = {date}
        ', [
            'username' => $this->user->getUsername(),
            'schemaname' => $attr->getSchemaName(),
            'attrname' => $attr->getName(),
            'datatype' => $attr->getDataType()->getName(),
            'date' => date(\DateTime::ISO8601)
        ]);
    }

    public function isNameUniqueForCurrentUser(Attribute $attr)
    {
        $count = $this->getClient()->cypher('
            MATCH (a:attribute)-[:attribute_of]->(s:schema),
                  (s)-[:created_by]->(u:user)
            WHERE s.name = {schemaname}
              AND u.name = {username}
              AND a.name = {attributename}
           RETURN COUNT(a) AS cnt
        ', [
            'schemaname' => $attr->getSchemaName(),
            'username' => $this->user->getUsername(),
            'attributename' => $attr->getName()
        ])->getRows()['cnt'][0];

        return $count < 1;
    }

    private function createFromRow($row)
    {
        $a = new Attribute();
        $a->setName($row['name']);
        $a->setCreatedAt(\DateTime::createFromFormat(\DateTime::ISO8601, $row['created_at']));
        $a->setDataType(AttributeDataType::getByName($row['datatype']));
        return $a;
    }
}
