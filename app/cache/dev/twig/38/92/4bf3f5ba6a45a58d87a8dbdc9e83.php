<?php

/* FrameworkBundle:Exception:exception.rdf.twig */
class __TwigTemplate_38924bf3f5ba6a45a58d87a8dbdc9e83 extends Twig_Template
{
    protected function doDisplay(array $context, array $blocks = array())
    {
        $context = array_merge($this->env->getGlobals(), $context);

        // line 1
        $this->env->loadTemplate("FrameworkBundle:Exception:exception.xml.twig")->display(array_merge($context, array("exception" => $this->getContext($context, 'exception'))));
    }

    public function getTemplateName()
    {
        return "FrameworkBundle:Exception:exception.rdf.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
