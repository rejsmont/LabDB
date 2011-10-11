<?php

/* AcmeDemoBundle:Demo:contact.html.twig */
class __TwigTemplate_c446a870013a0693e3dcc346327f581a extends Twig_Template
{
    protected $parent;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = array();
        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'content' => array($this, 'block_content'),
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
    public function block_title($context, array $blocks = array())
    {
        echo "Symfony - Contact form";
    }

    // line 5
    public function block_content($context, array $blocks = array())
    {
        // line 6
        echo "    <form action=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("_demo_contact"), "html");
        echo "\" method=\"POST\" id=\"contact_form\">
        ";
        // line 7
        echo $this->env->getExtension('form')->renderErrors($this->getContext($context, 'form'));
        echo "

        ";
        // line 9
        echo $this->env->getExtension('form')->renderRow($this->getAttribute($this->getContext($context, 'form'), "email", array(), "any", false));
        echo "
        ";
        // line 10
        echo $this->env->getExtension('form')->renderRow($this->getAttribute($this->getContext($context, 'form'), "message", array(), "any", false));
        echo "

        ";
        // line 12
        echo $this->env->getExtension('form')->renderRest($this->getContext($context, 'form'));
        echo "
        <input type=\"submit\" value=\"Send\" class=\"symfony-button-grey\" />
    </form>
";
    }

    public function getTemplateName()
    {
        return "AcmeDemoBundle:Demo:contact.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
