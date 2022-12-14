<?php

declare(strict_types=1);

namespace App\Tests\Form;

use App\Form\BlogPostType;
use App\Models\Input\BlogPostInput;
use App\Repository\ImageRepository;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Validator\Validation;

final class BlogPostTypeTest extends AbstractTypeTest
{
    private ImageRepository $imageRepository;

    protected function setUp(): void
    {
        $this->imageRepository = $this->createMock(ImageRepository::class);
        parent::setUp();
    }

    public function testSubmitValidData()
    {
        $formData = [
            'title'   => 'My title',
            'content' => 'My content...',
        ];

        $model = new BlogPostInput();
        $form  = $this->factory->create(BlogPostType::class, $model);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $expected          = new BlogPostInput();
        $expected->title   = 'My title';
        $expected->content = 'My content...';

        $this->assertEquals($expected, $model);
    }

    public function testSubmitInvalidData()
    {
        $formData = [
            'content' => 'My content...',
        ];
        $form  = $this->factory->create(BlogPostType::class);
        $form->submit($formData);
        $this->assertFalse($form->isValid());

        $formData = [
            'title' => 'My title',
        ];
        $form  = $this->factory->create(BlogPostType::class);
        $form->submit($formData);
        $this->assertFalse($form->isValid());

        $formData = [
            'title' => 'My title',
            'content' => [],
        ];

        $form  = $this->factory->create(BlogPostType::class);
        $form->submit($formData);
        $this->assertFalse($form->isValid());
    }

    protected function getExtensions(): array
    {
        $validator = Validation::createValidatorBuilder()
                               ->enableAnnotationMapping()
                               ->addDefaultDoctrineAnnotationReader()
                               ->getValidator();

        return [
            ...parent::getExtensions(),
            new PreloadedExtension(
                [
                    new ValidatorExtension($validator),
                    new BlogPostType($this->imageRepository),
                ],
                []
            ),
        ];
    }

}
