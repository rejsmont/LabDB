<?php

/* MpiCbgFliesBundle:FlyVial:show.html.twig */
class __TwigTemplate_e0d095b58d33e8ddfc58075ffce87dcd extends Twig_Template
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
        echo "    <h1>Bottle ";
        echo twig_escape_filter($this->env, $this->getContext($context, 'vial'), "html");
        echo "</h1>
    ";
        // line 5
        if ((!twig_test_empty($this->getAttribute($this->getContext($context, 'vial'), "stock", array(), "any", false)))) {
            // line 6
            echo "    <p>Stock: <a href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flystock_show", array("id" => $this->getAttribute($this->getAttribute($this->getContext($context, 'vial'), "stock", array(), "any", false), "id", array(), "any", false))), "html");
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, 'vial'), "stock", array(), "any", false), "html");
            echo "</a></p>
    ";
        }
        // line 8
        echo "    ";
        if ((!twig_test_empty($this->getAttribute($this->getContext($context, 'vial'), "cross", array(), "any", false)))) {
            // line 9
            echo "    <p>Cross: <a href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flycross_show", array("id" => $this->getAttribute($this->getAttribute($this->getContext($context, 'vial'), "cross", array(), "any", false), "id", array(), "any", false))), "html");
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, 'vial'), "cross", array(), "any", false), "html");
            echo "</a></p>
    ";
        }
        // line 11
        echo "    ";
        if ((!twig_test_empty($this->getAttribute($this->getContext($context, 'vial'), "parent", array(), "any", false)))) {
            // line 12
            echo "    <p>Flipped from: <a href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flyvial_show", array("id" => $this->getAttribute($this->getAttribute($this->getContext($context, 'vial'), "parent", array(), "any", false), "id", array(), "any", false))), "html");
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, 'vial'), "parent", array(), "any", false), "html");
            echo "</a></p>
    ";
        }
        // line 14
        echo "    <p>Setup date: ";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->getAttribute($this->getContext($context, 'vial'), "setupDate", array(), "any", false), "d.m.Y"), "html");
        echo "</p>
    <p>Flip date: ";
        // line 15
        echo twig_escape_filter($this->env, twig_date_format_filter($this->getAttribute($this->getContext($context, 'vial'), "flipDate", array(), "any", false), "d.m.Y"), "html");
        echo "</p>
    ";
        // line 16
        if ((twig_length_filter($this->env, $this->getAttribute($this->getContext($context, 'vial'), "children", array(), "any", false)) > 0)) {
            // line 17
            echo "        <p>Bottle has been flipped into the following vials:</p>
        <ul>
        ";
            // line 19
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getContext($context, 'vial'), "children", array(), "any", false));
            foreach ($context['_seq'] as $context['_key'] => $context['child']) {
                // line 20
                echo "            <li>
                <a href=\"";
                // line 21
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flyvial_show", array("id" => $this->getAttribute($this->getContext($context, 'child'), "id", array(), "any", false))), "html");
                echo "\">";
                echo twig_escape_filter($this->env, $this->getContext($context, 'child'), "html");
                echo "</a>
                (";
                // line 22
                echo twig_escape_filter($this->env, twig_date_format_filter($this->getAttribute($this->getContext($context, 'child'), "setupDate", array(), "any", false), "d.m.Y"), "html");
                echo ")
            </li>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 25
            echo "        </ul>
    ";
        }
        // line 27
        echo "    <p>
        ";
        // line 28
        if ((!twig_test_empty($this->getAttribute($this->getContext($context, 'vial'), "stock", array(), "any", false)))) {
            // line 29
            echo "            <a href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flyvial_list", array("filter" => "stock")), "html");
            echo "\">List</a> |
        ";
        } elseif ((!twig_test_empty($this->getAttribute($this->getContext($context, 'vial'), "cross", array(), "any", false)))) {
            // line 31
            echo "            <a href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flyvial_list", array("filter" => "cross")), "html");
            echo "\">List</a> |
        ";
        } else {
            // line 33
            echo "            <a href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flyvial_list", array("filter" => "cross")), "html");
            echo "\">List</a> |
        ";
        }
        // line 35
        echo "        <a href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flyvial_create"), "html");
        echo "\">New</a> |
        <a href=\"";
        // line 36
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flyvial_edit", array("id" => $this->getAttribute($this->getContext($context, 'vial'), "id", array(), "any", false))), "html");
        echo "\">Edit</a> |
        <a href=\"";
        // line 37
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flyvial_delete", array("id" => $this->getAttribute($this->getContext($context, 'vial'), "id", array(), "any", false))), "html");
        echo "\">Delete</a></p>
";
    }

    public function getTemplateName()
    {
        return "MpiCbgFliesBundle:FlyVial:show.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
