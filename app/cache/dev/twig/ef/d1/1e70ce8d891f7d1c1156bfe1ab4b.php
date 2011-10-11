<?php

/* MpiCbgFliesBundle:FlyStock:show.html.twig */
class __TwigTemplate_efd11e70ce8d891f7d1c1156bfe1ab4b extends Twig_Template
{
    protected $parent;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = array();
        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    public function getParent(array $context)
    {
        $parent = "MpiCbgFliesBundle::layout.html.twig";
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
    public function block_content($context, array $blocks = array())
    {
        // line 4
        echo "    <h1>Stock ";
        echo twig_escape_filter($this->env, $this->getContext($context, 'stock'), "html");
        echo "</h1>
    ";
        // line 5
        if ((!twig_test_empty($this->getAttribute($this->getContext($context, 'stock'), "sourceCross", array(), "any", false)))) {
            // line 6
            echo "    <p>
        Source cross:
        <a href=\"";
            // line 8
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flycross_show", array("id" => $this->getAttribute($this->getAttribute($this->getContext($context, 'stock'), "sourceCross", array(), "any", false), "id", array(), "any", false))), "html");
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, 'stock'), "sourceCross", array(), "any", false), "html");
            echo "</a>
    </p>
    ";
        }
        // line 11
        echo "    ";
        if ((twig_length_filter($this->env, $this->getAttribute($this->getContext($context, 'stock'), "vials", array(), "any", false)) > 0)) {
            // line 12
            echo "        <p>Stock is kept in the following vials:</p>
        <ul>
        ";
            // line 14
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getContext($context, 'stock'), "vials", array(), "any", false));
            foreach ($context['_seq'] as $context['_key'] => $context['vial']) {
                // line 15
                echo "            <li>
                <a href=\"";
                // line 16
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flyvial_show", array("id" => $this->getAttribute($this->getContext($context, 'vial'), "id", array(), "any", false))), "html");
                echo "\">";
                echo twig_escape_filter($this->env, $this->getContext($context, 'vial'), "html");
                echo "</a>
                (";
                // line 17
                echo twig_escape_filter($this->env, twig_date_format_filter($this->getAttribute($this->getContext($context, 'vial'), "setupDate", array(), "any", false), "d.m.Y"), "html");
                echo ")
            </li>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['vial'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 20
            echo "        </ul>
    ";
        }
        // line 22
        echo "    <p><a href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flystock_list"), "html");
        echo "\">List</a> |
       <a href=\"";
        // line 23
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flystock_create"), "html");
        echo "\">New</a> |
       <a href=\"";
        // line 24
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flystock_edit", array("id" => $this->getAttribute($this->getContext($context, 'stock'), "id", array(), "any", false))), "html");
        echo "\">Edit</a> |
       <a href=\"";
        // line 25
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flystock_delete", array("id" => $this->getAttribute($this->getContext($context, 'stock'), "id", array(), "any", false))), "html");
        echo "\">Delete</a></p>
";
    }

    public function getTemplateName()
    {
        return "MpiCbgFliesBundle:FlyStock:show.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
