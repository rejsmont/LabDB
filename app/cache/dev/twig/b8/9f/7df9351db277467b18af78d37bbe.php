<?php

/* AcmeDemoBundle:Secured:layout.html.twig */
class __TwigTemplate_b89f7df9351db277467b18af78d37bbe extends Twig_Template
{
    protected $parent;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = array();
        $this->blocks = array(
            'content_header_more' => array($this, 'block_content_header_more'),
        );
    }

    public function getParent(array $context)
    {
        $parent = "AcmeDemoBundle::layout.html.twig";
        if ($parent instanceof Twig_Template) {
            $name = $parent->getTemplateName();
            $this->parent[$name] = $parent;
            $parent = $name;
        } elseif (!isset($this->parent[$parent])) {
            $this->parent[$parent] = $this->env->loadTemplate($parent);
        }

        return $this->parent[$parent];
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $context = array_merge($this->env->getGlobals(), $context);

        $this->getParent($context)->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content_header_more($context, array $blocks = array())
    {
        // line 4
        echo "    ";
        echo $this->renderParentBlock("content_header_more", $context, $blocks);
        echo "
    <li>logged in as <strong>";
        // line 5
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getContext($context, 'app'), "user", array(), "any", false)) ? ($this->getAttribute($this->getAttribute($this->getContext($context, 'app'), "user", array(), "any", false), "username", array(), "any", false)) : ("Anonymous")), "html");
        echo "</strong> - <a href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("_demo_logout"), "html");
        echo "\">Logout</a></li>
";
    }

    public function getTemplateName()
    {
        return "AcmeDemoBundle:Secured:layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
