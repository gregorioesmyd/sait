<?php

namespace Frontend\NominaBundle\Form\Reporte;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class HistoricoType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */

    public function buildForm(FormBuilderInterface $builder, array $options){

        $anios=array();
        for($i=date('Y')-11;$i<=date('Y');$i++)
            $anios[$i]=$i;
        
        $meses=array();
        for($i=1;$i<=12;$i++)
            $meses[$i]=$i;
        
        $builder->add('cedula');
        $builder->add('aniodesde', 'choice',array(
                    'choices'=>$anios));
        $builder->add('mesdesde', 'choice',array(
                    'choices'=>$meses));        
        $builder->add('aniohasta', 'choice',array(
                    'choices'=>array_reverse($anios,true)));
        $builder->add('meshasta', 'choice',array(
                    'choices'=>array_reverse($meses,true)));
        

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\NominaBundle\Entity\Reporte\Historico'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'data14100';
    }
}
