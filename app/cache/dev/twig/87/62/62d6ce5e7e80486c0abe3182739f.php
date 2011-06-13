<?php

/* MpiCbgFliesBundle:FlyStock:list.html.twig */
class __TwigTemplate_876262d6ce5e7e80486c0abe3182739f extends Twig_Template
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
        <h1>Stocks</h1>
        <table>
            <thead>
                <tr>
                    <th></th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Source cross</th>
                </tr>
            </thead>
            <tbody>
            ";
        // line 16
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getContext($context, 'form'), "items", array(), "any", false));
        foreach ($context['_seq'] as $context['key'] => $context['item']) {
            // line 17
            echo "                ";
            $context['stockSelector'] = $this->getAttribute($this->getAttribute($this->getContext($context, 'stocks'), "items", array(), "any", false), $this->getContext($context, 'key'), array(), "array", false);
            // line 18
            echo "                ";
            $context['stock'] = $this->getAttribute($this->getContext($context, 'stockSelector'), "item", array(), "any", false);
            // line 19
            echo "                <tr>
                    <td>";
            // line 20
            echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getContext($context, 'item'), "selected", array(), "any", false));
            echo "</td>
                    <td><a href=\"";
            // line 21
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flystock_show", array("id" => $this->getAttribute($this->getContext($context, 'stock'), "id", array(), "any", false))), "html");
            echo "\">
                        ";
            // line 22
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, 'stock'), "id", array(), "any", false), "html");
            echo "</a>
                    </td>
                    <td>";
            // line 24
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, 'stock'), "name", array(), "any", false), "html");
            echo "</td>
                    <td>";
            // line 25
            if ((!twig_test_empty($this->getAttribute($this->getContext($context, 'stock'), "sourceCross", array(), "any", false)))) {
                // line 26
                echo "                        <a href=\"";
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flycross_show", array("id" => $this->getAttribute($this->getAttribute($this->getContext($context, 'stock'), "sourceCross", array(), "any", false), "id", array(), "any", false))), "html");
                echo "\">
                            ";
                // line 27
                echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, 'stock'), "sourceCross", array(), "any", false), "html");
                echo "
                        </a>
                        ";
            }
            // line 30
            echo "                    </td>
                </tr>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['key'], $context['item'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 33
        echo "            </tbody>
        </table>
        ";
        // line 35
        echo $this->env->getExtension('form')->renderRest($this->getContext($context, 'form'));
        echo "
        <!-- <input type=\"submit\" /> -->
    </form>
    
";
    }

    public function getTemplateName()
    {
        return "MpiCbgFliesBundle:FlyStock:list.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
