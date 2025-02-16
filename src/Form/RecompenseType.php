<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\Recompense;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File; // Pour les contraintes de fichier

class RecompenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('valeur' ,IntegerType::class, [
                'required' => true,])
                ->add('description', TextareaType::class, [
                    'required' => true, // Correspond à nullable=false dans l'entité
                ])         
                ->add('disponibilite', CheckboxType::class, [
                    'label' => 'disponibilite',
                    'required' => false,
                ])
                
                  ->add('image', FileType::class, [
                'required' => false, // Correspond à nullable=true dans l'entité
                'mapped' => false, // Ne pas mapper directement à l'entité
                'constraints' => [
                    new File([
                        'maxSize' => '5000k', 
                        'mimeTypes' => [ // Types MIME autorisés
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG, PNG, GIF).',
                    ])
                ],
                'label' => 'Image (JPEG, PNG, GIF)',
            ])
                        ->add('categorie', EntityType::class, [
                'class' => Produit::class,
                'choice_label' => 'type',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recompense::class,
        ]);
    }
}
