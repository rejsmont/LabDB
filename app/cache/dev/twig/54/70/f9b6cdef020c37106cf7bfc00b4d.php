<?php

/* FrameworkBundle:Exception:exception.json.twig */
class __TwigTemplate_5470f9b6cdef020c37106cf7bfc00b4d extends Twig_Template
{
    protected function doDisplay(array $context, array $blocks = array())
    {
        $context = array_merge($this->env->getGlobals(), $context);

        // line 1
        echo twig_jsonencode_filter($this->getAttribute($this->getContext($context, 'exception'), "toarray", array(), "any", false));
        echo "
";
    }

    public function getTemplateName()
    {
        return "FrameworkBundle:Exception:exception.json.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
