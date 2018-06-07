<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Service;

use Jett\JSONEntitySerializerBundle\Exception\ClassNotFoundException;
use Jett\JSONEntitySerializerBundle\Exception\RenderFailedException;
use Jett\JSONEntitySerializerBundle\Exception\SampleObjectException;
use Jett\JSONEntitySerializerBundle\Info\InfoProvider;
use Symfony\Component\Filesystem\Filesystem;

class SerializerBuilder
{

    protected $_fs;
    private $_configService;
    private $_cachePath;
    private $_environment;
    private $_infoProvider;

    public function __construct(
        InfoProvider $infoProvider,
        ConfigService $configService,
        $cachePath,
        $environment
    ) {
        $this->_fs = new Filesystem();
        $this->_configService = $configService;
        $this->_cachePath = $cachePath;
        $this->_environment = strtolower($environment);
        $this->_infoProvider = $infoProvider;
    }

    public function loadSerializer()
    {
        $name = $this->getClassName();
        $file = $this->_cachePath.DIRECTORY_SEPARATOR.$name.'.php';
        require_once $file;
        $name::setSamples($this->getSamples());
        $instance = new $name();

        return $instance;
    }

    /**
     * Generates the serializer class.
     *
     * @param $force - Force rebuild of class
     *
     * @throws RenderFailedException  if can't render file
     * @throws ClassNotFoundException if some entity class was not found
     * @throws SampleObjectException
     */
    public function generateService($force = false)
    {
        $classes = [];

        $name = $this->getClassName();

        if ($force || $this->fileShouldBeRebuilt()) {
            $this->checkSamples();
            foreach ($this->_configService->getEntities() as $entity => $_) {
                $classes[$entity] = $this->generate($entity);
            }

            $content = $this->render('serializer.php.twig', [
                'classes' => $classes,
                'entities' => $this->_configService->getEntities(),
                'name' => $name,
            ]);
            if (!file_exists($this->_cachePath)) {
                mkdir($this->_cachePath);
            }
            file_put_contents($this->_cachePath.DIRECTORY_SEPARATOR.$name.'.php', $content);
        }
    }

    /**
     * Check entities maps if they are correct.
     *
     * @throws SampleObjectException if at least one map can't be transformed to an object
     */
    public function checkSamples()
    {
        foreach ($this->_configService->getEntities() as $entity => $data) {
            if (!isset($data['samples'])) {
                continue;
            }
            $samples = $data['samples'];

            foreach ($samples as $name => $sample) {
                $obj = json_decode($sample);

                if (!$obj) {
                    throw new SampleObjectException($entity, $name);
                }
            }
        }
    }

    public function getClassName()
    {
        return 'Serializer'.$this->_configService->getConfigHash();
    }

    protected function getSamples()
    {
        $samples = [];
        $entities = $this->_configService->getEntities();
        foreach ($entities as $entity => $data) {
            if (!isset($data['samples'])) {
                continue;
            }
            foreach ($data['samples'] as $name => $sample) {
                $samples[$entity.':'.$name] = json_decode($sample);
            }
        }

        return $samples;
    }

    protected function fileShouldBeRebuilt()
    {
        if ('prod' === $this->_environment) {
            return false;
        }
        $name = $this->getClassName();
        if (!file_exists($this->_cachePath)) {
            $files = [];
        } else {
            $files = array_filter(scandir($this->_cachePath), function ($i) {
                return '.' !== $i && '..' !== $i && strpos($i, 'php');
            });
        }

        foreach ($files as $i => $file) {
            if ($file !== $name.'.php') {
                unlink($this->_cachePath.DIRECTORY_SEPARATOR.$file);
                unset($files[$i]);
            }
        }

        return empty($files);
    }



    /**
     * @param $template - template name relative to bundle Resources/views
     * @param $vars - variables accessible from template
     *
     * @throws RenderFailedException if an error occurred
     *
     * @return string
     */
    protected function render($template, $vars)
    {
        try {
            $loader = new \Twig_Loader_Filesystem(__DIR__.'/../Resources/views');
            $twig = new \Twig_Environment($loader);

            return $twig->render($template, $vars);
        } catch (\Exception $ex) {
            throw new RenderFailedException($template, $ex);
        }
    }


    /**
     * Generates a function which contains the normalization logic for the current entity.
     *
     * @param string $className - Doctrine entity FQCN
     *
     * @throws ClassNotFoundException if class cant be loaded
     * @throws RenderFailedException  if can't render template
     *
     * @return string - php function text representation
     */
    protected function generate(string $className): string
    {
        try {
            list($fields, $links) = $this->_infoProvider->getInfoForClass($className);

            $content = $this->render('function.php.twig', [
                'fields' => $fields,
                'links' => $links,
                'var' => 'var',
                'object' => 'object',
                'return' => true,
            ]);

            return $content;
        } catch (\ReflectionException $ex) {
            throw new ClassNotFoundException($className);
        }
    }
}
