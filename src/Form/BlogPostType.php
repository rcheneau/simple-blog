<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Image;
use App\Models\Input\BlogPostInput;
use App\Repository\ImageRepository;
use Doctrine\DBAL\Types\ConversionException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BlogPostType extends AbstractType
{
    private ImageRepository $imageRepository;

    public function __construct(ImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', TextType::class, [
                'label' => 'blog_post.image',
                'required' => false,
            ])
            ->add('title', TextType::class, [
                'label' => 'blog_post.title',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'blog_post.content',
                'attr'  => ['rows' => '5'],
            ]);


        $builder->get('image')
            ->addModelTransformer(new CallbackTransformer(
                fn (?Image $image) => $image?->getId()->toRfc4122() ?? '',
                function (?string $uuid) {
                    if (null === $uuid) {
                        return null;
                    }

                    try {
                        $image = $this->imageRepository->find($uuid);
                    } catch (ConversionException) {
                        $image = null;
                    }

                    if (null === $image) {
                        throw new TransformationFailedException(sprintf(
                            'An image with id "%s" does not exist.',
                            $uuid
                        ));
                    }

                    return $this->imageRepository->find($uuid);
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BlogPostInput::class,
        ]);
    }
}
