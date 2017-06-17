<?php

namespace Frontend\FormularioBundle\Form\Bazar;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class PostularType extends AbstractType
{
    
    public function __construct($em,$producto)
    {
        $this->em = $em;
        $this->producto = $producto;

    }
    
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $em=$this->em;
        $producto=$this->producto;
        
        $check=array();
        foreach ($producto as $p) {
            $check[$p->getId()]=$p->getDescripcion();
        }
        
        
        $dql = "select x from FormularioBundle:Bazar\Producto x order by x.descripcion asc";
        $query = $em->createQuery($dql);
        $productos = $query->getResult();
        $gastronomia=array();$otros=array();
        foreach ($productos as $p) {
            if($p->getComida()==true)
                $gastronomia[$p->getId()]=$p;
            else
                $otros[$p->getId()]=$p;
        }

        $builder
            
            ->add('tipoProducto','choice',array(
                
                'choices'  => array('g' => 'Gastronomía', 'o' => 'Manualidades y otros'),
                ))
                
                
            /*->add('productoGastronomia','entity',
                array(
                    'class' => 'FormularioBundle:Bazar\Producto',
                    'multiple'=>true,
                    'expanded'=>true,
                    'query_builder' => function(EntityRepository $x){
                    return $x->createQueryBuilder('x')
                    ->where("x.comida = true")
                ;}))*/
            ->add('productoOtros','choice',
                array(
                    'multiple'=>true,
                    'expanded'=>true,
                    'choices'  => $otros,
                    'data' => array_keys($check),
                ))
                
            ->add('productoGastronomia','choice',
                array(
                    'multiple'=>true,
                    'expanded'=>true,
                    'choices'  => $gastronomia,
                    'data' => array_keys($check),
                ))
                

                
                
                
            ->add('poseePunto')
            ->add('descripcionProducto',null,array(
                'attr'=>array('data-toggle'=>'popover','title'=>'Descripcion Producto','data-content'=>'Describa los productos que va a ofrecer.',' placeholder'=>'Ej: Tortas de chocolate, maní salado etc.')
            ))
            ->add('cantidadProducto',null,array(
                'attr'=>array('data-toggle'=>'popover','title'=>'Cantidad Producto','data-content'=>'Indique la cantidad de cada producto que tiene disponible para ofrecer.',' placeholder'=>'Ej: 200 Panes de Jamón, 300 Porciones de torta')
            ))
            ->add('vendedores',null,array(
                'attr'=>array('data-toggle'=>'popover','title'=>'Vendedores','data-content'=>'Indique el nombre, apellido y numero de cédula de las personas que venderan los productos.',' placeholder'=>'Ej: Pedro Otero 123456789, Carla Maitan 45678945')
            ))
            ->add('descripcionMarca',null,array(
                'attr'=>array('data-toggle'=>'popover','title'=>'Marca Producto','data-content'=>'Realice una breve descripcion de su marca, si es empresa para la publicidad.',' placeholder'=>'Ej: Empresa dedicada a...')
            ))
                
            /*->add('descripcionOtro','textarea',array(
                'label' => 'Otro Producto',
                'attr'=>array('data-toggle'=>'popover','title'=>'Otros Productos','data-content'=>'Si en la lista de productos no aparece el que deseas ofrecer, puedes describirlo aquí.')
                ))*/
            //->add('solicitante')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Frontend\FormularioBundle\Entity\Bazar\Postular'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frontend_formulariobundle_bazar_postular';
    }
}
