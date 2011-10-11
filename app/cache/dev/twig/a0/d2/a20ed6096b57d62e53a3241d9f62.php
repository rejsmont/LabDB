<?php

/* FrameworkBundle:Exception:error.css.twig */
class __TwigTemplate_a0d2a20ed6096b57d62e53a3241d9f62 extends Twig_Template
{
    protected function doDisplay(array $context, array $blocks = array())
    {
        $context = array_merge($this->env->getGlobals(), $context);

        // line 1
        echo "/*
";
        // line 2
        echo twig_escape_filter($this->env, $this->getContext($context, 'status_code'), "html");
        echo " ";
        echo twig_escape_filter($this->env, $this->getContext($context, 'status_text'), "html");
        echo "

*/
";
    }

    public function getTemplateName()
    {
        return "FrameworkBundle:Exception:error.css.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
