<?php

declare(strict_types=1);

namespace App\Form;

use App\Models\Input\ImageInput;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class ImageType extends AbstractType
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var ImageInput|null $data */
        $data = $options['data'] ?? null;

        $builder
            ->add('file', VichImageType::class, [
                'label' => 'image.file',
                'required' => $data === null,
                'download_uri' =>   $data && $data->id
                    ? $this->router->generate('app_image_original', ['id' => $data->id->toRfc4122()])
                    : false,
                'imagine_pattern' => 'image_500_500',
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
