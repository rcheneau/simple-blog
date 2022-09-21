<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Image;
use App\Models\Input\ImageInput;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', VichImageType::class, [
                'label' => 'image.file',
                'required' => true,
            ])
            ->add('title', TextType::class, [
                'label' => 'image.title',
                'required' => true,
                'attr' => ['maxlength' => 25],
            ])
            ->add('description', TextType::class, [
                'label' => 'image.description',
                'required' => false,
                'attr' => ['maxlength' => 100],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ImageInput::class,
        ]);
    }
}
