<?php

namespace ZfcTwig\Twig;

use Twig_SimpleFunction;
use Zend\View\Helper\HelperInterface;

class FallbackFunction extends Twig_SimpleFunction
{
    /**
     * @var HelperInterface
     */
    protected $helper;

    public function __construct($helper)
    {
        $this->helper = $helper;

        parent::__construct($helper, null, array('is_safe' => array('all')));
    }

    /**
     * Compiles a function.
     *
     * @return string The PHP code for the function
     */
    public function compile()
    {
        return sprintf('$this->env->getExtension("zfc-twig")->getRenderer()->plugin("%s")->__invoke', $this->helper);
    }
}
