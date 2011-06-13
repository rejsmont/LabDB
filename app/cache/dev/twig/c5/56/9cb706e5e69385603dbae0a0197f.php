<?php

/* MpiCbgFliesBundle::layout.html.twig */
class __TwigTemplate_c5569cb706e5e69385603dbae0a0197f extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $context = array_merge($this->env->getGlobals(), $context);

        // line 1
        echo "<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
        <style type=\"text/css\">
            body {font-family: sans-serif;}
        </style>
    </head>
    <body>
        ";
        // line 10
        $this->displayBlock('content', $context, $blocks);
        // line 12
        echo "    </body>
</html>
";
    }

    // line 10
    public function block_content($context, array $blocks = array())
    {
        // line 11
        echo "        ";
    }

    public function getTemplateName()
    {
        return "MpiCbgFliesBundle::layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
