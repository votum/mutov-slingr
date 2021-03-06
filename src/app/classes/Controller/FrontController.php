<?php
/**
 * Created by PhpStorm.
 * User: rozbeh.sharahi
 * Date: 21.04.2016
 * Time: 12:55
 */

namespace MutovSlingr\Controller;

use MutovSlingr\Controller\AbstractController;
use MutovSlingr\Loader\TemplateLoader;
use MutovSlingr\Processor\TemplateProcessor;
use Slim\Interfaces\CollectionInterface;

/**
 * Class FrontController
 *
 * The main controller
 *
 * @package MutovSlingr\Controller
 */
class FrontController extends AbstractController
{

    /**
     * @var array
     */
    protected $config;

    /**
     * Configurations
     */
    public function __construct()
    {
        $this->view = new \MutovSlingr\Views\ViewHtml();

        $this->getView()->setLayout('/var/www/mutov-slingr/app/themes');
    }

    /**
     *
     * @param array $args
     * @return string
     */
    public function indexAction($request, $response, $args)
    {
        $templateFiles = array();
        $fullpath = $this->config->get('template_folder');
        $fileList = scandir($fullpath);

        foreach ($fileList as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            $templateFiles[] = $file;
        }

        $this->getView()->setThemeFile('base.phtml');

        return $this->getView()->render(array('templateList' => $templateFiles));
    }


    public function templatesAction($request, $response, $args)
    {
        /** @todo secure this! */
        $jsonFile = $_GET['templateName'];

        $fullpath = $this->config->get('template_folder');

        $jsonContent = file_get_contents($fullpath .'/'. $jsonFile);

        return $jsonContent;
    }

    /**
     * @param CollectionInterface $config
     */
    public function setConfig(CollectionInterface $config)
    {
        $this->config = $config;
    }

}
