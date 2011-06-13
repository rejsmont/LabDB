<?php

/* MpiCbgFliesBundle:FlyCross:show.html.twig */
class __TwigTemplate_53f9011dbb7e9350e4703edd3420e369 extends Twig_Template
{
    protected $parent;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    public function getParent(array $context)
    {
        if (null === $this->parent) {
            $this->parent = $this->env->loadTemplate("MpiCbgFliesBundle::layout.html.twig");
        }

        return $this->parent;
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
        echo "    <h1>
        Cross ";
        // line 5
        echo twig_escape_filter($this->env, $this->getContext($context, 'cross'), "html");
        echo "
        (<a href=\"";
        // line 6
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flyvial_show", array("id" => $this->getAttribute($this->getAttribute($this->getContext($context, 'cross'), "vial", array(), "any", false), "id", array(), "any", false))), "html");
        echo "\">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, 'cross'), "vial", array(), "any", false), "html");
        echo "</a>)
    </h1>
    <p>
        Virgin: ";
        // line 9
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, 'cross'), "virginName", array(), "any", false), "html");
        echo "
        (<a href=\"";
        // line 10
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flyvial_show", array("id" => $this->getAttribute($this->getAttribute($this->getContext($context, 'cross'), "virgin", array(), "any", false), "id", array(), "any", false))), "html");
        echo "\">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, 'cross'), "virgin", array(), "any", false), "html");
        echo "</a>)
    </p>
    <p>
        Male: ";
        // line 13
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, 'cross'), "maleName", array(), "any", false), "html");
        echo "
        (<a href=\"";
        // line 14
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flyvial_show", array("id" => $this->getAttribute($this->getAttribute($this->getContext($context, 'cross'), "male", array(), "any", false), "id", array(), "any", false))), "html");
        echo "\">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, 'cross'), "male", array(), "any", false), "html");
        echo "</a>)
    </p>
    <p>Setup date: ";
        // line 16
        echo twig_escape_filter($this->env, twig_date_format_filter($this->getAttribute($this->getAttribute($this->getContext($context, 'cross'), "vial", array(), "any", false), "setupDate", array(), "any", false), "d.m.Y"), "html");
        echo "</p>
    <p>Check date: ";
        // line 17
        echo twig_escape_filter($this->env, twig_date_format_filter($this->getAttribute($this->getAttribute($this->getContext($context, 'cross'), "vial", array(), "any", false), "flipDate", array(), "any", false), "d.m.Y"), "html");
        echo "</p>
    ";
        // line 18
        if ((twig_length_filter($this->env, $this->getAttribute($this->getContext($context, 'cross'), "stocks", array(), "any", false)) > 0)) {
            // line 19
            echo "        <p>The following stocks were established from this cross:</p>
        <ul>
        ";
            // line 21
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getContext($context, 'cross'), "stocks", array(), "any", false));
            foreach ($context['_seq'] as $context['_key'] => $context['stock']) {
                // line 22
                echo "            <li>
                <a href=\"";
                // line 23
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flystock_show", array("id" => $this->getAttribute($this->getContext($context, 'stock'), "id", array(), "any", false))), "html");
                echo "\">";
                echo twig_escape_filter($this->env, $this->getContext($context, 'stock'), "html");
                echo "</a>
            </li>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['stock'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 26
            echo "        </ul>
    ";
        }
        // line 28
        echo "    ";
        if ((twig_length_filter($this->env, $this->getAttribute($this->getContext($context, 'cross'), "crosses", array(), "any", false)) > 0)) {
            // line 29
            echo "        <p>The following crosses were established from this cross:</p>
        <ul>
        ";
            // line 31
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getContext($context, 'cross'), "crosses", array(), "any", false));
            foreach ($context['_seq'] as $context['_key'] => $context['cross']) {
                // line 32
                echo "            <li>
                <a href=\"";
                // line 33
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flycross_show", array("id" => $this->getAttribute($this->getContext($context, 'cross'), "id", array(), "any", false))), "html");
                echo "\">";
                echo twig_escape_filter($this->env, $this->getContext($context, 'cross'), "html");
                echo "</a>
            </li>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['cross'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 36
            echo "        </ul>
    ";
        }
        // line 38
        echo "    <p><a href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flycross_list"), "html");
        echo "\">List</a> |
       <a href=\"";
        // line 39
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flycross_create"), "html");
        echo "\">New</a> |
       <a href=\"";
        // line 40
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flycross_edit", array("id" => $this->getAttribute($this->getContext($context, 'cross'), "id", array(), "any", false))), "html");
        echo "\">Edit</a> |
       <a href=\"";
        // line 41
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flycross_delete", array("id" => $this->getAttribute($this->getContext($context, 'cross'), "id", array(), "any", false))), "html");
        echo "\">Delete</a></p>
";
    }

    public function getTemplateName()
    {
        return "MpiCbgFliesBundle:FlyCross:show.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
