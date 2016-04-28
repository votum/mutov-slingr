<?php
/**
 * Copyright notice
 *
 *
 * @author: Christian Müllenhagen <christian.muellenhagen@votum.de> - VOTUM GmbH
 * All rights reserved
 * @date: 23.04.16
 */

namespace MutovSlingr\Test\Unit\Loader;


use Codeception\TestCase\Test;
use MutovSlingr\Loader\TemplateLoader;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;
use Slim\Interfaces\CollectionInterface;

class TemplateLoaderTest extends Test
{
    private $validContent = ['test' => 'value'];


    public function setUp()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('templateFolder'));
    }

    /**
     * @expectedException \ErrorException
     */
    public function testShouldThrowExceptionIfNoFilenameGiven()
    {
        $configMock = $this->getConfigMock();

        $templateLoader = new TemplateLoader($configMock);
        $templateLoader->loadTemplate('');
    }

    /**
     * @expectedException \ErrorException
     */
    public function testShouldThrowExceptionIfFileExtensionIsNotJson()
    {
        $configMock = $this->getConfigMock();

        $templateLoader = new TemplateLoader($configMock);
        $templateLoader->loadTemplate('test.txt');
    }

    /**
     * @expectedException \ErrorException
     */
    public function testShouldThrowExceptionWithInvalidJsonFileGiven()
    {
        $file = vfsStream::newFile('someFile.json');
        $file->setContent('NO_VALID_JSON');
        vfsStreamWrapper::getRoot()->addChild($file);

        $templateLoader = new TemplateLoader($this->getConfigMock());
        $templateLoader->loadTemplate(vfsStream::url('templateFolder') . '/someFile.json');
    }

    public function testShouldReturnDecodedJson()
    {
        $this->setupFile();
        $templateLoader = new TemplateLoader($this->getConfigMock());
        $this->assertEquals($this->validContent, $templateLoader->loadTemplate(vfsStream::url('templateFolder') . '/someFile.json'));
    }

    public function testShouldReturnValidJsonFromSettingsTemplateFolder()
    {
        $this->setupFile();
        $configMock = $this->getConfigMock();
        $configMock->expects($this->any())
            ->method('get')
            ->willReturn(vfsStream::url('templateFolder'));

        $templateLoader = new TemplateLoader($configMock);
        $this->assertEquals($this->validContent, $templateLoader->loadTemplate('/someFile.json'));
    }

    /**
     * @return \Slim\Interfaces\CollectionInterface
     */
    private function getConfigMock()
    {
        return $this->getMockBuilder(CollectionInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function setupFile()
    {
        $file = vfsStream::newFile('someFile.json');
        $testContentJson = json_encode($this->validContent);
        $file->setContent($testContentJson);
        vfsStreamWrapper::getRoot()->addChild($file);
    }
}
