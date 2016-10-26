<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
//use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
//use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
//use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
//use AdminBundle\Entity\size;
//use AdminBundle\Entity\childrenGoodsSizeNumber;
use AdminBundle\Entity\childrenGoodsSizeNumber;
use Doctrine\Common\Persistence\ObjectManager;
use AdminBundle\Form\DataTransformer\IssueToNumberTransformer;
//use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class bagRegistrType extends AbstractType
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /*->add('title')
            ->add('position')
            ->add('prodDatetime', 'datetime')
            ->add('prodDatetimeUpdate', 'datetime')
            ->add('childrenGoodsCategory')
            ->add('childrenGoodsSubcategory')
            ->add('descriptionGoods')
            ->add('priceGoods')*/
            ->add('prodDatetime', DateType::class)//('prodDatetime', 'datetime')
            ->add('prodDatetimeUpdate', DateTimeType::class, 
                array('input' => 'datetime', 
                    //'months' => array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6'        , '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '         12'),
                    'choice_translation_domain' => true,
                    'placeholder' => array(
                        'hour' => 'Hour', 'minute' => 'Minute', 'second' => 'Second',
                    )
                ))
                //('prodDatetimeUpdate', DateType::class)
            ->add('title')
            ->add('position')
            ->add('childrenGoodsCategory', 'entity', array('class' => 'AdminBundle:childrenGoodsCategory', 'property' => 'title', 'required' => false))
            ->add('childrenGoodsSubcategory', 'entity', array('class' => 'AdminBundle:childrenGoodsSubcategory', 'property' => 'title', 'required' => false))
            ->add('descriptionGoods', 'entity', array('class' => 'AdminBundle:descriptionGoods', 'property' => 'description', 'required' => false))
            ->add('priceGoods', 'entity', array( // MoneyType::class,
                'class' => 'AdminBundle:priceGoods', 'property' => 'rub', 'required' => false
                ))
            /*->add('category', ChoiceType::class, [
                'choices' => [
                    new size(),
                    //new size('Cat2'),
                    //new size('Cat3'),
                    //new size('Cat4'),
                ],
                'compound' => true,
                ])
            ->add('childrenGoodsSizeNumber', ChoiceType::class, array(
                'choices'  => array(
                    'Maybe' => null,
                    'Yes' => true,
                    'No' => false,
                ),
                // *this line is important*
                'choices_as_values' => true,
            ))*/
            ->add('childrenGoodsSizeNumber', 'entity', array('class' => 'AdminBundle:size', 
                'property' => 'size',//array('size', 'entity', array('class' => 'AdminBundle:size', 'property' => 'size', 'required' => false)), 
                'required' => false,
                'choices_as_values' => true,
                'expanded' => true,
                'multiple' => true,
                'invalid_message' => 'That is not a valid issue number',
                ))
            /*->add('childrenGoodsSizeNumber', CollectionType::class, array(
                'entry_type' => size::class, array(
                    'property' => 'size', 
                    'required' => false,
                    'choices_as_values' => true,
                    'expanded' => true,
                    'multiple' => true,
                    'invalid_message' => 'That is not a valid issue number',
                    )) 
                    
            )*/
            /*->add('childrenGoodsSizeNumber', 'collection', array(
               'label' => 'Size',
               'type' => new sizeType(),
               'allow_add' => true,
               'allow_delete' => true,
               'prototype' => true
              ))*/
        ;

        //$builder->get('childrenGoodsSizeNumber')
        //    ->addModelTransformer(new IssueToNumberTransformer($this->manager));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AdminBundle\Entity\childrenGoods'
        ));
    }

    /*public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array(
                'm' => 'Male',
                'f' => 'Female',
            )
        ));
    }

    public function getParent()
    {
        return ChoiceType::class;
    }*/
}
