<?php
namespace AppBundle\Form\Type;

use AppBundle\Entity\SchemaRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use laniger\Neo4jBundle\Validator\Constraints\Neo4jUniqueLabelConstraint;
use Symfony\Component\Validator\Constraints\Regex;
use laniger\Neo4jBundle\Validator\Constraints\Neo4jLabelConstraint;
use laniger\Neo4jBundle\Validator\Constraints\Neo4jCallbackConstraint;
use laniger\Neo4jBundle\Architecture\Neo4jClientWrapper;
use laniger\Neo4jBundle\Validator\Constraints\Neo4jUniqueNameConstraint;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Entity\Attribute;
use AppBundle\Entity\AttributeDataType;
use AppBundle\Entity\Schema;
use AppBundle\Form\ServiceForm;

class SchemaFilterType extends AbstractType
{
    use ServiceForm;

    private $schemas;

    public function __construct(SchemaRepository $repo)
    {
        $this->schemas = $repo->fetchAllForCurrentUser();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = [];
        foreach ($this->schemas as $schema) {
            $choices[$schema->getName()] = $schema->getName();
        }

        $builder->add('schema', ChoiceType::class, [
            'choices' => $choices,
            'empty_data' => null,
            'placeholder' => 'label.attribute.choose-schema',
            'label' => 'label.schema.name'
        ]);
    }
}
