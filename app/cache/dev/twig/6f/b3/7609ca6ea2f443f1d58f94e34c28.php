<?php

/* TwigBundle:Form:table_layout.html.twig */
class __TwigTemplate_6fb37609ca6ea2f443f1d58f94e34c28 extends Twig_Template
{
    protected $parent;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = array();
        $this->blocks = array(
            'field_row' => array($this, 'block_field_row'),
            'form_errors' => array($this, 'block_form_errors'),
            'hidden_row' => array($this, 'block_hidden_row'),
            'repeated_errors' => array($this, 'block_repeated_errors'),
            'form_widget' => array($this, 'block_form_widget'),
        );
    }

    public function getParent(array $context)
    {
        $parent = "TwigBundle:Form:div_layout.html.twig";
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
    public function block_field_row($context, array $blocks = array())
    {
        // line 4
        ob_start();
        // line 5
        echo "    <tr>
        <td>
            ";
        // line 7
        echo $this->env->getExtension('form')->renderLabel($this->getContext($context, 'form'));
        echo "
        </td>
        <td>
            ";
        // line 10
        echo $this->env->getExtension('form')->renderErrors($this->getContext($context, 'form'));
        echo "
            ";
        // line 11
        echo $this->env->getExtension('form')->renderWidget($this->getContext($context, 'form'));
        echo "
        </td>
    </tr>
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 17
    public function block_form_errors($context, array $blocks = array())
    {
        // line 18
        ob_start();
        // line 19
        echo "    ";
        if ((twig_length_filter($this->env, $this->getContext($context, 'errors')) > 0)) {
            // line 20
            echo "    <tr>
        <td colspan=\"2\">
            ";
            // line 22
            $this->displayBlock("field_errors", $context, $blocks);
            echo "
        </td>
    </tr>
    ";
        }
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 29
    public function block_hidden_row($context, array $blocks = array())
    {
        // line 30
        ob_start();
        // line 31
        echo "    <tr style=\"display: none\">
        <td colspan=\"2\">
            ";
        // line 33
        echo $this->env->getExtension('form')->renderWidget($this->getContext($context, 'form'));
        echo "
        </td>
    </tr>
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 39
    public function block_repeated_errors($context, array $blocks = array())
    {
        // line 40
        ob_start();
        // line 41
        echo "    ";
        $this->displayBlock("form_errors", $context, $blocks);
        echo "
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 45
    public function block_form_widget($context, array $blocks = array())
    {
        // line 46
        ob_start();
        // line 47
        echo "    <table ";
        $this->displayBlock("container_attributes", $context, $blocks);
        echo ">
        ";
        // line 48
        $this->displayBlock("field_rows", $context, $blocks);
        echo "
        ";
        // line 49
        echo $this->env->getExtension('form')->renderRest($this->getContext($context, 'form'));
        echo "
    </table>
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    public function getTemplateName()
    {
        return "TwigBundle:Form:table_layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
