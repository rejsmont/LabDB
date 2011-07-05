<?php

/* MpiCbgFliesBundle:FlyVial:verify.html.twig */
class __TwigTemplate_669a6deab3573a623fb258905a5443f7 extends Twig_Template
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
        echo "<h1>Verify</h1>
    
    <label>To select vials, scan barcodes here: </label>
    <input id=\"barcode\" type=\"text\" value=\"\" onchange=\"
               var parent = document.getElementById('vial_' + barcode.value);
               var checkbox = parent.childNodes[1].childNodes[0];
               checkbox.checked = ! checkbox.checked;
               barcode.value = '';
               \" />
    <br /><br />
    
    <table>
        <thead>
            <tr>
                <th colspan=\"2\">Parent vial</th>
                <th colspan=\"2\">Flipped vial</th>
            </tr>
        </thead>
        <tbody>
        ";
        // line 23
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getContext($context, 'vials'));
        foreach ($context['_seq'] as $context['_key'] => $context['vial']) {
            // line 24
            echo "            <tr id=\"vial\">
                <td>";
            // line 25
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, 'vial'), "parent", array(), "any", false), "html");
            echo "</td>
                <td>";
            // line 26
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getContext($context, 'vial'), "parent", array(), "any", false), "stock", array(), "any", false), "html");
            echo "</td>
                <td>";
            // line 27
            echo twig_escape_filter($this->env, $this->getContext($context, 'vial'), "html");
            echo "</td>
                <td>";
            // line 28
            echo twig_escape_filter($this->env, $this->getAttribute($this->getContext($context, 'vial'), "stock", array(), "any", false), "html");
            echo "</td>
            </tr>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['vial'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        // line 31
        echo "        </tbody>
    </table>
";
    }

    public function getTemplateName()
    {
        return "MpiCbgFliesBundle:FlyVial:verify.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
