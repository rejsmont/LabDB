<?php

/* MpiCbgFliesBundle:FlyVial:list.html.twig */
class __TwigTemplate_9c6aa979ead77bc7276adfd76c743e7f extends Twig_Template
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
        echo "<h1>";
        echo twig_escape_filter($this->env, $this->getContext($context, 'header'), "html");
        echo "</h1>
    
    <label>To select vials, scan barcodes here: </label>
    <input id=\"barcode\" type=\"text\" value=\"\" onchange=\"
               var parent = document.getElementById('vial_' + barcode.value);
               var checkbox = parent.childNodes[1].childNodes[0];
               checkbox.checked = ! checkbox.checked;
               barcode.value = '';
               \" />
    <br /><br />
    
    <form id=\"form\" action=\"";
        // line 15
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getContext($context, 'app'), "request", array(), "any", false), "requestUri", array(), "any", false), "html");
        echo "\" method=\"post\" ";
        echo $this->env->getExtension('form')->renderEnctype($this->getContext($context, 'form'));
        echo ">
        <input type=\"button\" 
               value=\"Generate labels\"
               onclick=\"collectionselector_action.value = 'label'; form.submit()\" />
        <input type=\"button\" 
               value=\"Flip vials\"
               onclick=\"collectionselector_action.value = 'flip'; form.submit()\" />
        <input type=\"button\" 
               value=\"Trash vials\"
               onclick=\"collectionselector_action.value = 'trash'; form.submit()\" />
        <br /><br />
        <table>
            <thead>
                <tr>
                    <th></th>
                    <th>ID</th>
                    <th>Stock</th>
                    <th>Cross</th>
                    <th>Setup date</th>
                    <th>Flip date</th>
                    <th>Flipped from</th>
                </tr>
            </thead>
            <tbody>
            ";
        // line 39
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getContext($context, 'form'), "items", array(), "any", false));
        foreach ($context['_seq'] as $context['key'] => $context['item']) {
            // line 40
            echo "                ";
            $context['vialSelector'] = $this->getAttribute($this->getAttribute($this->getContext($context, 'vials'), "items", array(), "any", false), $this->getContext($context, 'key'), array(), "array", false);
            // line 41
            echo "                ";
            $context['vial'] = $this->getAttribute($this->getContext($context, 'vialSelector'), "item", array(), "any", false);
            // line 42
            echo "                <tr id=\"vial_";
            echo twig_escape_filter($this->env, $this->getContext($context, 'vial'), "html");
            echo "\">
                    <td>";
            // line 43
            echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getContext($context, 'item'), "selected", array(), "any", false));
            echo "</td>
                    <td><a href=\"";
            // line 44
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flyvial_show", array("id" => $this->getAttribute($this->getContext($context, 'vial'), "id", array(), "any", false))), "html");
            echo "\">
                        ";
            // line 45
            echo twig_escape_filter($this->env, $this->getContext($context, 'vial'), "html");
            echo "</a></td>
                    <td>";
            // line 46
            if ((!twig_test_empty($this->getAttribute($this->getContext($context, 'vial'), "stock", array(), "any", false)))) {
                // line 47
                echo "                        <a href=\"";
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flystock_show", array("id" => $this->getAttribute($this->getAttribute($this->getContext($context, 'vial'), "stock", array(), "any", false), "id", array(), "any", false))), "html");
                echo "\">";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, 'vial'), "stock", array(), "any", false), "html");
                echo "</a>
                        ";
            }
            // line 48
            echo "</td>
                    <td>";
            // line 49
            if ((!twig_test_empty($this->getAttribute($this->getContext($context, 'vial'), "cross", array(), "any", false)))) {
                // line 50
                echo "                        <a href=\"";
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flycross_show", array("id" => $this->getAttribute($this->getAttribute($this->getContext($context, 'vial'), "cross", array(), "any", false), "id", array(), "any", false))), "html");
                echo "\">";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, 'vial'), "cross", array(), "any", false), "html");
                echo "</a>
                        ";
            }
            // line 51
            echo "</td>
                    <td>";
            // line 52
            echo twig_escape_filter($this->env, twig_date_format_filter($this->getAttribute($this->getContext($context, 'vial'), "setupDate", array(), "any", false), "d.m.Y"), "html");
            echo "</td>
                    <td>";
            // line 53
            echo twig_escape_filter($this->env, twig_date_format_filter($this->getAttribute($this->getContext($context, 'vial'), "flipDate", array(), "any", false), "d.m.Y"), "html");
            echo "</td>
                    <td>";
            // line 54
            if ((!twig_test_empty($this->getAttribute($this->getContext($context, 'vial'), "parent", array(), "any", false)))) {
                // line 55
                echo "                        <a href=\"";
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("flyvial_show", array("id" => $this->getAttribute($this->getAttribute($this->getContext($context, 'vial'), "parent", array(), "any", false), "id", array(), "any", false))), "html");
                echo "\">
                        ";
                // line 56
                echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, 'vial'), "parent", array(), "any", false), "html");
                echo "
                        </a>
                        ";
            }
            // line 58
            echo "</td>
                    <td>";
            // line 59
            if ($this->getAttribute($this->getContext($context, 'vial'), "trashed", array(), "any", false)) {
                // line 60
                echo "                        TRASHED
                        ";
            }
            // line 62
            echo "                    
                    </td>
                </tr>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['key'], $context['item'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 66
        echo "            </tbody>
        </table>
        ";
        // line 68
        echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getContext($context, 'form'), "action", array(), "any", false));
        echo "
        ";
        // line 69
        echo $this->env->getExtension('form')->renderRest($this->getContext($context, 'form'));
        echo "
        <br />
        <input type=\"button\" 
               value=\"Generate labels\"
               onclick=\"collectionselector_action.value = 'label'; form.submit()\" />
        <input type=\"button\" 
               value=\"Flip vials\"
               onclick=\"collectionselector_action.value = 'flip'; form.submit()\" />
        <input type=\"button\" 
               value=\"Trash vials\"
               onclick=\"collectionselector_action.value = 'trash'; form.submit()\" />
";
    }

    public function getTemplateName()
    {
        return "MpiCbgFliesBundle:FlyVial:list.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
