<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType; // Ajout de FileType
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File; // Pour les contraintes de fichier

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', TextType::class, [
                'required' => false, // Correspond à nullable=true dans l'entité
            ])
            ->add('ecopoint', IntegerType::class, [
                'required' => true, // Correspond à nullable=false dans l'entité
            ])
            ->add('quantite', IntegerType::class, [
                'required' => true, // Correspond à nullable=false dans l'entité
            ])
            ->add('description', TextareaType::class, [
                'required' => true, // Correspond à nullable=false dans l'entité
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}