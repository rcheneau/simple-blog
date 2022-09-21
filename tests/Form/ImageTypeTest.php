<?php

declare(strict_types=1);

namespace App\Tests\Form;

use App\Form\ImageType;
use App\Models\Input\ImageInput;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Routing\RouterInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Vich\UploaderBundle\Handler\UploadHandler;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * Use PreloadedExtension to be able to use VichImageType because constructor needs arguments.
 * Internal class PropertyMappingFactory must be mocked.
 * Set file to null because purpose here is not to test VichUploaderBundle.
 */
final class ImageTypeTest extends AbstractTypeTest
{
    private StorageInterface $storage;
    private UploadHandler $uploadHandler;
    private PropertyMappingFactory $propertyMappingFactory;
    private RouterInterface $router;

    protected function setUp(): void
    {
        // mock any dependencies
        $this->storage = $this->createMock(StorageInterface::class);
        $this->uploadHandler = $this->createMock(UploadHandler::class);
        $this->propertyMappingFactory = $this->createMock(PropertyMappingFactory::class);
        $this->router = $this->createMock(RouterInterface::class);
        parent::setUp();
    }


    public function testSubmitValidData()
    {
        $formData = [
            'title' => 'My title',
            'description' => 'My description...',
        ];

        $model = new ImageInput();
        $form = $this->factory->create(ImageType::class, $model);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $expected = new ImageInput();
        $expected->file = null;
        $expected->title = 'My title';
        $expected->description = 'My description...';

        $this->assertEquals($expected, $model);
    }

    protected function getExtensions(): array
    {
        return [
            ...parent::getExtensions(),
            new PreloadedExtension(
                [
                    new VichImageType($this->storage, $this->uploadHandler, $this->propertyMappingFactory),
                    new ImageType($this->router),
                ],
                []
            ),
        ];
    }
}
