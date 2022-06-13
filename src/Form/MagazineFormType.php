<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Magazine;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class MagazineFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du magazine',
                'required' => false
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix du magazine',
                'required' => false,
                'currency' => 'EUR'
            ])
            ->add('created_at', DateType::class, [
                'label' => 'Date de sorie',
                'required' => false,
                'input' => 'datetime_immutable',
                'widget' => 'single_text'
            ])
            ->add('category', EntityType::class, [
                'label' => 'Choisir une catégorie',
                'class' => Category::class,
                'choice_label' => 'name'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du magazine',
                'required' => false
            ])
            ->add('coverFile', VichImageType::class, [
                    'imagine_pattern' => 'thumb', // Applique une configuration LiipImagine sur l'image
                    'download_label' => false, // Enlève le lien de téléchargement
                    'label' => 'Image de couverture',
                    'required' => false,
                    'delete_label' => 'Cocher pour supprimer cette image'
            ])

            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Magazine::class,
        ]);
    }
}
