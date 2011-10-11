<?php

/* TwigBundle:Exception:exception.json.twig */
class __TwigTemplate_f40e2ad49bcd462234ddf22ce4495cc2 extends Twig_Template
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
        return "TwigBundle:Exception:exception.json.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
