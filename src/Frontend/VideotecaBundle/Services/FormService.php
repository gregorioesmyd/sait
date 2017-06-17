<?php

namespace Frontend\VideotecaBundle\Services;

use Frontend\VideotecaBundle\TipoCinta;
use Doctrine\ORM\EntityRepository;

/**
 * Description of FormService
 *
 * @author ecastro
 */
class FormService
{

	public function createForm()
	{
		$form = $this->createFormBuilder()
            ->add('tipoCinta', 'entity', array(
                'class' => 'VideotecaBundle:TipoCinta',
                'label' => 'TipoCinta:',
                'label_attr' => array(
                    'class' => 'col-xs-3 control-label'
                ),
                'attr' => array(
                    'class' => 'form-control'
                ),
                'query_builder' => function (EntityRepository $repository){
                    $qb = $repository->createQueryBuilder('tipoCinta');
                    return $qb;
                }
            ))
            ->getForm();

        return $form;
	}

}
