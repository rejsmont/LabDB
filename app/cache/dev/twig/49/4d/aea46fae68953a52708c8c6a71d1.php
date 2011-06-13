<?php

/* MpiCbgFliesBundle:FlyCross:list.html.twig */
class __TwigTemplate_494daea46fae68953a52708c8c6a71d1 extends Twig_Template
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
        echo "    <form action=\"";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getContext($context, 'app'), "request", array(), "any", false), "requestUri", array(), "any", false), "html");
        echo "\" method=\"post\" ";
        echo $this->env->getExtension('form')->renderEnctype($this->getContext($context, 'form'));
        echo ">
        <h1>Crosses</h1>
        <table>
            <thead>
                <tr>
                    <th></th>
                    <th>ID</th>
                    <th>Bottle</th>
                    <th colspan=\"2\">Virgin</th>
                    <th colspan=\"2\">Male</th>
                    <th>Setup date</th>
                    <th>Check date</th>
                </tr>
            </thead>
            <tbody>
            ";
        // line 19
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getContext($context, 'form'), "items", array(), "any", false));
        foreach ($context['_seq'] as $context['key'] => $context['item']) {
            // line 20
            echo "                ";
            $context['crossSelector'] = $this->getAttribute($this->getAttribute($this->getContext($context, 'crosses'), "items", array(), "any", false), $this->getContext($context, 'key'), array(), "array", false);
            // line 21
            echo "                ";
            $context['cross'] = $this->getAttribute($this->getContext($context, 'crossSelector'), "item", array(), "any", false);
            // line 22
            echo "                <tr>
                    <td>";
            // line 23
            echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getContext($context, 'item'), "selected", array(), "any", false));
            echo "</td>
                    <td><a href=\"";
            // line 24
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flycross_show", array("id" => $this->getAttribute($this->getContext($context, 'cross'), "id", array(), "any", false))), "html");
            echo "\">
                        ";
            // line 25
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, 'cross'), "id", array(), "any", false), "html");
            echo "</a></td>
                    <td><a href=\"";
            // line 26
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flyvial_show", array("id" => $this->getAttribute($this->getAttribute($this->getContext($context, 'cross'), "vial", array(), "any", false), "id", array(), "any", false))), "html");
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, 'cross'), "vial", array(), "any", false), "html");
            echo "</a></td>
                    <td>";
            // line 27
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, 'cross'), "virginName", array(), "any", false), "html");
            echo "</td>
                    <td><a href=\"";
            // line 28
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flyvial_show", array("id" => $this->getAttribute($this->getAttribute($this->getContext($context, 'cross'), "virgin", array(), "any", false), "id", array(), "any", false))), "html");
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, 'cross'), "virgin", array(), "any", false), "html");
            echo "</a></td>
                    <td>";
            // line 29
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, 'cross'), "maleName", array(), "any", false), "html");
            echo "</td>
                    <td><a href=\"";
            // line 30
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flyvial_show", array("id" => $this->getAttribute($this->getAttribute($this->getContext($context, 'cross'), "male", array(), "any", false), "id", array(), "any", false))), "html");
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, 'cross'), "male", array(), "any", false), "html");
            echo "</a></td>
                    <td>";
            // line 31
            echo twig_escape_filter($this->env, twig_date_format_filter($this->getAttribute($this->getAttribute($this->getContext($context, 'cross'), "vial", array(), "any", false), "setupDate", array(), "any", false), "d.m.Y"), "html");
            echo "</td>
                    <td>";
            // line 32
            echo twig_escape_filter($this->env, twig_date_format_filter($this->getAttribute($this->getAttribute($this->getContext($context, 'cross'), "vial", array(), "any", false), "flipDate", array(), "any", false), "d.m.Y"), "html");
            echo "</td>
                </tr>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['key'], $context['item'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 35
        echo "            </tbody>
        </table>
    </form>
";
    }

    public function getTemplateName()
    {
        return "MpiCbgFliesBundle:FlyCross:list.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
