<?php

/* FrameworkBundle:Exception:exception.css.twig */
class __TwigTemplate_78a39bbe98750780bcd35dabdfcdc6a5 extends Twig_Template
{
    protected function doDisplay(array $context, array $blocks = array())
    {
        $context = array_merge($this->env->getGlobals(), $context);

        // line 1
        echo "/*
";
        // line 2
        $this->env->loadTemplate("FrameworkBundle:Exception:exception.txt.twig")->display(array_merge($context, array("exception" => $this->getContext($context, 'exception'))));
        // line 3
        echo "*/
";
    }

    public function getTemplateName()
    {
        return "FrameworkBundle:Exception:exception.css.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}